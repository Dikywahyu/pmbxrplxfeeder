<div class="alert alert-info border-0 shadow-sm mb-3">
    <i class="ti ti-info-circle me-1"></i> Kelola akun marketing, link referral, dan harga SPP khusus dalam satu halaman.
</div>

<ul class="nav nav-pills pmb-fee-tabs gap-2 mb-4" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="pmb-referrals-marketing-tab" data-bs-toggle="pill" data-bs-target="#pmb-referrals-marketing-pane" type="button" role="tab" aria-controls="pmb-referrals-marketing-pane" aria-selected="true">
            <i class="ti ti-users me-1"></i>Marketing & Referral <span class="badge rounded-pill bg-white text-primary ms-1">{{ $referrals->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pmb-referrals-spp-tab" data-bs-toggle="pill" data-bs-target="#pmb-referrals-spp-pane" type="button" role="tab" aria-controls="pmb-referrals-spp-pane" aria-selected="false">
            <i class="ti ti-currency-rupiah me-1"></i>Harga SPP <span class="badge rounded-pill bg-white text-primary ms-1">{{ $sppRates->count() }}</span>
        </button>
    </li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="pmb-referrals-marketing-pane" role="tabpanel" aria-labelledby="pmb-referrals-marketing-tab">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary-subtle border-0"><h5 class="card-title mb-1">Tambah marketing</h5><small class="text-muted">Tautkan akun pengguna ke kode referral. Pendaftar dari link tersebut akan tercatat pada akun marketing.</small></div>
            <div class="card-body">
                <form action="{{ route('admin.pmb.referrals.store') }}" method="POST" data-ajax-form class="row g-3 align-items-end">
                    @csrf
                    <div class="col-12 col-lg-8">
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
                    <div class="col-12 col-lg-4"><button class="btn btn-primary w-100" type="submit"><i class="ti ti-user-plus me-1"></i>Buat kode referral</button></div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0"><h5 class="card-title mb-1">Daftar marketing</h5><small class="text-muted">Salin link referral atau ubah status akun marketing.</small></div>
            <div class="card-body">
                <div class="table-responsive"><table class="table table-hover align-middle pmb-data-table w-100">
                    <thead class="table-light"><tr><th>Marketing</th><th>Email</th><th>Kode referral</th><th>Link referral</th><th>Pendaftar</th><th>Status</th><th data-orderable="false" data-searchable="false">Aksi</th></tr></thead>
                    <tbody>
                        @foreach($referrals as $referral)
                            @php($link = route('pmb.index', ['ref' => $referral->kode_referral]))
                            <tr>
                                <td class="fw-semibold">{{ $referral->owner?->name ?? 'Akun dihapus' }}</td>
                                <td>{{ $referral->owner?->email ?? '—' }}</td>
                                <td><code>{{ $referral->kode_referral }}</code></td>
                                <td><button type="button" class="btn btn-sm btn-light-primary text-primary" data-copy-link="{{ $link }}"><i class="ti ti-copy me-1"></i>Salin link</button></td>
                                <td><span class="badge bg-primary-subtle text-primary">{{ $referral->registrations_count }}</span></td>
                                <td><span class="badge {{ $referral->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">{{ $referral->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                                <td class="text-nowrap">
                                    <button type="button" class="btn btn-sm btn-light-primary text-primary mb-1" data-marketing-spp-edit data-payload="{{ json_encode(['referral_id' => (string) $referral->id, 'marketing_name' => $referral->owner?->name ?? 'Marketing', 'periods' => $periods->map(fn($period) => [(string) $period->id, $period->nama_periode])->values(), 'paths' => $paths->map(fn($path) => [(string) $path->id, $path->nama_jalur])->values()]) }}"><i class="ti ti-currency-rupiah me-1"></i>Atur SPP</button>
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
                </table></div>
                @if($referrals->isEmpty())<div class="text-center text-muted py-3">Belum ada marketing. Tambahkan akun di atas untuk membuat kode referral.</div>@endif
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="pmb-referrals-spp-pane" role="tabpanel" aria-labelledby="pmb-referrals-spp-tab">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary-subtle border-0"><h5 class="card-title mb-1"><i class="ti ti-currency-rupiah me-2 text-primary"></i>Atur harga SPP massal</h5><small class="text-muted">Harga akan diterapkan ke semua program studi pada periode dan jalur yang dipilih untuk marketing tersebut.</small></div>
            <div class="card-body">
                <form action="{{ route('admin.pmb.referrals.spp-rates.bulk') }}" method="POST" data-ajax-form class="row g-3 align-items-end">
                    @csrf
                    <div class="col-12 col-md-6 col-xl-3"><label class="form-label" for="spp-bulk-referral">Marketing</label><select class="form-select" id="spp-bulk-referral" name="referral_id" required><option value="">Pilih marketing</option>@foreach($referrals as $referral)<option value="{{ $referral->id }}">{{ $referral->owner?->name }} ({{ $referral->kode_referral }})</option>@endforeach</select></div>
                    <div class="col-12 col-md-6 col-xl-2"><label class="form-label" for="spp-bulk-period">Periode</label><select class="form-select" id="spp-bulk-period" name="period_id" required><option value="">Pilih periode</option>@foreach($periods as $period)<option value="{{ $period->id }}">{{ $period->nama_periode }}</option>@endforeach</select></div>
                    <div class="col-12 col-md-6 col-xl-2"><label class="form-label" for="spp-bulk-path">Jalur</label><select class="form-select" id="spp-bulk-path" name="path_id" required><option value="">Pilih jalur</option>@foreach($paths as $path)<option value="{{ $path->id }}">{{ $path->nama_jalur }}</option>@endforeach</select></div>
                    <div class="col-12 col-md-8 col-xl-3"><label class="form-label" for="spp-bulk-amount">Harga SPP (Rp)</label><input class="form-control" id="spp-bulk-amount" type="number" name="nominal_spp" min="0" step="1000" required></div>
                    <div class="col-12 col-md-4 col-xl-2 d-grid"><button class="btn btn-primary text-nowrap" type="submit"><i class="ti ti-device-floppy me-1"></i>Terapkan</button></div>
                    <div class="col-12"><small class="text-muted">Harga massal memperbarui semua prodi yang sudah memiliki konfigurasi biaya untuk kombinasi periode dan jalur tersebut.</small></div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0"><h5 class="card-title mb-1">Daftar harga khusus marketing</h5><small class="text-muted">Gunakan tombol Ubah pada baris untuk mengedit satu kombinasi harga melalui modal.</small></div>
            <div class="card-body">
                <div class="table-responsive"><table class="table table-hover align-middle pmb-data-table w-100">
                    <thead class="table-light"><tr><th>Marketing</th><th>Periode</th><th>Jalur</th><th>Program studi</th><th>SPP standar</th><th>SPP khusus</th><th data-orderable="false" data-searchable="false">Aksi</th></tr></thead>
                    <tbody>
                        @foreach($sppRates as $rate)
                            <tr>
                                <td>{{ $rate->referral?->owner?->name }} <small class="text-muted d-block">{{ $rate->referral?->kode_referral }}</small></td>
                                <td>{{ $rate->fee?->period?->nama_periode }}</td><td>{{ $rate->fee?->path?->nama_jalur }}</td><td>{{ $rate->fee?->program?->nama_prodi }}</td>
                                <td>Rp {{ number_format((float) ($rate->fee?->nominal_spp ?? 0), 0, ',', '.') }}</td>
                                <td><span class="badge rounded-pill bg-success-subtle text-success">Rp {{ number_format((float) $rate->nominal_spp, 0, ',', '.') }}</span></td>
                                <td class="text-nowrap"><button type="button" class="btn btn-sm btn-light-primary text-primary" data-pmb-edit data-edit-schema="referralSppRate" data-update-url="{{ route('admin.pmb.referrals.spp-rates.update', $rate->id) }}" data-payload="{{ json_encode(['record' => ['nominal_spp' => $rate->nominal_spp]]) }}"><i class="ti ti-edit"></i> Ubah</button> <button type="button" class="btn btn-sm btn-light-danger text-danger" data-pmb-delete data-delete-url="{{ route('admin.pmb.referrals.spp-rates.destroy', $rate->id) }}"><i class="ti ti-trash"></i> Hapus</button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table></div>
                @if($sppRates->isEmpty())<div class="text-center text-muted py-3">Belum ada harga khusus. Pendaftar memakai tarif SPP standar PMB.</div>@endif
            </div>
        </div>
    </div>
</div>
