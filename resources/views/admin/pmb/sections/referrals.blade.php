<div class="row g-4">
    <div class="col-12">
        <div class="alert alert-info mb-0">
            Tautkan satu akun pengguna ke kode referral. Pendaftar yang membuka link referral akan tercatat pada akun tersebut.
        </div>
    </div>
    <div class="col-12">
        <div class="card border mb-0">
            <div class="card-body">
                <h5 class="card-title">Tambah marketing</h5>
                <form action="{{ route('admin.pmb.referrals.store') }}" method="POST" data-ajax-form class="row g-3 align-items-end">
                    @csrf
                    <div class="col-md-8">
                        <label class="form-label" for="referral-owner">Akun pengguna</label>
                        <select class="form-select" id="referral-owner" name="owner_id" required>
                            <option value="">Pilih akun</option>
                            @foreach($users as $user)
                                @unless($referrals->contains('owner_id', $user->id))
                                    <option value="{{ $user->id }}">{{ $user->name }} — {{ $user->email }}</option>
                                @endunless
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4"><button class="btn btn-primary" type="submit">Buat kode referral</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="table-responsive">
            <table class="table table-striped align-middle pmb-data-table w-100">
                <thead><tr><th>Marketing</th><th>Email</th><th>Kode referral</th><th>Link referral</th><th>Pendaftar</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                    @foreach($referrals as $referral)
                        @php($link = route('pmb.index', ['ref' => $referral->kode_referral]))
                        <tr>
                            <td>{{ $referral->owner?->name ?? 'Akun dihapus' }}</td>
                            <td>{{ $referral->owner?->email ?? '—' }}</td>
                            <td><code>{{ $referral->kode_referral }}</code></td>
                            <td><button type="button" class="btn btn-sm btn-light-primary text-primary" data-copy-link="{{ $link }}"><i class="ti ti-copy me-1"></i>Salin link</button></td>
                            <td>{{ $referral->registrations_count }}</td>
                            <td><span class="badge {{ $referral->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">{{ $referral->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                            <td class="text-nowrap">
                                <form action="{{ route('admin.pmb.referrals.status', $referral->id) }}" method="POST" data-ajax-form class="d-inline">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="is_active" value="{{ $referral->is_active ? 0 : 1 }}">
                                    <button class="btn btn-sm btn-outline-primary" type="submit">{{ $referral->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                                </form>
                                <button type="button" class="btn btn-sm btn-outline-danger" data-pmb-delete data-delete-url="{{ route('admin.pmb.referrals.destroy', $referral->id) }}">Hapus</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
