@forelse($paths as $path)
    <span class="badge bg-light text-dark border me-1 mb-1">{{ $path->nama_jalur }}</span>
@empty
    <p class="text-muted small mb-0">Belum ada jalur pendaftaran.</p>
@endforelse
