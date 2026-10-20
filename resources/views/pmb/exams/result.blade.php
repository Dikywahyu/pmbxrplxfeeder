@extends('layouts.app')
@section('title', 'Hasil Ujian')
@section('content')
<div class="container py-3"><div class="card"><div class="card-body text-center py-5"><i class="ti ti-circle-check fs-1 text-success"></i><h3 class="fw-bold mt-2">Jawaban berhasil dikumpulkan</h3><h5>{{ $attempt->exam->title }}</h5>
    @php($pendingEssay = $attempt->answers->filter(fn($answer) => $answer->question->type === 'essay' && $answer->score === null)->count())
    <div class="display-6 fw-bold text-primary mt-3">{{ $attempt->score }}</div><p class="text-muted">Nilai sementara</p>
    @if($pendingEssay)<div class="alert alert-info d-inline-block">{{ $pendingEssay }} jawaban esai menunggu penilaian panitia. Nilai akhir diperbarui setelah pemeriksaan.</div>@else<div class="alert alert-success d-inline-block">Semua jawaban sudah dinilai.</div>@endif
    <div><a href="{{ route('pmb.exams.index') }}" class="btn btn-primary">Kembali ke daftar ujian</a></div>
</div></div></div>
@endsection
