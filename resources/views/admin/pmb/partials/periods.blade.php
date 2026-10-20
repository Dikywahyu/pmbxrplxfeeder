@forelse($periods as $period)
    <div class="d-flex justify-content-between align-items-center border-top py-2 gap-2">
        <span class="small">
            {{ $period->nama_periode }}
            <span class="badge {{ $period->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $period->is_active ? 'Aktif' : 'Nonaktif' }}</span>
            <span class="text-muted">{{ $period->tanggal_mulai->format('d/m/Y') }} – {{ $period->tanggal_selesai->format('d/m/Y') }}</span>
        </span>
        <form method="POST" action="{{ route('admin.pmb.periods.status', $period->id) }}" data-ajax-form>
            @csrf
            @method('PATCH')
            <input type="hidden" name="is_active" value="{{ $period->is_active ? 0 : 1 }}">
            <button class="btn btn-outline-secondary btn-sm" type="submit">{{ $period->is_active ? 'Tutup' : 'Aktifkan' }}</button>
        </form>
    </div>
@empty
    <p class="text-muted small mb-0">Belum ada periode.</p>
@endforelse
