@extends('layouts.app')
@section('title', $attempt->exam->title)
@section('content')
<div class="container py-3">
    <div class="card border-primary shadow-sm sticky-top mb-4" style="top:10px;z-index:10"><div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2"><div><h4 class="fw-bold mb-1">{{ $attempt->exam->title }}</h4><span class="text-muted">{{ $attempt->exam->questions->count() }} soal · {{ $attempt->exam->duration_minutes }} menit</span></div><div class="badge bg-danger fs-5 px-3 py-2">Sisa waktu: <span id="cbt-timer">--:--</span></div></div></div>
    @if($attempt->exam->description)<div class="alert alert-info">{{ $attempt->exam->description }}</div>@endif
    <form id="cbt-answer-form" method="POST" enctype="multipart/form-data" action="{{ route('pmb.exams.submit', $attempt->id) }}">@csrf
        @foreach($attempt->exam->questions as $index => $question)
            @php($saved = $attempt->answers->firstWhere('question_id', $question->id))
            <div class="card"><div class="card-body"><div class="d-flex justify-content-between"><h5 class="fw-semibold">Soal {{ $index + 1 }}</h5><span class="badge bg-secondary-subtle text-secondary">{{ $question->points }} poin</span></div>
                <p class="mb-3">{{ $question->question_text }}</p>
                @if($question->question_image_path)<img class="img-fluid rounded mb-3" style="max-height:360px" src="{{ asset('storage/'.$question->question_image_path) }}" alt="Gambar soal {{ $index + 1 }}">@endif
                @if($question->type === 'multiple_choice')
                    @foreach($question->options ?? [] as $key => $option)
                        <label class="d-flex align-items-start gap-2 border rounded p-3 mb-2 cursor-pointer"><input class="form-check-input mt-1" type="radio" name="answers[{{ $question->id }}][selected_option]" value="{{ $key }}" @checked($saved?->selected_option === $key)><span><strong>{{ $key }}.</strong> {{ $option['text'] ?? '' }} @if(!empty($option['image']))<br><img src="{{ asset('storage/'.$option['image']) }}" alt="Gambar opsi {{ $key }}" class="img-fluid rounded mt-2" style="max-height:220px">@endif</span></label>
                    @endforeach
                @else
                    <label class="form-label">Jawaban esai</label><textarea class="form-control mb-3" rows="6" name="answers[{{ $question->id }}][answer_text]" maxlength="20000" placeholder="Tulis jawaban Anda">{{ $saved?->answer_text }}</textarea>
                    <label class="form-label">Lampiran gambar jawaban (opsional)</label><input class="form-control" type="file" name="answers[{{ $question->id }}][answer_image]" accept="image/jpeg,image/png,image/webp"><small class="text-muted">Format JPG, PNG, atau WEBP; maksimal 5 MB.</small>
                    @if($saved?->answer_image_path)<div class="mt-2">Gambar terlampir sebelumnya: <a href="{{ asset('storage/'.$saved->answer_image_path) }}" target="_blank">Lihat</a></div>@endif
                @endif
            </div></div>
        @endforeach
        <div class="d-flex justify-content-end mb-5"><button class="btn btn-success btn-lg" onclick="return confirm('Kumpulkan jawaban sekarang? Ujian tidak dapat dibuka kembali setelah dikumpulkan.')"><i class="ti ti-send me-1"></i>Kumpulkan jawaban</button></div>
    </form>
</div>
@endsection
@push('scripts')
<script>
(() => {
    const deadline = new Date(@json($deadline->toIso8601String())).getTime();
    const timer = document.getElementById('cbt-timer');
    const form = document.getElementById('cbt-answer-form');
    const tick = () => {
        const remaining = Math.max(0, Math.floor((deadline - Date.now()) / 1000));
        timer.textContent = `${String(Math.floor(remaining / 60)).padStart(2, '0')}:${String(remaining % 60).padStart(2, '0')}`;
        if (remaining <= 0) { timer.textContent = '00:00'; form.requestSubmit(); }
    };
    tick(); setInterval(tick, 1000);
})();
</script>
@endpush
