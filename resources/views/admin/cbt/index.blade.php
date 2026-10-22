@extends('layouts.app')
@section('title', 'Manajemen Ujian CBT')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-semibold mb-1">Manajemen Ujian CBT</h4>
            <p class="text-muted mb-0">Buat ujian, kelola soal, dan periksa hasil peserta.</p>
        </div>
    </div>
    @if(session('success'))<div class="alert alert-success" data-app-toast>{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger" data-app-toast>{{ $errors->first() }}</div>@endif
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Buat ujian</h5>
            <form method="POST" action="{{ route('admin.cbt.exams.store') }}" class="row g-3 mb-4">
                @csrf
                <div class="col-md-6"><label class="form-label">Nama ujian</label><input class="form-control" name="title" value="{{ old('title') }}" required maxlength="255"></div>
                <div class="col-md-3"><label class="form-label">Durasi (menit)</label><input class="form-control" type="number" name="duration_minutes" min="1" max="600" value="{{ old('duration_minutes', 60) }}" required></div>
                <div class="col-md-3"><label class="form-label">Mulai</label><input class="form-control" type="datetime-local" name="starts_at"></div>
                <div class="col-md-3"><label class="form-label">Berakhir</label><input class="form-control" type="datetime-local" name="ends_at"></div>
                <div class="col-md-7"><label class="form-label">Petunjuk / keterangan</label><input class="form-control" name="description" maxlength="5000"></div>
                <div class="col-md-5"><label class="form-label d-block">Jalur yang wajib mengikuti ujian</label>
                    <div class="d-flex flex-wrap align-items-center gap-2"><button type="button" class="btn btn-outline-primary" data-path-picker data-selected-paths="{{ collect(old('path_ids', []))->implode(',') }}"><i class="ti ti-route me-1"></i>Pilih jalur</button><span class="small text-muted" data-path-summary>{{ collect(old('path_ids', []))->count() ? collect(old('path_ids', []))->count().' jalur dipilih' : 'Belum ada jalur dipilih' }}</span></div>
                    <div data-path-inputs>@foreach(old('path_ids', []) as $pathId)<input type="hidden" name="path_ids[]" value="{{ $pathId }}">@endforeach</div><small class="text-muted">Peserta dari jalur lain tidak akan melihat ujian ini.</small>
                </div>
                <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary w-100">Buat ujian</button></div>
            </form>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Ujian</th>
                            <th>Jadwal</th>
                            <th>Durasi</th>
                            <th>Jalur</th>
                            <th>Soal</th>
                            <th>Peserta</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exams as $exam)
                        <tr>
                            <td><strong>{{ $exam->title }}</strong>
                                <div class="small text-muted">{{ $exam->description }}</div>
                            </td>
                            <td>{{ $exam->starts_at?->format('d/m/Y H:i') ?? 'Fleksibel' }}<br><small>s.d. {{ $exam->ends_at?->format('d/m/Y H:i') ?? 'tanpa batas' }}</small></td>
                            <td>{{ $exam->duration_minutes }} menit</td>
                            <td>
                                <div class="small mb-2" data-path-summary>{{ $exam->paths->pluck('nama_jalur')->join(', ') ?: 'Belum dipilih' }}</div>
                                <form method="POST" action="{{ route('admin.cbt.exams.paths', $exam->id) }}">@csrf @method('PATCH')<div class="d-flex flex-wrap gap-1 align-items-center"><button type="button" class="btn btn-sm btn-outline-primary" data-path-picker data-selected-paths="{{ $exam->paths->pluck('id')->implode(',') }}">Jalur</button> </div>
                                    <div data-path-inputs>@foreach($exam->paths as $path)<input type="hidden" name="path_ids[]" value="{{ $path->id }}">@endforeach</div>
                                </form>
                            </td>
                            <td>{{ $exam->questions_count }}</td>
                            <td>{{ $exam->attempts_count }}</td>
                            <td><span class="badge {{ $exam->is_published ? 'bg-success' : 'bg-secondary' }}">{{ $exam->is_published ? 'Terbit' : 'Draft' }}</span></td>
                            <td class="text-nowrap">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.cbt.questions.index', $exam->id) }}">Kelola soal</a>
                                <a class="btn btn-sm btn-outline-info" href="{{ route('admin.cbt.attempts.index', $exam->id) }}">Hasil</a>
                                <form class="d-inline" method="POST" action="{{ route('admin.cbt.exams.update', $exam->id) }}">@csrf @method('PATCH')<input type="hidden" name="is_published" value="{{ $exam->is_published ? 0 : 1 }}"><button class="btn btn-sm btn-outline-success">{{ $exam->is_published ? 'Tarik' : 'Terbitkan' }}</button></form>
                                <form class="d-inline" method="POST" action="{{ route('admin.cbt.exams.destroy', $exam->id) }}" onsubmit="return confirm('Hapus ujian dan seluruh hasilnya?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form>
                            </td>
                        </tr>
                        @empty<tr>
                            <td colspan="8" class="text-center text-muted py-4">Belum ada ujian CBT.</td>
                        </tr>@endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="cbt-path-modal" tabindex="-1" aria-labelledby="cbt-path-modal-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cbt-path-modal-title">Pilih jalur ujian</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">Pilih satu atau beberapa jalur. Hanya pendaftar dari jalur terpilih yang dapat mengikuti ujian.</p>
                <div class="d-flex flex-column gap-2" id="cbt-path-options">
                    @forelse($paths as $path)
                    <label class="form-check border rounded p-3 ps-5 mb-0"><input class="form-check-input" type="checkbox" value="{{ $path->id }}" data-path-name="{{ $path->nama_jalur }}"><span class="form-check-label">{{ $path->nama_jalur }}</span></label>
                    @empty
                    <div class="alert alert-warning mb-0">Belum ada jalur pendaftaran. Tambahkan jalur terlebih dahulu di menu Jalur Pendaftaran.</div>
                    @endforelse
                </div>
                <div class="small text-danger mt-2 d-none" id="cbt-path-error">Pilih minimal satu jalur ujian.</div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button type="button" class="btn btn-primary" id="cbt-save-paths">Terapkan pilihan</button></div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    (() => {
        const modalElement = document.getElementById('cbt-path-modal');
        if (!modalElement || !window.bootstrap) return;
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        const checkboxes = [...modalElement.querySelectorAll('[data-path-name]')];
        const error = document.getElementById('cbt-path-error');
        let activePicker = null;

        document.querySelectorAll('[data-path-picker]').forEach((button) => {
            button.addEventListener('click', () => {
                activePicker = button;
                const holder = button.closest('form')?.querySelector('[data-path-inputs]') ||
                    button.parentElement.parentElement.querySelector('[data-path-inputs]');
                const selected = (holder ? [...holder.querySelectorAll('input[name="path_ids[]"]')].map((input) => input.value) : (button.dataset.selectedPaths || '').split(',').filter(Boolean));
                checkboxes.forEach((checkbox) => {
                    checkbox.checked = selected.includes(checkbox.value);
                });
                error.classList.add('d-none');
                modal.show();
            });
        });

        document.getElementById('cbt-save-paths')?.addEventListener('click', () => {
            const selected = checkboxes.filter((checkbox) => checkbox.checked);
            if (!selected.length) {
                error.classList.remove('d-none');
                return;
            }
            const form = activePicker?.closest('form') || activePicker?.closest('.col-md-5')?.querySelector('form');
            const holder = form?.querySelector('[data-path-inputs]') || activePicker?.parentElement.parentElement.querySelector('[data-path-inputs]');
            if (!holder) return;
            holder.replaceChildren(...selected.map((checkbox) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'path_ids[]';
                input.value = checkbox.value;
                return input;
            }));
            const summary = activePicker?.closest('form')?.parentElement?.querySelector('[data-path-summary]') ||
                activePicker?.parentElement.querySelector('[data-path-summary]');
            if (summary) summary.textContent = selected.map((checkbox) => checkbox.dataset.pathName).join(', ');
            activePicker.dataset.selectedPaths = selected.map((checkbox) => checkbox.value).join(',');
            modal.hide();
        });
    })();
</script>
@endpush
