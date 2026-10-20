<div class="card border mb-0"><div class="card-header bg-transparent"><h5 class="card-title mb-0">Berkas yang dapat diunggah pendaftar</h5><small class="text-muted">Persyaratan diatur per jalur pendaftaran.</small></div><div class="card-body">
    <form method="POST" action="{{ route('admin.pmb.requirements.store') }}" data-ajax-form class="row g-3 mb-4">
        @csrf
        <div class="col-lg-3 col-md-6"><label class="form-label">Jalur</label><select class="form-select" name="path_id" required><option value="">Pilih jalur</option>@foreach($paths as $path)<option value="{{ $path->id }}">{{ $path->nama_jalur }}</option>@endforeach</select></div>
        <div class="col-lg-3 col-md-6"><label class="form-label">Nama berkas</label><input class="form-control" name="nama_dokumen" maxlength="255" placeholder="Contoh: Kartu Keluarga" required></div>
        <div class="col-lg-2 col-md-6"><label class="form-label">Format diterima</label><input class="form-control" name="format_file" value="pdf,jpg,jpeg,png" required><small class="text-muted">Pisahkan dengan koma</small></div>
        <div class="col-lg-2 col-md-6"><label class="form-label">Sifat</label><select class="form-select" name="is_required"><option value="1">Wajib</option><option value="0">Opsional</option></select></div>
        <div class="col-lg-2 d-flex align-items-end"><button class="btn btn-primary" type="submit"><i class="ti ti-plus me-1"></i>Tambah berkas</button></div>
    </form>
    <div class="table-responsive"><table class="table table-sm table-hover align-middle w-100 pmb-data-table"><thead class="table-light"><tr><th>Nama berkas</th><th>Jalur</th><th>Sifat</th><th>Format</th><th data-orderable="false" data-searchable="false">Aksi</th></tr></thead><tbody>
        @foreach($requirements as $requirement)
            <tr><td class="fw-semibold">{{ $requirement->nama_dokumen }}</td><td>{{ $requirement->path?->nama_jalur ?? '—' }}</td><td>{{ $requirement->is_required ? 'Wajib' : 'Opsional' }}</td><td>{{ strtoupper(str_replace(',', ', ', $requirement->format_file)) }}</td><td class="text-nowrap">
                <button type="button" class="btn btn-sm btn-light-primary text-primary" data-pmb-edit data-update-url="{{ route('admin.pmb.requirements.update', $requirement->id) }}" data-payload="{{ json_encode(['record' => ['path_id' => (string) $requirement->path_id, 'nama_dokumen' => $requirement->nama_dokumen, 'is_required' => $requirement->is_required ? '1' : '0', 'format_file' => $requirement->format_file], 'options' => ['path_id' => $paths->map(fn($path) => [(string) $path->id, $path->nama_jalur])->values()]], JSON_HEX_APOS | JSON_HEX_QUOT) }}"><i class="ti ti-edit"></i> Edit</button>
                <button type="button" class="btn btn-sm btn-light-danger text-danger" data-pmb-delete data-delete-url="{{ route('admin.pmb.requirements.destroy', $requirement->id) }}"><i class="ti ti-trash"></i> Hapus</button>
            </td></tr>
        @endforeach
    </tbody></table></div>
</div></div>
