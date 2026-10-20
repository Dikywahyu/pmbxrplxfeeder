<?php

namespace App\Http\Controllers;

use App\Models\Pmb\Candidate;
use App\Models\Pmb\Exam;
use App\Models\Pmb\ExamAnswer;
use App\Models\Pmb\ExamAttempt;
use App\Models\Pmb\ExamQuestion;
use App\Models\Pmb\Registration;
use App\Models\Pmb\Path as PmbPath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CbtController extends Controller
{
    public function index()
    {
        $registrations = Candidate::where('user_id', auth()->id())->first()?->registrations()->with('period')->latest()->get() ?? collect();
        $exams = Exam::where('is_published', true)
            ->whereHas('paths', fn ($query) => $query->whereIn('pmb_paths.id', $registrations->pluck('path_id')))
            ->with(['paths', 'questions'])->withCount('questions')->latest()->get();
        $attempts = ExamAttempt::whereIn('registration_id', $registrations->pluck('id'))->get()->keyBy('exam_id');

        return view('pmb.exams.index', compact('exams', 'registrations', 'attempts'));
    }

    public function start(Request $request, int $examId, int $registrationId)
    {
        $exam = Exam::whereKey($examId)->where('is_published', true)->firstOrFail();
        $registration = Registration::whereKey($registrationId)
            ->whereHas('candidate', fn ($query) => $query->where('user_id', auth()->id()))->firstOrFail();
        abort_unless($exam->paths()->whereKey($registration->path_id)->exists(), 403, 'Ujian ini tidak tersedia untuk jalur pendaftaran Anda.');
        abort_if($exam->starts_at && now()->lt($exam->starts_at), 403, 'Ujian belum dimulai.');
        abort_if($exam->ends_at && now()->gt($exam->ends_at), 403, 'Waktu ujian sudah berakhir.');

        $attempt = ExamAttempt::firstOrCreate(
            ['exam_id' => $exam->id, 'registration_id' => $registration->id],
            ['started_at' => now(), 'status' => 'in_progress']
        );
        abort_if($attempt->status === 'submitted', 403, 'Ujian ini sudah dikumpulkan.');

        return redirect()->route('pmb.exams.take', $attempt->id);
    }

    public function take(int $attemptId)
    {
        $attempt = ExamAttempt::with(['exam.questions', 'answers'])
            ->whereKey($attemptId)
            ->whereHas('registration.candidate', fn ($query) => $query->where('user_id', auth()->id()))
            ->firstOrFail();
        abort_if($attempt->status !== 'in_progress', 403, 'Ujian ini sudah dikumpulkan.');

        $deadline = $attempt->started_at->copy()->addMinutes($attempt->exam->duration_minutes);
        if ($attempt->exam->ends_at && $attempt->exam->ends_at->lt($deadline)) $deadline = $attempt->exam->ends_at;
        abort_if(now()->gte($deadline), 403, 'Waktu ujian sudah habis. Silakan hubungi panitia.');

        return view('pmb.exams.take', compact('attempt', 'deadline'));
    }

    public function submit(Request $request, int $attemptId)
    {
        $attempt = ExamAttempt::with(['exam.questions', 'answers'])
            ->whereKey($attemptId)
            ->whereHas('registration.candidate', fn ($query) => $query->where('user_id', auth()->id()))
            ->firstOrFail();
        abort_if($attempt->status !== 'in_progress', 403, 'Ujian ini sudah dikumpulkan.');

        $deadline = $attempt->started_at->copy()->addMinutes($attempt->exam->duration_minutes);
        if ($attempt->exam->ends_at && $attempt->exam->ends_at->lt($deadline)) $deadline = $attempt->exam->ends_at;
        abort_if(now()->gt($deadline), 403, 'Waktu ujian sudah habis.');
        $data = $request->validate([
            'answers' => ['nullable', 'array'],
            'answers.*.selected_option' => ['nullable', 'string', 'max:10'],
            'answers.*.answer_text' => ['nullable', 'string', 'max:20000'],
            'answers.*.answer_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
        $answers = $data['answers'] ?? [];
        $score = 0;

        DB::transaction(function () use ($attempt, $answers, &$score) {
            foreach ($attempt->exam->questions as $question) {
                $answerData = $answers[$question->id] ?? [];
                if ($question->type === 'multiple_choice') {
                    $selected = $answerData['selected_option'] ?? null;
                    if ($selected !== null && ! array_key_exists($selected, $question->options ?? [])) {
                        abort(422, 'Pilihan jawaban tidak valid.');
                    }
                    $earned = $selected !== null && $selected === $question->correct_option ? $question->points : 0;
                    ExamAnswer::updateOrCreate(
                        ['attempt_id' => $attempt->id, 'question_id' => $question->id],
                        ['selected_option' => $selected, 'score' => $earned]
                    );
                    $score += $earned;
                    continue;
                }

                $text = $answerData['answer_text'] ?? null;
                $image = $answerData['answer_image'] ?? null;
                if ($text !== null && mb_strlen($text) > 20000) abort(422, 'Jawaban esai terlalu panjang.');
                if ($image && (! $image->isValid() || ! in_array(strtolower($image->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'webp'], true))) {
                    abort(422, 'Lampiran jawaban harus berupa gambar JPG, PNG, atau WEBP.');
                }
                if ($image && $image->getSize() > 5 * 1024 * 1024) abort(422, 'Ukuran gambar jawaban maksimal 5 MB.');
                $existing = ExamAnswer::where('attempt_id', $attempt->id)->where('question_id', $question->id)->first();
                $path = $image ? $image->store('pmb/exams/answers', 'public') : $existing?->answer_image_path;
                ExamAnswer::updateOrCreate(
                    ['attempt_id' => $attempt->id, 'question_id' => $question->id],
                    ['answer_text' => $text, 'answer_image_path' => $path]
                );
            }
            $attempt->update(['status' => 'submitted', 'submitted_at' => now(), 'score' => $score]);
        });

        return redirect()->route('pmb.exams.result', $attempt->id)->with('success', 'Jawaban ujian berhasil dikumpulkan.');
    }

    public function result(int $attemptId)
    {
        $attempt = ExamAttempt::with(['exam', 'answers.question'])
            ->whereKey($attemptId)
            ->whereHas('registration.candidate', fn ($query) => $query->where('user_id', auth()->id()))
            ->firstOrFail();
        return view('pmb.exams.result', compact('attempt'));
    }

    public function adminIndex()
    {
        $exams = Exam::with(['paths'])->withCount(['questions', 'attempts'])->latest()->get();
        $paths = PmbPath::orderBy('nama_jalur')->get();
        return view('admin.cbt.index', compact('exams', 'paths'));
    }

    public function storeExam(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'], 'description' => ['nullable', 'string', 'max:5000'],
            'starts_at' => ['nullable', 'date'], 'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:600'],
            'path_ids' => ['required', 'array', 'min:1'], 'path_ids.*' => ['required', 'integer', 'distinct', 'exists:pmb_paths,id'],
        ]);
        $exam = Exam::create([...collect($data)->except('path_ids')->all(), 'created_by' => auth()->id(), 'is_published' => false]);
        $exam->paths()->sync($data['path_ids']);
        return back()->with('success', 'Ujian berhasil dibuat. Tambahkan soal sebelum menerbitkan ujian.');
    }

    public function updateExamPaths(Request $request, int $examId)
    {
        $data = $request->validate([
            'path_ids' => ['required', 'array', 'min:1'],
            'path_ids.*' => ['required', 'integer', 'distinct', 'exists:pmb_paths,id'],
        ]);
        Exam::findOrFail($examId)->paths()->sync($data['path_ids']);
        return back()->with('success', 'Jalur ujian berhasil diperbarui.');
    }

    public function updateExam(Request $request, int $examId)
    {
        $exam = Exam::findOrFail($examId);
        $data = $request->validate(['is_published' => ['required', 'boolean']]);
        if ($data['is_published'] && ! $exam->questions()->exists()) {
            return back()->withErrors(['exam' => 'Tambahkan minimal satu soal sebelum menerbitkan ujian.']);
        }
        $exam->update($data);
        return back()->with('success', 'Status ujian diperbarui.');
    }

    public function destroyExam(int $examId)
    {
        $exam = Exam::with(['questions', 'attempts.answers'])->findOrFail($examId);
        $paths = [];
        foreach ($exam->questions as $question) {
            $paths[] = $question->question_image_path;
            $paths = [...$paths, ...collect($question->options ?? [])->pluck('image')->all()];
        }
        foreach ($exam->attempts as $attempt) {
            $paths = [...$paths, ...$attempt->answers->pluck('answer_image_path')->all()];
        }
        Storage::disk('public')->delete(array_filter($paths));
        $exam->delete();
        return back()->with('success', 'Ujian berhasil dihapus.');
    }

    public function questions(int $examId)
    {
        $exam = Exam::with('questions')->findOrFail($examId);
        return view('admin.cbt.questions', compact('exam'));
    }

    public function storeQuestion(Request $request, int $examId)
    {
        $exam = Exam::findOrFail($examId);
        abort_if($exam->is_published, 403, 'Batalkan publikasi ujian sebelum mengubah soal.');
        abort_if($exam->attempts()->exists(), 403, 'Soal terkunci karena ujian sudah pernah dikerjakan peserta.');
        $data = $request->validate([
            'type' => ['required', Rule::in(['multiple_choice', 'essay'])],
            'question_text' => ['required', 'string', 'max:20000'],
            'question_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'points' => ['required', 'integer', 'min:1', 'max:1000'],
            'option_text' => ['nullable', 'array'], 'option_text.*' => ['nullable', 'string', 'max:2000'],
            'option_image' => ['nullable', 'array'], 'option_image.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'correct_option' => ['nullable', 'string', 'max:10'],
        ]);

        $options = [];
        if ($data['type'] === 'multiple_choice') {
            foreach (['A', 'B', 'C', 'D', 'E'] as $key) {
                $text = trim((string) ($data['option_text'][$key] ?? ''));
                $image = $request->file("option_image.$key");
                if ($text === '' && ! $image) continue;
                $options[$key] = [
                    'text' => $text,
                    'image' => $image?->store('pmb/exams/options', 'public'),
                ];
            }
            if (count($options) < 2 || ! isset($options[$data['correct_option'] ?? ''])) {
                return back()->withInput()->withErrors(['options' => 'Isi minimal dua opsi dan pilih jawaban yang benar.']);
            }
        }

        ExamQuestion::create([
            'exam_id' => $exam->id, 'type' => $data['type'], 'question_text' => $data['question_text'],
            'question_image_path' => $request->file('question_image')?->store('pmb/exams/questions', 'public'),
            'options' => $options ?: null, 'correct_option' => $data['type'] === 'multiple_choice' ? $data['correct_option'] : null,
            'points' => $data['points'], 'position' => (int) $exam->questions()->max('position') + 1,
        ]);
        return back()->with('success', 'Soal berhasil ditambahkan.');
    }

    public function destroyQuestion(int $questionId)
    {
        $question = ExamQuestion::findOrFail($questionId);
        abort_if($question->exam->is_published, 403, 'Batalkan publikasi ujian sebelum mengubah soal.');
        abort_if($question->exam->attempts()->exists(), 403, 'Soal terkunci karena ujian sudah pernah dikerjakan peserta.');
        $paths = [$question->question_image_path, ...collect($question->options ?? [])->pluck('image')->all()];
        Storage::disk('public')->delete(array_filter($paths));
        $question->delete();
        return back()->with('success', 'Soal berhasil dihapus.');
    }

    public function attempts(int $examId)
    {
        $exam = Exam::with(['attempts.registration.candidate', 'attempts.answers.question'])->findOrFail($examId);
        return view('admin.cbt.attempts', compact('exam'));
    }

    public function gradeAnswer(Request $request, int $answerId)
    {
        $data = $request->validate(['score' => ['required', 'numeric', 'min:0'], 'grader_note' => ['nullable', 'string', 'max:2000']]);
        $answer = ExamAnswer::with(['question', 'attempt'])->findOrFail($answerId);
        abort_if($answer->question->type !== 'essay' || $data['score'] > $answer->question->points, 422, 'Nilai tidak valid untuk jawaban ini.');
        $answer->update($data);
        $answer->attempt->update(['score' => $answer->attempt->answers()->whereNotNull('score')->sum('score')]);
        return back()->with('success', 'Nilai esai berhasil disimpan.');
    }
}
