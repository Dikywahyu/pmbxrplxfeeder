<div class="card border mb-0"><div class="card-header bg-transparent"><h5 class="card-title mb-0">Jalur pendaftaran</h5></div><div class="card-body">
    <form method="POST" action="{{ route('admin.pmb.paths.store') }}" data-ajax-form class="row g-3 mb-4">
        @csrf
        <div class="col-md-5"><label class="form-label">Nama jalur</label><input class="form-control" name="nama_jalur" maxlength="255" required></div>
        <div class="col-md-5"><label class="form-label">Deskripsi</label><input class="form-control" name="deskripsi" maxlength="5000"></div>
        <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary" type="submit"><i class="ti ti-plus me-1"></i>Tambah jalur</button></div>
    </form>
    <div class="table-responsive"><table class="table table-sm table-hover align-middle w-100 pmb-data-table"><thead class="table-light"><tr><th>Nama jalur</th><th>Deskripsi</th><th class="text-end" data-orderable="false" data-searchable="false">Aksi</th></tr></thead><tbody>
        @foreach($paths as $path)
            <tr>
                <td class="fw-semibold">{{ $path->nama_jalur }}</td><td>{{ $path->deskripsi ?: '—' }}</td>
                <td class="text-end text-nowrap">
                    <button type="button" class="btn btn-sm btn-light-primary text-primary" data-pmb-edit data-update-url="{{ route('admin.pmb.paths.update', $path->id) }}" data-payload="{{ json_encode(['record' => ['nama_jalur' => $path->nama_jalur, 'deskripsi' => $path->deskripsi]]) }}"><i class="ti ti-edit"></i> Edit</button>
                    <button type="button" class="btn btn-sm btn-light-danger text-danger" data-pmb-delete data-delete-url="{{ route('admin.pmb.paths.destroy', $path->id) }}"><i class="ti ti-trash"></i> Hapus</button>
                </td>
            </tr>
        @endforeach
    </tbody></table></div>
</div></div>
