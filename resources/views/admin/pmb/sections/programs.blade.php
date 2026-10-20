<div class="card border mb-0"><div class="card-header bg-transparent"><h5 class="card-title mb-0">Program studi</h5></div><div class="card-body">
    <form method="POST" action="{{ route('admin.pmb.programs.store') }}" data-ajax-form class="row g-3 mb-4">
        @csrf
        <div class="col-md-4"><label class="form-label">ID Program Studi DIKTI (UUID)</label><input class="form-control" name="id_dikti" placeholder="9860403a-f9ed-4959-8eef-98a7fbd91543" maxlength="36"></div>
        <div class="col-md-2"><label class="form-label">Kode prodi</label><input class="form-control" name="kode_prodi" maxlength="255" required></div>
        <div class="col-md-3"><label class="form-label">Nama program studi</label><input class="form-control" name="nama_prodi" maxlength="255" required></div>
        <div class="col-md-1"><label class="form-label">Jenjang</label><input class="form-control" name="jenjang" placeholder="S1" maxlength="50" required></div>
        <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary" type="submit"><i class="ti ti-plus me-1"></i>Tambah</button></div>
    </form>
    <div class="table-responsive"><table class="table table-sm table-hover align-middle w-100 pmb-data-table"><thead class="table-light"><tr><th>ID DIKTI</th><th>Kode</th><th>Program studi</th><th>Jenjang</th><th>Status</th><th class="text-end" data-orderable="false" data-searchable="false">Aksi</th></tr></thead><tbody>
        @foreach($programs as $program)
            <tr>
                <td><code>{{ $program->id_dikti ?: 'Belum dipetakan' }}</code></td><td>{{ $program->kode_prodi }}</td><td class="fw-semibold">{{ $program->nama_prodi }}</td><td>{{ $program->jenjang }}</td>
                <td><span class="badge {{ $program->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">{{ $program->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                <td class="text-end text-nowrap">
                    <button type="button" class="btn btn-sm btn-light-primary text-primary" data-pmb-edit data-update-url="{{ route('admin.pmb.programs.update', $program->id) }}" data-payload="{{ json_encode(['record' => ['id_dikti' => $program->id_dikti, 'kode_prodi' => $program->kode_prodi, 'nama_prodi' => $program->nama_prodi, 'jenjang' => $program->jenjang, 'is_active' => $program->is_active ? '1' : '0']]) }}"><i class="ti ti-edit"></i> Edit</button>
                    <button type="button" class="btn btn-sm btn-light-danger text-danger" data-pmb-delete data-delete-url="{{ route('admin.pmb.programs.destroy', $program->id) }}"><i class="ti ti-trash"></i> Hapus</button>
                </td>
            </tr>
        @endforeach
    </tbody></table></div>
</div></div>
