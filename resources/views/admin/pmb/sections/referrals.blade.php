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
            <div class="card-header bg-primary-subtle border-0">
                <h5 class="card-title mb-1">Daftarkan marketing baru</h5><small class="text-muted">Siapkan data dan tarifnya sekarang. Marketing membuat akun sendiri melalui link klaim WhatsApp.</small>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.pmb.referrals.store') }}" method="POST" data-ajax-form class="row g-3 align-items-end">
                    @csrf
                    <div class="col-12 col-md-4"><label class="form-label" for="referral-marketing-name">Nama marketing</label><input class="form-control" id="referral-marketing-name" name="marketing_name" maxlength="255" required></div>
                    <div class="col-12 col-md-4"><label class="form-label" for="referral-whatsapp">Nomor WhatsApp</label><input class="form-control" id="referral-whatsapp" name="whatsapp" type="tel" placeholder="08xxxxxxxxxx" maxlength="30" required></div>
                    <div class="col-12 col-md-4"><label class="form-label" for="referral-contact-email">Email (opsional)</label><input class="form-control" id="referral-contact-email" name="contact_email" type="email" maxlength="255" placeholder="Email akun dapat diisi saat klaim"></div>
                    <div class="col-12"><button class="btn btn-primary" type="submit"><i class="ti ti-link me-1"></i>Siapkan data & link klaim</button></div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-success-subtle border-0 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 py-3">
                <div>
                    <h5 class="card-title mb-1">
                        <i class="ti ti-file-upload me-2 text-success"></i>Import Data Marketing
                    </h5>
                    <small class="text-muted">Upload CSV dari Excel untuk menambahkan banyak marketing sekaligus. Setiap baris akan mendapat kode referral dan link klaim baru.</small>
                </div>
                <a href="{{ route('admin.pmb.referrals.import-template') }}" class="btn btn-sm btn-outline-success bg-white text-nowrap align-self-start align-self-sm-center shadow-xs">
                    <i class="ti ti-download me-1"></i>Unduh Template CSV
                </a>
            </div>

            <div class="card-body">
                <form action="{{ route('admin.pmb.referrals.import') }}" method="POST" enctype="multipart/form-data" data-ajax-form>
                    @csrf
                    <label class="form-label fw-medium" for="referral-import-file">File CSV <span class="text-danger">*</span></label>

                    <div class="row g-2 align-items-center">
                        <div class="col-12 col-md-8 col-lg-9">
                            <input class="form-control" id="referral-import-file" name="file" type="file" accept=".csv,.txt,text/csv" required>
                        </div>
                        <div class="col-12 col-md-4 col-lg-3">
                            <button class="btn btn-success w-100 d-inline-flex align-items-center justify-content-center" type="submit">
                                <i class="ti ti-upload me-2"></i>Import Marketing
                            </button>
                        </div>
                    </div>

                    <div class="form-text text-muted mt-2">
                        <i class="ti ti-info-circle me-1"></i>Header wajib: <code>nama_marketing,whatsapp</code> | Opsional: <code>email</code> (Pemisah koma <code>,</code> atau titik koma <code>;</code> didukung).
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h5 class="card-title mb-1">Daftar marketing</h5><small class="text-muted">Pantau klaim akun, bagikan link referral, dan atur status marketing.</small>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle pmb-data-table w-100">
                        <thead class="table-light">
                            <tr>
                                <th>Marketing</th>
                                <th>Email / WhatsApp</th>
                                <th>Kode referral</th>
                                <th>Link referral</th>
                                <th>Pendaftar</th>
                                <th>Akun</th>
                                <th>Status</th>
                                <th data-orderable="false" data-searchable="false">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($referrals as $referral)
                            @php($link = route('pmb.index', ['ref' => $referral->kode_referral]))
                            <tr>
                                <td class="fw-semibold">{{ $referral->owner?->name ?? $referral->marketing_name ?? 'Akun dihapus' }}@if($referral->whatsapp)<small class="text-muted d-block">WA {{ $referral->whatsapp }}</small>@endif</td>
                                <td>{{ $referral->owner?->email ?? $referral->contact_email ?? $referral->whatsapp ?? "-" }}</td>
                                <td><code>{{ $referral->kode_referral }}</code></td>
                                <td><button type="button" class="btn btn-sm btn-light-primary text-primary" data-copy-link="{{ $link }}"><i class="ti ti-copy me-1"></i>Salin link</button></td>
                                <td><span class="badge bg-primary-subtle text-primary">{{ $referral->registrations_count }}</span></td>
                                <td><span class="badge {{ $referral->owner_id ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning-emphasis' }}">{{ $referral->owner_id ? 'Diklaim' : 'Belum diklaim' }}</span></td>
                                <td><span class="badge {{ $referral->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">{{ $referral->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                                <td class="text-nowrap">
                                    <button type="button" class="btn btn-sm btn-outline-secondary mb-1" data-pmb-edit data-edit-schema="referralContact" data-update-url="{{ route('admin.pmb.referrals.whatsapp.update', $referral->id) }}" data-payload="{{ json_encode(['record' => ['whatsapp' => $referral->whatsapp]]) }}"><i class="ti ti-phone me-1"></i>Edit WA</button>
                                    <button type="button" class="btn btn-sm btn-light-primary text-primary mb-1" data-marketing-spp-edit data-payload="{{ json_encode(['referral_id' => (string) $referral->id, 'marketing_name' => $referral->owner?->name ?? $referral->marketing_name ?? 'Marketing', 'periods' => $periods->map(fn($period) => [(string) $period->id, $period->nama_periode])->values(), 'paths' => $paths->map(fn($path) => [(string) $path->id, $path->nama_jalur])->values(), 'programs' => $programs->map(fn($program) => [(string) $program->id, $program->nama_prodi])->values()]) }}"><i class="ti ti-currency-rupiah me-1"></i>Atur SPP</button>
                                    @unless($referral->owner_id)<button type="button" class="btn btn-sm btn-outline-success mb-1" data-marketing-invite-link data-link-url="{{ route('admin.pmb.referrals.claim-link', $referral->id) }}"><i class="ti ti-brand-whatsapp me-1"></i>Buat link klaim</button>@endunless
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
                @if($referrals->isEmpty())<div class="text-center text-muted py-3">Belum ada marketing. Daftarkan marketing untuk membuat kode referral dan link klaim.</div>@endif
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="pmb-referrals-spp-pane" role="tabpanel" aria-labelledby="pmb-referrals-spp-tab">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary-subtle border-0">
                <h5 class="card-title mb-1"><i class="ti ti-currency-rupiah me-2 text-primary"></i>Atur harga SPP massal</h5><small class="text-muted">Gunakan tombol Atur SPP pada tabel marketing untuk mengatur nominal berbeda per program studi. Form ini tetap tersedia untuk menerapkan satu nominal ke semua prodi.</small>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.pmb.referrals.spp-rates.bulk') }}" method="POST" data-ajax-form class="row g-3 align-items-end">
                    @csrf
                    <div class="col-12 col-md-6 col-xl-3"><label class="form-label" for="spp-bulk-referral">Marketing</label><select class="form-select" id="spp-bulk-referral" name="referral_id" required>
                            <option value="">Pilih marketing</option>@foreach($referrals as $referral)<option value="{{ $referral->id }}">{{ $referral->owner?->name ?? $referral->marketing_name ?? 'Marketing' }} ({{ $referral->kode_referral }})</option>@endforeach
                        </select></div>
                    <div class="col-12 col-md-6 col-xl-2"><label class="form-label" for="spp-bulk-period">Periode</label><select class="form-select" id="spp-bulk-period" name="period_id" required>
                            <option value="">Pilih periode</option>@foreach($periods as $period)<option value="{{ $period->id }}">{{ $period->nama_periode }}</option>@endforeach
                        </select></div>
                    <div class="col-12 col-md-6 col-xl-2"><label class="form-label" for="spp-bulk-path">Jalur</label><select class="form-select" id="spp-bulk-path" name="path_id" required>
                            <option value="">Pilih jalur</option>@foreach($paths as $path)<option value="{{ $path->id }}">{{ $path->nama_jalur }}</option>@endforeach
                        </select></div>
                    <div class="col-12 col-md-8 col-xl-3"><label class="form-label" for="spp-bulk-amount">Harga SPP (Rp)</label><input class="form-control" id="spp-bulk-amount" type="number" name="nominal_spp" min="0" step="1000" required></div>
                    <div class="col-12 col-md-4 col-xl-2 d-grid"><button class="btn btn-primary text-nowrap" type="submit"><i class="ti ti-device-floppy me-1"></i>Terapkan</button></div>
                    <div class="col-12"><small class="text-muted">Harga massal memperbarui semua prodi yang sudah memiliki konfigurasi biaya untuk kombinasi periode dan jalur tersebut.</small></div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h5 class="card-title mb-1">Daftar harga khusus marketing</h5><small class="text-muted">Gunakan tombol Ubah pada baris untuk mengedit satu kombinasi harga melalui modal.</small>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle pmb-data-table w-100">
                        <thead class="table-light">
                            <tr>
                                <th>Marketing</th>
                                <th>Periode</th>
                                <th>Jalur</th>
                                <th>Program studi</th>
                                <th>SPP standar</th>
                                <th>SPP khusus</th>
                                <th data-orderable="false" data-searchable="false">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sppRates as $rate)
                            <tr>
                                <td>{{ $rate->referral?->owner?->name ?? $rate->referral?->marketing_name ?? 'Marketing' }} <small class="text-muted d-block">{{ $rate->referral?->kode_referral }}</small></td>
                                <td>{{ $rate->fee?->period?->nama_periode }}</td>
                                <td>{{ $rate->fee?->path?->nama_jalur }}</td>
                                <td>{{ $rate->fee?->program?->nama_prodi }}</td>
                                <td>Rp {{ number_format((float) ($rate->fee?->nominal_spp ?? 0), 0, ',', '.') }}</td>
                                <td><span class="badge rounded-pill bg-success-subtle text-success">Rp {{ number_format((float) $rate->nominal_spp, 0, ',', '.') }}</span></td>
                                <td class="text-nowrap"><button type="button" class="btn btn-sm btn-light-primary text-primary" data-pmb-edit data-edit-schema="referralSppRate" data-update-url="{{ route('admin.pmb.referrals.spp-rates.update', $rate->id) }}" data-payload="{{ json_encode(['record' => ['nominal_spp' => $rate->nominal_spp]]) }}"><i class="ti ti-edit"></i> Ubah</button> <button type="button" class="btn btn-sm btn-light-danger text-danger" data-pmb-delete data-delete-url="{{ route('admin.pmb.referrals.spp-rates.destroy', $rate->id) }}"><i class="ti ti-trash"></i> Hapus</button></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($sppRates->isEmpty())<div class="text-center text-muted py-3">Belum ada harga khusus. Pendaftar memakai tarif SPP standar PMB.</div>@endif
            </div>
        </div>
    </div>
</div>
