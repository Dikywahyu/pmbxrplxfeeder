@extends('layouts.app')
@section('title', 'Hasil Ujian CBT')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4"><div><h4 class="fw-semibold mb-1">Hasil: {{ $exam->title }}</h4><p class="text-muted mb-0">Periksa jawaban esai dan beri nilai manual.</p></div><a class="btn btn-light" href="{{ route('admin.cbt.index') }}">Kembali</a></div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @forelse($exam->attempts as $attempt)
        <div class="card"><div class="card-body"><div class="d-flex justify-content-between flex-wrap"><div><h5>{{ $attempt->registration->candidate?->nama_mahasiswa ?? 'Pendaftar' }}</h5><p class="text-muted">{{ $attempt->registration->nomor_pendaftaran }} · {{ $attempt->status }} · Mulai {{ $attempt->started_at->format('d/m/Y H:i') }}</p></div><span class="badge bg-primary align-self-start">Nilai saat ini: {{ $attempt->score }}</span></div>
            @foreach($attempt->answers as $answer)
                <div class="border rounded p-3 mb-3"><div class="d-flex justify-content-between"><strong>{{ $answer->question->type === 'essay' ? 'Esai' : 'Pilihan ganda' }} · {{ $answer->question->points }} poin</strong><span>Nilai: {{ $answer->score ?? 'Belum dinilai' }}</span></div><p class="mb-2 mt-2">{{ $answer->question->question_text }}</p>
                    @if($answer->question->type === 'essay')
                        <div class="bg-light rounded p-3 mb-2">{{ $answer->answer_text ?: 'Tidak ada teks jawaban.' }}</div>
                        @if($answer->answer_image_path)<a href="{{ asset('storage/'.$answer->answer_image_path) }}" target="_blank">Lihat gambar jawaban</a>@endif
                        <form method="POST" action="{{ route('admin.cbt.answers.grade', $answer->id) }}" class="row g-2 mt-2">@csrf @method('PATCH')<div class="col-md-2"><input class="form-control" type="number" name="score" min="0" max="{{ $answer->question->points }}" step="0.01" value="{{ $answer->score }}" placeholder="Nilai" required></div><div class="col-md-7"><input class="form-control" name="grader_note" value="{{ $answer->grader_note }}" placeholder="Catatan penilai"></div><div class="col-md-3"><button class="btn btn-primary">Simpan nilai</button></div></form>
                    @else
                        <p class="mb-0">Jawaban: <strong>{{ $answer->selected_option ?? 'Tidak dijawab' }}</strong> · Kunci: {{ $answer->question->correct_option }}</p>
                    @endif
                </div>
            @endforeach
        </div></div>
    @empty<div class="card"><div class="card-body text-muted">Belum ada peserta yang memulai ujian.</div></div>@endforelse
</div>
@endsection
