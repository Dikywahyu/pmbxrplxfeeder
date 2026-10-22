@extends('layouts.app')
@section('title', 'Ujian CBT')
@section('content')
<div class="container py-3"><div class="mb-4"><h3 class="fw-bold mb-1">Ujian CBT</h3><p class="text-muted mb-0">Ujian dapat berisi pilihan ganda dan esai. Perhatikan jadwal dan durasi sebelum memulai.</p></div>
    @if(session('success'))<div class="alert alert-success" data-app-toast>{{ session('success') }}</div>@endif
    @forelse($exams as $exam)
        @php($attempt = $attempts->get($exam->id))
        <div class="card shadow-sm"><div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div><h5 class="fw-semibold mb-1">{{ $exam->title }}</h5><p class="text-muted mb-2">{{ $exam->description }}</p><span class="badge bg-primary-subtle text-primary">{{ $exam->questions_count }} soal</span><span class="badge bg-secondary-subtle text-secondary">{{ $exam->duration_minutes }} menit</span>
                @if($exam->starts_at)<span class="badge bg-light text-dark">Mulai {{ $exam->starts_at->format('d/m/Y H:i') }}</span>@endif @if($exam->ends_at)<span class="badge bg-light text-dark">Tutup {{ $exam->ends_at->format('d/m/Y H:i') }}</span>@endif
            </div>
            <div>
                @if($attempt?->status === 'submitted')<a class="btn btn-outline-primary" href="{{ route('pmb.exams.result', $attempt->id) }}">Lihat hasil</a>
                @elseif($attempt?->status === 'in_progress')<a class="btn btn-warning" href="{{ route('pmb.exams.take', $attempt->id) }}">Lanjutkan ujian</a>
                @elseif($registrations->isNotEmpty())<form method="POST" action="{{ route('pmb.exams.start', [$exam->id, $registrations->first()->id]) }}">@csrf<button class="btn btn-primary">Mulai ujian</button></form>
                @else<span class="text-muted">Anda perlu memiliki pendaftaran PMB untuk mengikuti ujian.</span>@endif
            </div>
        </div></div>
    @empty<div class="card"><div class="card-body text-center py-5"><i class="ti ti-clipboard-off fs-1 text-muted"></i><h5 class="mt-2">Belum ada ujian tersedia</h5><p class="text-muted mb-0">Ujian akan muncul di sini saat panitia menerbitkannya.</p></div></div>@endforelse
</div>
@endsection
