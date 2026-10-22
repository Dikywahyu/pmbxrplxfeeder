@extends('layouts.app')
@section('title', 'Soal CBT')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4"><div><h4 class="fw-semibold mb-1">Soal: {{ $exam->title }}</h4><p class="text-muted mb-0">Pilihan jawaban dapat berupa teks, gambar, atau gabungan keduanya.</p></div><a class="btn btn-light" href="{{ route('admin.cbt.index') }}">Kembali ke ujian</a></div>
    @if(session('success'))<div class="alert alert-success" data-app-toast>{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger" data-app-toast>{{ $errors->first() }}</div>@endif
    @if($exam->is_published)<div class="alert alert-warning">Ujian sedang terbit. Tarik publikasi terlebih dahulu untuk mengubah soal.</div>@else
    <div class="card"><div class="card-body"><h5 class="card-title">Tambah soal</h5>
        <form method="POST" enctype="multipart/form-data" action="{{ route('admin.cbt.questions.store', $exam->id) }}" class="row g-3">
            @csrf
            <div class="col-md-4"><label class="form-label">Jenis soal</label><select class="form-select" name="type" id="cbt-question-type"><option value="multiple_choice">Pilihan ganda</option><option value="essay">Esai</option></select></div>
            <div class="col-md-2"><label class="form-label">Poin</label><input class="form-control" name="points" type="number" min="1" max="1000" value="1" required></div>
            <div class="col-md-6"><label class="form-label">Gambar soal (opsional)</label><input class="form-control" name="question_image" type="file" accept="image/jpeg,image/png,image/webp"></div>
            <div class="col-12"><label class="form-label">Teks soal</label><textarea class="form-control" name="question_text" rows="3" required maxlength="20000"></textarea></div>
            <div class="col-12" id="cbt-options"><div class="row g-3">
                @foreach(['A','B','C','D','E'] as $key)
                    <div class="col-lg-6"><div class="border rounded p-3 h-100"><label class="form-label">Opsi {{ $key }} @if($key==='A') <span class="text-danger">(jawaban benar?)</span>@endif</label><input class="form-control mb-2" name="option_text[{{ $key }}]" placeholder="Teks opsi {{ $key }}"><input class="form-control mb-2" type="file" name="option_image[{{ $key }}]" accept="image/jpeg,image/png,image/webp"><div class="form-check"><input class="form-check-input" type="radio" name="correct_option" value="{{ $key }}" @checked($key==='A')><label class="form-check-label">Tandai sebagai jawaban benar</label></div></div></div>
                @endforeach
            </div></div>
            <div class="col-12"><button class="btn btn-primary">Tambah soal</button></div>
        </form>
    </div></div>
    @endif
    <div class="card"><div class="card-body"><h5 class="card-title">Daftar soal ({{ $exam->questions->count() }})</h5>
        @forelse($exam->questions as $question)
            <div class="border rounded p-3 mb-3">
                <div class="d-flex justify-content-between gap-3"><div><span class="badge bg-primary-subtle text-primary me-2">{{ $question->type === 'essay' ? 'Esai' : 'Pilihan ganda' }}</span><span class="badge bg-secondary-subtle text-secondary">{{ $question->points }} poin</span><p class="mt-2 mb-2">{{ $question->question_text }}</p>
                    @if($question->question_image_path)<img class="img-fluid rounded mb-2" style="max-height:220px" src="{{ asset('storage/'.$question->question_image_path) }}" alt="Gambar soal">@endif
                    @foreach($question->options ?? [] as $key => $option)<div class="small mb-1"><strong>{{ $key }}.</strong> {{ $option['text'] ?? '' }} @if(!empty($option['image']))<img src="{{ asset('storage/'.$option['image']) }}" alt="Gambar opsi {{ $key }}" style="max-height:70px" class="ms-2 rounded">@endif @if($question->correct_option===$key)<span class="badge bg-success ms-1">Benar</span>@endif</div>@endforeach
                </div>
                @unless($exam->is_published)<form method="POST" action="{{ route('admin.cbt.questions.destroy', $question->id) }}" onsubmit="return confirm('Hapus soal ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form>@endunless</div>
            </div>
        @empty<p class="text-muted mb-0">Belum ada soal.</p>@endforelse
    </div></div>
</div>
@endsection
@push('scripts')
<script>document.getElementById('cbt-question-type')?.addEventListener('change', e => { document.getElementById('cbt-options').classList.toggle('d-none', e.target.value !== 'multiple_choice'); });</script>
@endpush
