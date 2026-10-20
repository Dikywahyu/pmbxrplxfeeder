@forelse($programs as $program)
    <div class="border-top py-2">
        <strong>{{ $program->nama_prodi }}</strong>
        <span class="text-muted">({{ $program->kode_prodi }} · {{ $program->jenjang }})</span>
    </div>
@empty
    <p class="text-muted small mb-0">Belum ada program studi.</p>
@endforelse
