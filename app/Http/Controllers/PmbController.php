<?php

namespace App\Http\Controllers;

use App\Models\Pmb\Candidate;
use App\Models\Pmb\CandidateDocument;
use App\Models\Pmb\DocumentRequirement;
use App\Models\Pmb\Fee;
use App\Models\Pmb\Payment;
use App\Models\Pmb\Path as PmbPath;
use App\Models\Pmb\Period;
use App\Models\Pmb\Registration;
use App\Models\Pmb\Referral;
use App\Models\Pmb\StudyProgram;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PmbController extends Controller
{
    public function index(Request $request)
    {
        $referralCode = $request->query('ref');
        if ($referralCode && Referral::where('kode_referral', $referralCode)->where('is_active', true)->exists()) {
            session(['pmb_referral_code' => $referralCode]);
        }
        $candidate = Candidate::where('user_id', auth()->id())->first();

        $periods = Period::query()
            ->where('is_active', true)
            ->whereDate('tanggal_mulai', '<=', today())
            ->whereDate('tanggal_selesai', '>=', today())
            ->orderBy('tanggal_mulai')
            ->get();
        $paths = PmbPath::orderBy('nama_jalur')->get();
        $programs = StudyProgram::where('is_active', true)->orderBy('nama_prodi')->get();
        $fees = Fee::with(['period', 'path', 'program'])
            ->get()
            ->keyBy(fn (Fee $fee) => "{$fee->period_id}:{$fee->path_id}:{$fee->program_id}");
        $admissionCards = $paths->map(function (PmbPath $path) use ($periods, $programs, $fees) {
            $options = collect();
            foreach ($periods as $period) {
                foreach ($programs as $program) {
                    $fee = $fees->get("{$period->id}:{$path->id}:{$program->id}");
                    if ($fee) {
                        $options->push([
                            'period_id' => $period->id,
                            'period_name' => $period->nama_periode,
                            'program_id' => $program->id,
                            'program_name' => $program->nama_prodi.' ('.$program->jenjang.')',
                            'amount' => (float) $fee->nominal_pendaftaran,
                        ]);
                    }
                }
            }
            return ['path' => $path, 'options' => $options];
        })->filter(fn ($card) => $card['options']->isNotEmpty())->values();

        $registrations = $candidate
            ? $candidate->registrations()
                ->with(['period', 'path.documentRequirements', 'program1', 'payments', 'documents.requirement'])
                ->latest()
                ->get()
            : collect();
        $profileFields = ['nama_mahasiswa', 'nik', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'handphone', 'jalan', 'kelurahan'];
        $profileComplete = $candidate && collect($profileFields)->every(fn ($field) => filled($candidate->{$field}));

        return view('pmb.index', compact(
            'candidate',
            'periods',
            'paths',
            'programs',
            'fees',
            'admissionCards',
            'registrations',
            'profileComplete'
        ));
    }

    public function saveCandidateProfile(Request $request)
    {
        $data = $request->validate([
            'nama_mahasiswa' => ['required', 'string', 'max:255'],
            'nik' => ['nullable', 'digits:16'],
            'nisn' => ['nullable', 'string', 'max:20'],
            'npwp' => ['nullable', 'string', 'max:25'],
            'jenis_kelamin' => ['nullable', Rule::in(['L', 'P'])],
            'tempat_lahir' => ['nullable', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date', 'before:today'],
            'id_agama' => ['nullable', 'string', 'max:5'],
            'handphone' => ['nullable', 'string', 'max:20'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'jalan' => ['nullable', 'string', 'max:255'],
            'dusun' => ['nullable', 'string', 'max:255'],
            'rt' => ['nullable', 'string', 'max:5'],
            'rw' => ['nullable', 'string', 'max:5'],
            'kelurahan' => ['nullable', 'string', 'max:255'],
            'kode_pos' => ['nullable', 'string', 'max:10'],
            'id_wilayah' => ['nullable', 'string', 'max:10'],
            'id_negara' => ['nullable', 'string', 'max:5'],
            'nomor_daftar_kip' => ['nullable', 'string', 'max:90'],
            'nomor_kip' => ['nullable', 'string', 'max:50'],
            'nomor_kks' => ['nullable', 'string', 'max:50'],
            'nomor_kps' => ['nullable', 'string', 'max:50'],
            'penerima_kps' => ['nullable', 'boolean'],
            'terdaftar_dtks' => ['nullable', 'boolean'],
            'nama_ayah' => ['nullable', 'string', 'max:255'],
            'nik_ayah' => ['nullable', 'string', 'max:20'],
            'tanggal_lahir_ayah' => ['nullable', 'date', 'before:today'],
            'id_pendidikan_ayah' => ['nullable', 'string', 'max:5'],
            'nama_pendidikan_ayah' => ['nullable', 'string', 'max:255'],
            'id_pekerjaan_ayah' => ['nullable', 'string', 'max:5'],
            'nama_pekerjaan_ayah' => ['nullable', 'string', 'max:255'],
            'id_penghasilan_ayah' => ['nullable', 'string', 'max:5'],
            'nama_penghasilan_ayah' => ['nullable', 'string', 'max:255'],
            'nama_ibu_kandung' => ['nullable', 'string', 'max:255'],
            'nik_ibu' => ['nullable', 'string', 'max:20'],
            'tanggal_lahir_ibu' => ['nullable', 'date', 'before:today'],
            'id_pendidikan_ibu' => ['nullable', 'string', 'max:5'],
            'nama_pendidikan_ibu' => ['nullable', 'string', 'max:255'],
            'id_pekerjaan_ibu' => ['nullable', 'string', 'max:5'],
            'nama_pekerjaan_ibu' => ['nullable', 'string', 'max:255'],
            'id_penghasilan_ibu' => ['nullable', 'string', 'max:5'],
            'nama_penghasilan_ibu' => ['nullable', 'string', 'max:255'],
            'nama_wali' => ['nullable', 'string', 'max:255'],
            'tanggal_lahir_wali' => ['nullable', 'date', 'before:today'],
            'id_pendidikan_wali' => ['nullable', 'string', 'max:5'],
            'nama_pendidikan_wali' => ['nullable', 'string', 'max:255'],
            'id_pekerjaan_wali' => ['nullable', 'string', 'max:5'],
            'nama_pekerjaan_wali' => ['nullable', 'string', 'max:255'],
            'id_penghasilan_wali' => ['nullable', 'string', 'max:5'],
            'nama_penghasilan_wali' => ['nullable', 'string', 'max:255'],
        ], ['nik.digits' => 'NIK harus terdiri dari 16 angka.']);

        $data['penerima_kps'] = $request->boolean('penerima_kps');
        $data['terdaftar_dtks'] = $request->boolean('terdaftar_dtks');

        Candidate::updateOrCreate(
            ['user_id' => $request->user()->id],
            array_merge($data, ['email' => $request->user()->email])
        );

        return redirect()->route('pmb.index')->with('success', 'Data diri berhasil disimpan. Anda dapat melanjutkan ke tahap pendaftaran.');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'period_id' => ['required', Rule::exists('pmb_periods', 'id')->where('is_active', true)],
            'path_id' => ['required', 'exists:pmb_paths,id'],
            'prodi_id' => [
                'required',
                Rule::exists('pmb_study_programs', 'id')->where('is_active', true),
            ],
            'referral_code' => ['nullable', 'string', Rule::exists('pmb_referrals', 'kode_referral')->where('is_active', true)],
        ], [
            'period_id.exists' => 'Periode pendaftaran tidak aktif atau sudah di luar jadwal.',
        ]);

        $candidate = Candidate::where('user_id', $request->user()->id)->first();
        $profileFields = ['nama_mahasiswa', 'nik', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'handphone', 'jalan', 'kelurahan'];
        if (! $candidate || ! collect($profileFields)->every(fn ($field) => filled($candidate->{$field}))) {
            return back()->withErrors(['profile' => 'Lengkapi dan simpan data diri terlebih dahulu sebelum memilih jalur pendaftaran.']);
        }

        $period = Period::find($data['period_id']);
        if (
            ! $period
            || ! $period->is_active
            || $period->tanggal_mulai->toDateString() > today()->toDateString()
            || $period->tanggal_selesai->toDateString() < today()->toDateString()
        ) {
            return back()->withInput()->withErrors([
                'period_id' => 'Periode pendaftaran tidak aktif atau sudah di luar jadwal.',
            ]);
        }

        $program = StudyProgram::whereKey($data['prodi_id'])->where('is_active', true)->exists();
        if (! $program) {
            return back()->withInput()->withErrors([
                'prodi_id' => 'Program studi yang dipilih sedang tidak aktif.',
            ]);
        }

        $fee = Fee::where('period_id', $data['period_id'])
            ->where('path_id', $data['path_id'])
            ->where('program_id', $data['prodi_id'])
            ->first();

        if (! $fee) {
            return back()->withInput()->withErrors([
                'prodi_id' => 'Biaya untuk periode, jalur, dan program studi yang dipilih belum tersedia.',
            ]);
        }

        $existingRegistration = $candidate->registrations()
            ->with('payments')
            ->where('period_id', $data['period_id'])
            ->first();
        $changingSelection = $existingRegistration && (
            (string) $existingRegistration->path_id !== (string) $data['path_id']
            || (string) $existingRegistration->prodi_id_1 !== (string) $data['prodi_id']
        );
        $hadExistingRegistration = (bool) $existingRegistration;
        if ($changingSelection && (
            $existingRegistration->status !== 'draft'
            || $existingRegistration->payments->contains(fn (Payment $payment) => $payment->status !== 'pending' || filled($payment->file_bukti_bayar))
        )) {
            return back()->withErrors([
                'path_id' => 'Pilihan jalur atau program tidak dapat diubah karena pendaftaran atau pembayarannya sudah diproses.',
            ]);
        }

        $registration = DB::transaction(function () use ($data, $fee, $candidate, $existingRegistration, $changingSelection) {
            if ($existingRegistration) {
                if ($changingSelection) {
                    $existingRegistration->update([
                        'path_id' => $data['path_id'],
                        'prodi_id_1' => $data['prodi_id'],
                    ]);
                    $existingRegistration->payments()->updateOrCreate(
                        ['jenis_tagihan' => 'pendaftaran'],
                        [
                            'nominal_tagihan' => $fee->nominal_pendaftaran,
                            'status' => (float) $fee->nominal_pendaftaran === 0.0 ? 'lunas' : 'pending',
                            'file_bukti_bayar' => null,
                            'verified_by' => null,
                        ]
                    );
                }
                return $existingRegistration;
            }

            $existing = $candidate->registrations()
                ->where('period_id', $data['period_id'])
                ->first();

            if ($existing) {
                return $existing;
            }

            $registration = $candidate->registrations()->create([
                'period_id' => $data['period_id'],
                'input_by' => auth()->id(),
                'path_id' => $data['path_id'],
                'prodi_id_1' => $data['prodi_id'],
                'nomor_pendaftaran' => 'PMB-'.now()->format('Y').'-'.Str::upper(Str::random(8)),
                'status' => 'draft',
                'referral_id' => ! empty($data['referral_code'])
                    ? Referral::where('kode_referral', $data['referral_code'])->where('is_active', true)->value('id')
                    : null,
                'referral_by' => ! empty($data['referral_code'])
                    ? Referral::where('kode_referral', $data['referral_code'])->where('is_active', true)->value('owner_id')
                    : null,
            ]);

            $registration->payments()->create([
                'jenis_tagihan' => 'pendaftaran',
                'nominal_tagihan' => $fee->nominal_pendaftaran,
                'status' => (float) $fee->nominal_pendaftaran === 0.0 ? 'lunas' : 'pending',
            ]);

            return $registration;
        });

        session()->forget('pmb_referral_code');

        return redirect()
            ->route('pmb.index')
            ->with('success', $changingSelection
                ? "Pilihan jalur dan program pada pendaftaran {$registration->nomor_pendaftaran} berhasil diperbarui."
                : ($hadExistingRegistration
                    ? "Pendaftaran {$registration->nomor_pendaftaran} sudah tercatat."
                    : "Pendaftaran {$registration->nomor_pendaftaran} berhasil dibuat."));
    }

    public function uploadPaymentProof(Request $request, int $registrationId, int $paymentId)
    {
        $data = $request->validate([
            'bukti_bayar' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $registration = Registration::whereKey($registrationId)
            ->whereHas('candidate', fn ($query) => $query->where('user_id', auth()->id()))
            ->firstOrFail();
        $payment = $registration->payments()->findOrFail($paymentId);

        if (! in_array($payment->status, ['pending', 'ditolak'], true)) {
            return back()->withErrors([
                'bukti_bayar' => 'Bukti hanya dapat diunggah untuk tagihan yang belum lunas atau ditolak.',
            ]);
        }

        $path = $data['bukti_bayar']->store('pmb/payment-proofs', 'local');
        $oldPath = $payment->file_bukti_bayar;

        DB::transaction(function () use ($payment, $path, $registration) {
            $payment->update([
                'file_bukti_bayar' => $path,
                'status' => 'menunggu_verifikasi',
                'verified_by' => null,
            ]);

            $registration->update(['status' => 'berkas_masuk']);
        });

        if ($oldPath) {
            Storage::disk('local')->delete($oldPath);
        }

        return redirect()->route('pmb.index')->with('success', 'Bukti pembayaran berhasil dikirim untuk diverifikasi.');
    }

    public function adminIndex()
    {
        abort_unless(
            auth()->user()->can('pmb-access')
                || auth()->user()->can('pmb-manage')
                || auth()->user()->can('pmb-review')
                || auth()->user()->can('pmb-payment-review'),
            403
        );

        return view('admin.pmb.index', ['section' => 'overview']);
    }

    public function adminSection(Request $request, string $section)
    {
        $allowed = ['registrations', 'periods', 'paths', 'programs', 'fees', 'referrals', 'requirements'];
        abort_unless(in_array($section, $allowed, true), 404);

        $user = $request->user();
        $canViewRegistrations = $user->can('pmb-access') || $user->can('pmb-manage')
            || $user->can('pmb-review') || $user->can('pmb-payment-review');
        abort_unless(
            $section === 'registrations' ? $canViewRegistrations : ($section === 'overview' || $user->can('pmb-manage')),
            403
        );

        return view('admin.pmb.index', compact('section'));
    }

    public function adminData(Request $request, string $section)
    {
        $user = $request->user();
        $canManage = $user->can('pmb-manage');
        $canReview = $user->can('pmb-review');
        $canReviewPayment = $user->can('pmb-payment-review');
        $canViewRegistrations = $user->can('pmb-access') || $canManage || $canReview || $canReviewPayment;

        if ($section === 'overview') {
            abort_unless($canViewRegistrations, 403);

            $registrationCounts = Registration::query()
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status');
            $paymentCounts = Payment::query()
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status');

            return response()->json([
                'html' => view('admin.pmb.sections.overview', compact('registrationCounts', 'paymentCounts'))->render(),
                'chart' => [
                    'labels' => ['Draft', 'Berkas masuk', 'Review', 'Sudah ujian', 'Lulus', 'Diterima', 'Ditolak', 'Mundur'],
                    'series' => [
                        (int) ($registrationCounts['draft'] ?? 0),
                        (int) ($registrationCounts['berkas_masuk'] ?? 0),
                        (int) ($registrationCounts['review'] ?? 0),
                        (int) ($registrationCounts['sudah_ujian'] ?? 0),
                        (int) ($registrationCounts['lulus'] ?? 0),
                        (int) ($registrationCounts['diterima'] ?? 0),
                        (int) ($registrationCounts['ditolak'] ?? 0),
                        (int) ($registrationCounts['mundur'] ?? 0),
                    ],
                ],
            ]);
        }

        if ($section === 'registrations') {
            abort_unless($canViewRegistrations, 403);
            $registrations = Registration::with([
                'candidate.user', 'period', 'path', 'program1', 'payments', 'documents.requirement',
            ])->latest()->get();
            return response()->json([
                'html' => view('admin.pmb.sections.registrations', compact('registrations'))->render(),
            ]);
        }

        abort_unless($canManage, 403);

        $html = match ($section) {
            'periods' => view('admin.pmb.sections.periods', [
                'periods' => Period::orderByDesc('tanggal_mulai')->get(),
            ])->render(),
            'paths' => view('admin.pmb.sections.paths', [
                'paths' => PmbPath::orderBy('nama_jalur')->get(),
            ])->render(),
            'programs' => view('admin.pmb.sections.programs', [
                'programs' => StudyProgram::orderBy('nama_prodi')->get(),
            ])->render(),
            'fees' => (function () {
                $periods = Period::orderByDesc('tanggal_mulai')->get();
                $paths = PmbPath::orderBy('nama_jalur')->get();
                $programs = StudyProgram::orderBy('nama_prodi')->get();
                $fees = Fee::with(['period', 'path', 'program'])
                    ->orderBy('period_id')->orderBy('path_id')->get();

                return view('admin.pmb.sections.fees', compact('periods', 'paths', 'programs', 'fees'))->render();
            })(),
            'referrals' => (function () {
                $referrals = Referral::with('owner')->withCount('registrations')->latest()->get();
                $users = User::orderBy('name')->get(['id', 'name', 'email']);
                return view('admin.pmb.sections.referrals', compact('referrals', 'users'))->render();
            })(),
            'requirements' => (function () {
                $paths = PmbPath::orderBy('nama_jalur')->get();
                $requirements = DocumentRequirement::with('path')->orderBy('path_id')->orderBy('nama_dokumen')->get();
                return view('admin.pmb.sections.requirements', compact('paths', 'requirements'))->render();
            })(),
            default => abort(404),
        };

        return response()->json(['html' => $html]);
    }

    public function storePeriod(Request $request)
    {
        $data = $request->validate([
            'nama_periode' => ['required', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($data) {
            if (! empty($data['is_active'])) {
                Period::query()->update(['is_active' => false]);
            }

            Period::create([
                'nama_periode' => $data['nama_periode'],
                'tanggal_mulai' => $data['tanggal_mulai'],
                'tanggal_selesai' => $data['tanggal_selesai'],
                'is_active' => ! empty($data['is_active']),
            ]);
        });

        return $this->mutationResponse($request, 'Periode PMB berhasil ditambahkan.');
    }

    public function updatePeriod(Request $request, int $periodId)
    {
        $data = $request->validate([
            'nama_periode' => ['required', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'is_active' => ['required', 'boolean'],
        ]);
        $period = Period::findOrFail($periodId);

        DB::transaction(function () use ($period, $data) {
            if ($data['is_active']) Period::where('id', '!=', $period->id)->update(['is_active' => false]);
            $period->update($data);
        });

        return $this->mutationResponse($request, 'Periode PMB berhasil diperbarui.');
    }

    public function destroyPeriod(Request $request, int $periodId)
    {
        $period = Period::findOrFail($periodId);
        if ($period->registrations()->exists() || $period->fees()->exists()) {
            return $this->deleteBlocked($request, 'Periode sudah digunakan oleh pendaftar atau pengaturan biaya.');
        }
        $period->delete();

        return $this->mutationResponse($request, 'Periode PMB berhasil dihapus.');
    }

    public function storePath(Request $request)
    {
        $data = $request->validate([
            'nama_jalur' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:5000'],
        ]);
        PmbPath::create($data);

        return $this->mutationResponse($request, 'Jalur pendaftaran berhasil ditambahkan.');
    }

    public function updatePath(Request $request, int $pathId)
    {
        $data = $request->validate([
            'nama_jalur' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:5000'],
        ]);
        PmbPath::findOrFail($pathId)->update($data);

        return $this->mutationResponse($request, 'Jalur pendaftaran berhasil diperbarui.');
    }

    public function destroyPath(Request $request, int $pathId)
    {
        $path = PmbPath::findOrFail($pathId);
        if ($path->registrations()->exists() || $path->fees()->exists() || $path->documentRequirements()->exists()) {
            return $this->deleteBlocked($request, 'Jalur sudah dipakai pendaftar, biaya, atau persyaratan dokumen.');
        }
        $path->delete();

        return $this->mutationResponse($request, 'Jalur pendaftaran berhasil dihapus.');
    }

    public function storeDocumentRequirement(Request $request)
    {
        $data = $this->validatedDocumentRequirement($request);
        DocumentRequirement::create($data);
        return $this->mutationResponse($request, 'Persyaratan berkas berhasil ditambahkan.');
    }

    public function updateDocumentRequirement(Request $request, int $requirementId)
    {
        $data = $this->validatedDocumentRequirement($request);
        DocumentRequirement::findOrFail($requirementId)->update($data);
        return $this->mutationResponse($request, 'Persyaratan berkas berhasil diperbarui.');
    }

    public function destroyDocumentRequirement(Request $request, int $requirementId)
    {
        $requirement = DocumentRequirement::findOrFail($requirementId);
        if ($requirement->candidateDocuments()->exists()) {
            return $this->deleteBlocked($request, 'Persyaratan ini sudah memiliki berkas pendaftar sehingga tidak dapat dihapus.');
        }
        $requirement->delete();
        return $this->mutationResponse($request, 'Persyaratan berkas berhasil dihapus.');
    }

    private function validatedDocumentRequirement(Request $request): array
    {
        $data = $request->validate([
            'path_id' => ['required', 'exists:pmb_paths,id'],
            'nama_dokumen' => ['required', 'string', 'max:255'],
            'is_required' => ['nullable', 'boolean'],
            'format_file' => ['required', 'string', 'max:100'],
        ]);
        $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'webp'];
        $formats = collect(explode(',', strtolower($data['format_file'])))
            ->map(fn ($format) => ltrim(trim($format), '.'))
            ->filter(fn ($format) => in_array($format, $allowed, true))->unique()->values();
        if ($formats->isEmpty()) {
            throw \Illuminate\Validation\ValidationException::withMessages(['format_file' => 'Pilih format PDF atau gambar yang didukung (JPG, PNG, WEBP).']);
        }
        return [
            'path_id' => $data['path_id'],
            'nama_dokumen' => $data['nama_dokumen'],
            'is_required' => $request->boolean('is_required'),
            'format_file' => $formats->implode(','),
        ];
    }

    public function uploadCandidateDocument(Request $request, int $registrationId, int $requirementId)
    {
        $registration = Registration::whereKey($registrationId)
            ->whereHas('candidate', fn ($query) => $query->where('user_id', auth()->id()))->firstOrFail();
        $requirement = DocumentRequirement::whereKey($requirementId)->where('path_id', $registration->path_id)->firstOrFail();
        $allowed = collect(explode(',', strtolower($requirement->format_file)))
            ->map(fn ($format) => ltrim(trim($format), '.'))
            ->filter(fn ($format) => in_array($format, ['pdf', 'jpg', 'jpeg', 'png', 'webp'], true))->values();
        abort_if($allowed->isEmpty(), 422, 'Format unggahan persyaratan belum dikonfigurasi.');
        $data = $request->validate(['file' => ['required', 'file', 'max:10240', 'mimes:'.$allowed->implode(',')]]);
        $path = $data['file']->store('pmb/candidate-documents', 'local');
        $document = CandidateDocument::firstOrNew([
            'registration_id' => $registration->id,
            'requirement_id' => $requirement->id,
        ]);
        $oldPath = $document->file_path;
        $document->fill(['file_path' => $path, 'status_verifikasi' => 'pending', 'catatan_admin' => null])->save();
        if ($oldPath) Storage::disk('local')->delete($oldPath);
        return redirect()->route('pmb.index')->with('success', 'Berkas berhasil diunggah.');
    }

    public function downloadCandidateDocument(int $documentId)
    {
        $document = CandidateDocument::with('registration.candidate')->findOrFail($documentId);
        $user = auth()->user();
        abort_unless(
            $document->registration?->candidate?->user_id === $user->id
                || $user->can('pmb-manage') || $user->can('pmb-review') || $user->can('pmb-access'),
            403
        );
        abort_unless(Storage::disk('local')->exists($document->file_path), 404);
        return Storage::disk('local')->download($document->file_path, basename($document->file_path));
    }

    public function previewCandidateDocument(int $documentId)
    {
        $document = CandidateDocument::with('registration.candidate')->findOrFail($documentId);
        $user = auth()->user();
        abort_unless(
            $document->registration?->candidate?->user_id === $user->id
                || $user->can('pmb-manage') || $user->can('pmb-review') || $user->can('pmb-access'),
            403
        );
        abort_unless(Storage::disk('local')->exists($document->file_path), 404);

        $mimeType = Storage::disk('local')->mimeType($document->file_path);
        abort_unless(in_array($mimeType, ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'], true), 415);

        return Storage::disk('local')->response(
            $document->file_path,
            basename($document->file_path),
            ['X-Content-Type-Options' => 'nosniff'],
            'inline'
        );
    }

    public function verifyCandidateDocument(Request $request, int $documentId)
    {
        $data = $request->validate([
            'status_verifikasi' => ['required', Rule::in(['valid', 'invalid'])],
            'catatan_admin' => ['nullable', 'string', 'max:2000'],
        ]);
        CandidateDocument::findOrFail($documentId)->update([
            'status_verifikasi' => $data['status_verifikasi'],
            'catatan_admin' => $data['status_verifikasi'] === 'invalid' ? ($data['catatan_admin'] ?? null) : null,
        ]);
        return $this->mutationResponse($request, 'Status berkas pendaftar berhasil diperbarui.');
    }

    public function updatePeriodStatus(Request $request, int $periodId)
    {
        $data = $request->validate(['is_active' => ['required', 'boolean']]);
        $period = Period::findOrFail($periodId);

        DB::transaction(function () use ($period, $data) {
            if ($data['is_active']) {
                Period::query()->update(['is_active' => false]);
            }

            $period->update(['is_active' => $data['is_active']]);
        });

        return $this->mutationResponse($request, 'Status periode berhasil diperbarui.');
    }

    public function storeProgram(Request $request)
    {
        $data = $request->validate([
            'id_dikti' => ['nullable', 'uuid', 'unique:pmb_study_programs,id_dikti'],
            'kode_prodi' => ['required', 'string', 'max:255', 'unique:pmb_study_programs,kode_prodi'],
            'nama_prodi' => ['required', 'string', 'max:255'],
            'jenjang' => ['required', 'string', 'max:50'],
        ]);

        StudyProgram::create([...$data, 'is_active' => true]);

        return $this->mutationResponse($request, 'Program studi berhasil ditambahkan.');
    }

    public function updateProgram(Request $request, int $programId)
    {
        $data = $request->validate([
            'id_dikti' => ['nullable', 'uuid', Rule::unique('pmb_study_programs', 'id_dikti')->ignore($programId)],
            'kode_prodi' => ['required', 'string', 'max:255', Rule::unique('pmb_study_programs', 'kode_prodi')->ignore($programId)],
            'nama_prodi' => ['required', 'string', 'max:255'],
            'jenjang' => ['required', 'string', 'max:50'],
            'is_active' => ['required', 'boolean'],
        ]);
        StudyProgram::findOrFail($programId)->update($data);

        return $this->mutationResponse($request, 'Program studi berhasil diperbarui.');
    }

    public function destroyProgram(Request $request, int $programId)
    {
        $program = StudyProgram::findOrFail($programId);
        if ($program->fees()->exists() || $program->firstChoiceRegistrations()->exists() || $program->secondChoiceRegistrations()->exists()) {
            return $this->deleteBlocked($request, 'Program studi sudah dipakai oleh biaya atau data pendaftar.');
        }
        $program->delete();

        return $this->mutationResponse($request, 'Program studi berhasil dihapus.');
    }

    public function storeFee(Request $request)
    {
        $data = $request->validate([
            'period_id' => ['required', 'exists:pmb_periods,id'],
            'path_id' => ['required', 'exists:pmb_paths,id'],
            'program_id' => ['required', 'exists:pmb_study_programs,id'],
            'nominal_pendaftaran' => ['required', 'numeric', 'min:0'],
            'nominal_spp' => ['required', 'numeric', 'min:0'],
        ]);

        Fee::updateOrCreate(
            [
                'period_id' => $data['period_id'],
                'path_id' => $data['path_id'],
                'program_id' => $data['program_id'],
            ],
            [
                'nominal_pendaftaran' => $data['nominal_pendaftaran'],
                'nominal_spp' => $data['nominal_spp'],
            ]
        );

        return $this->mutationResponse($request, 'Biaya pendaftaran berhasil disimpan.');
    }

    public function storeReferral(Request $request)
    {
        $data = $request->validate([
            'owner_id' => ['required', 'exists:users,id', Rule::unique('pmb_referrals', 'owner_id')],
        ]);
        do {
            $code = Str::upper(Str::random(10));
        } while (Referral::where('kode_referral', $code)->exists());

        Referral::create(['owner_id' => $data['owner_id'], 'kode_referral' => $code, 'is_active' => true]);

        return $this->mutationResponse($request, 'Kode referral marketing berhasil dibuat.');
    }

    public function updateReferralStatus(Request $request, int $referralId)
    {
        $data = $request->validate(['is_active' => ['required', 'boolean']]);
        Referral::findOrFail($referralId)->update($data);

        return $this->mutationResponse($request, 'Status referral berhasil diperbarui.');
    }

    public function destroyReferral(Request $request, int $referralId)
    {
        Referral::findOrFail($referralId)->delete();

        return $this->mutationResponse($request, 'Kode referral berhasil dihapus.');
    }

    public function storeFeesBulk(Request $request)
    {
        $data = $request->validate([
            'period_id' => ['required', 'exists:pmb_periods,id'],
            'path_id' => ['required', 'exists:pmb_paths,id'],
            'fees' => ['required', 'array', 'min:1'],
            'fees.*.program_id' => ['required', 'integer', 'distinct', 'exists:pmb_study_programs,id'],
            'fees.*.nominal_pendaftaran' => ['required', 'numeric', 'min:0'],
            'fees.*.nominal_spp' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($data) {
            foreach ($data['fees'] as $feeData) {
                Fee::updateOrCreate(
                    [
                        'period_id' => $data['period_id'],
                        'path_id' => $data['path_id'],
                        'program_id' => $feeData['program_id'],
                    ],
                    [
                        'nominal_pendaftaran' => $feeData['nominal_pendaftaran'],
                        'nominal_spp' => $feeData['nominal_spp'],
                    ]
                );
            }
        });

        return $this->mutationResponse($request, 'Biaya berhasil disimpan untuk seluruh program studi.');
    }

    public function updateFee(Request $request, int $feeId)
    {
        $data = $request->validate([
            'period_id' => ['required', 'exists:pmb_periods,id'],
            'path_id' => ['required', 'exists:pmb_paths,id'],
            'program_id' => ['required', 'exists:pmb_study_programs,id'],
            'nominal_pendaftaran' => ['required', 'numeric', 'min:0'],
            'nominal_spp' => ['required', 'numeric', 'min:0'],
        ]);
        $duplicate = Fee::where('period_id', $data['period_id'])
            ->where('path_id', $data['path_id'])
            ->where('program_id', $data['program_id'])
            ->where('id', '!=', $feeId)
            ->exists();
        if ($duplicate) {
            return response()->json(['message' => 'Biaya untuk periode, jalur, dan program studi ini sudah ada.'], 422);
        }
        Fee::findOrFail($feeId)->update($data);

        return $this->mutationResponse($request, 'Pengaturan biaya berhasil diperbarui.');
    }

    public function destroyFee(Request $request, int $feeId)
    {
        Fee::findOrFail($feeId)->delete();

        return $this->mutationResponse($request, 'Pengaturan biaya berhasil dihapus.');
    }

    public function updateRegistrationStatus(Request $request, int $registrationId)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['berkas_masuk', 'review', 'sudah_ujian', 'lulus', 'diterima', 'ditolak', 'mundur'])],
        ]);

        Registration::findOrFail($registrationId)->update(['status' => $data['status']]);

        return $this->mutationResponse($request, 'Status pendaftaran berhasil diperbarui.');
    }

    public function updateRegistrationDetails(Request $request, int $registrationId)
    {
        $data = $request->validate([
            'nama_mahasiswa' => ['required', 'string', 'max:255'],
            'nik' => ['nullable', 'digits:16'],
            'jenis_kelamin' => ['nullable', Rule::in(['L', 'P'])],
            'handphone' => ['nullable', 'string', 'max:20'],
            'status' => ['required', Rule::in(['draft', 'berkas_masuk', 'review', 'sudah_ujian', 'lulus', 'diterima', 'ditolak', 'mundur'])],
        ]);

        $registration = Registration::with('candidate')->findOrFail($registrationId);
        DB::transaction(function () use ($registration, $data) {
            $registration->candidate->update([
                'nama_mahasiswa' => $data['nama_mahasiswa'],
                'nik' => $data['nik'] ?? null,
                'jenis_kelamin' => $data['jenis_kelamin'] ?? null,
                'handphone' => $data['handphone'] ?? null,
            ]);
            $registration->update(['status' => $data['status']]);
        });

        return $this->mutationResponse($request, 'Data pendaftar berhasil diperbarui.');
    }

    public function destroyRegistration(Request $request, int $registrationId)
    {
        $registration = Registration::with('payments')->findOrFail($registrationId);
        $files = $registration->payments->pluck('file_bukti_bayar')->filter()->all();
        $files = array_merge($files, CandidateDocument::where('registration_id', $registrationId)->pluck('file_path')->all());

        DB::transaction(fn () => $registration->delete());
        Storage::disk('local')->delete($files);

        return $this->mutationResponse($request, 'Data pendaftar dan berkas terkait berhasil dihapus.');
    }

    public function verifyPayment(Request $request, int $paymentId)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['lunas', 'ditolak'])],
        ]);

        $updated = Payment::whereKey($paymentId)
            ->where('status', 'menunggu_verifikasi')
            ->update([
                'status' => $data['status'],
                'verified_by' => auth()->id(),
            ]);

        if (! $updated) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Tagihan tidak ditemukan atau sudah diproses.',
                ], 422);
            }

            return back()->withErrors(['payment' => 'Tagihan tidak ditemukan atau sudah diproses.']);
        }

        return $this->mutationResponse($request, 'Verifikasi pembayaran berhasil disimpan.');
    }

    private function mutationResponse(Request $request, string $message)
    {
        return $request->expectsJson()
            ? response()->json(['message' => $message])
            : back()->with('success', $message);
    }

    private function deleteBlocked(Request $request, string $message)
    {
        return $request->expectsJson()
            ? response()->json(['message' => $message], 422)
            : back()->withErrors(['delete' => $message]);
    }

    public function downloadPaymentProof(int $paymentId)
    {
        $payment = Payment::with('registration.candidate')->findOrFail($paymentId);
        abort_unless($payment->file_bukti_bayar, 404);

        $candidateUserId = $payment->registration->candidate->user_id;
        abort_unless(
            (int) $candidateUserId === (int) auth()->id()
                || auth()->user()->can('pmb-access')
                || auth()->user()->can('pmb-payment-review')
                || auth()->user()->can('pmb-review')
                || auth()->user()->can('pmb-manage'),
            403
        );
        abort_unless(Storage::disk('local')->exists($payment->file_bukti_bayar), 404);

        return Storage::disk('local')->download($payment->file_bukti_bayar);
    }
}
