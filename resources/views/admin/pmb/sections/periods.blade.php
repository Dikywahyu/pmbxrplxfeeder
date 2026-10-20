<div class="card border mb-0"><div class="card-header bg-transparent"><h5 class="card-title mb-0">Periode pendaftaran</h5></div><div class="card-body">
    <form method="POST" action="{{ route('admin.pmb.periods.store') }}" data-ajax-form class="row g-3 mb-4">
        @csrf
        <div class="col-lg-4"><label class="form-label">Nama periode</label><input class="form-control" name="nama_periode" maxlength="255" required></div>
        <div class="col-lg-2"><label class="form-label">Tanggal mulai</label><input class="form-control" type="date" name="tanggal_mulai" required></div>
        <div class="col-lg-2"><label class="form-label">Tanggal selesai</label><input class="form-control" type="date" name="tanggal_selesai" required></div>
        <div class="col-lg-2 d-flex align-items-end"><div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="period-active"><label class="form-check-label" for="period-active">Aktifkan</label></div></div>
        <div class="col-lg-2 d-flex align-items-end"><button class="btn btn-primary" type="submit"><i class="ti ti-plus me-1"></i>Tambah periode</button></div>
    </form>
    <div class="table-responsive"><table class="table table-sm table-hover align-middle w-100 pmb-data-table"><thead class="table-light"><tr><th>Nama periode</th><th>Tanggal</th><th>Status</th><th class="text-end" data-orderable="false" data-searchable="false">Aksi</th></tr></thead><tbody>
        @foreach($periods as $period)
            <tr>
                <td class="fw-semibold">{{ $period->nama_periode }}</td>
                <td>{{ $period->tanggal_mulai->format('d/m/Y') }} – {{ $period->tanggal_selesai->format('d/m/Y') }}</td>
                <td><span class="badge {{ $period->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">{{ $period->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                <td class="text-end text-nowrap">
                    <button type="button" class="btn btn-sm btn-light-primary text-primary" data-pmb-edit data-update-url="{{ route('admin.pmb.periods.update', $period->id) }}" data-payload="{{ json_encode(['record' => ['nama_periode' => $period->nama_periode, 'tanggal_mulai' => $period->tanggal_mulai->format('Y-m-d'), 'tanggal_selesai' => $period->tanggal_selesai->format('Y-m-d'), 'is_active' => $period->is_active ? '1' : '0']]) }}"><i class="ti ti-edit"></i> Edit</button>
                    <button type="button" class="btn btn-sm btn-light-danger text-danger" data-pmb-delete data-delete-url="{{ route('admin.pmb.periods.destroy', $period->id) }}"><i class="ti ti-trash"></i> Hapus</button>
                </td>
            </tr>
        @endforeach
    </tbody></table></div>
</div></div>
