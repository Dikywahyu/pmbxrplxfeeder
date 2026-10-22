<div class="row g-3">
    <div class="col-12">
        <div class="alert alert-primary border-0 d-flex gap-2 align-items-start mb-0">
            <i class="ti ti-info-circle fs-5"></i>
            <div>Masukkan data calon mahasiswa dan pilihan pendaftarannya. NIK digunakan saat klaim akun. Marketing juga dapat membantu mengunggah berkas sesuai jalur yang dipilih.</div>
        </div>
    </div>

    @unless($referral)
        <div class="col-12"><div class="alert alert-warning mb-0">Akun marketing Anda belum memiliki kode referral aktif. Hubungi Admin PMB agar kode referral diaktifkan.</div></div>
    @else
        <div class="col-12 d-flex justify-content-end">
            <button type="button" class="btn btn-primary" data-marketing-form-toggle="show"><i class="ti ti-user-plus me-1"></i>Tambah calon mahasiswa</button>
        </div>
        <div class="col-12">
            <div class="card border mb-0 d-none" id="marketing-registration-form-panel" hidden>
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center"><h5 class="card-title mb-0"><i class="ti ti-user-plus me-2 text-primary"></i>Input calon mahasiswa</h5><button type="button" class="btn btn-sm btn-outline-secondary" data-marketing-form-toggle="hide"><i class="ti ti-x me-1"></i>Tutup</button></div>
                <div class="card-body">
                    @if($fees->isEmpty())
                        <div class="alert alert-warning mb-0">Belum ada kombinasi periode, jadwal jalur, program studi, dan biaya yang sedang dibuka.</div>
                    @else
                        <form action="{{ route('admin.pmb.marketing.registrations.store') }}" method="POST" enctype="multipart/form-data" class="row g-3" data-marketing-registration-form>
                            @csrf
                            @php($marketingRequiredBio = [
                                ['nama_mahasiswa', 'Nama lengkap sesuai identitas', 'text', 255],
                                ['nik', 'NIK mahasiswa', 'text', 16],
                                ['tempat_lahir', 'Tempat lahir', 'text', 255],
                                ['tanggal_lahir', 'Tanggal lahir', 'date'],
                                ['handphone', 'Nomor HP', 'text', 20],
                                ['jalan', 'Jalan / alamat', 'text', 255],
                                ['kelurahan', 'Kelurahan / desa', 'text', 255],
                            ])
                            <div class="col-12">
                                <div class="p-3 rounded-3 border border-primary-subtle bg-primary-subtle">
                                    <h6 class="text-primary mb-1"><i class="ti ti-id me-1"></i>Biodata utama <span class="badge bg-primary">Wajib</span></h6>
                                    <p class="small text-muted mb-3">Lengkapi data dasar pendaftar. Semua kolom di bagian ini wajib diisi.</p>
                                    <div class="row g-2">
                                        @foreach($marketingRequiredBio as $field)
                                            @php($fieldId = 'marketing-'.str_replace('_', '-', $field[0]))
                                            <div class="col-6 col-md-4">
                                                <label class="form-label" for="{{ $fieldId }}">{{ $field[1] }} *</label>
                                                <input class="form-control" id="{{ $fieldId }}" name="{{ $field[0] }}" type="{{ $field[2] }}" @if(isset($field[3]) && $field[0] !== 'nik') maxlength="{{ $field[3] }}" @endif @if($field[0] === 'nik') inputmode="numeric" minlength="16" maxlength="16" pattern="[0-9]{16}" @endif @if($field[2] === 'date') max="{{ today()->subDay()->toDateString() }}" @endif required>
                                            </div>
                                        @endforeach
                                        <div class="col-6 col-md-4"><label class="form-label" for="marketing-gender">Jenis kelamin *</label><select class="form-select" id="marketing-gender" name="jenis_kelamin" required><option value="">Pilih</option><option value="L">Laki-laki</option><option value="P">Perempuan</option></select></div>
                                        <div class="col-12 col-md-6"><label class="form-label" for="marketing-nama-ibu-kandung-main">Nama ibu kandung *</label><input class="form-control" id="marketing-nama-ibu-kandung-main" name="nama_ibu_kandung" maxlength="255" required></div>
                                    </div>
                                </div>
                            </div>

                            @php($marketingOptionalBio = [
                                ['nisn', 'NISN', 'text', 20], ['npwp', 'NPWP', 'text', 25],
                                ['telepon', 'Telepon', 'text', 20], ['dusun', 'Dusun', 'text', 255], ['rt', 'RT', 'text', 5], ['rw', 'RW', 'text', 5],
                                ['kode_pos', 'Kode pos', 'text', 10], ['id_negara', 'Kode negara', 'text', 5],
                            ])
                            <div class="col-12">
                                <details class="marketing-optional-section">
                                    <summary><i class="ti ti-address-book me-1 text-info"></i>Data biodata tambahan <span class="text-muted fw-normal">(opsional)</span></summary>
                                    <div class="marketing-optional-body"><div class="row g-2">
                                        <div class="col-12 col-md-6"><label class="form-label" for="marketing-email">Email kontak</label><input class="form-control" id="marketing-email" type="email" name="email" maxlength="255"><small class="text-muted">Mahasiswa dapat memakai email akun yang berbeda saat klaim.</small></div>
                                        <div class="col-6 col-md-3"><label class="form-label" for="marketing-id-agama">Agama</label><select class="form-select" id="marketing-id-agama" name="id_agama"><option value="">Pilih agama</option>@foreach($agamaOptions as $agama)<option value="{{ $agama->id_agama }}">{{ $agama->nama_agama }}</option>@endforeach</select></div>
                                        <div class="col-6 col-md-3"><label class="form-label" for="marketing-id-transport">Alat transportasi</label><select class="form-select" id="marketing-id-transport" name="id_alat_transportasi"><option value="">Pilih transportasi</option>@foreach($transportOptions as $transport)<option value="{{ $transport->id_alat_transportasi }}">{{ $transport->nama_alat_transportasi }}</option>@endforeach</select></div>
                                        <div class="col-12 col-md-6"><label class="form-label" for="marketing-id-wilayah">Kecamatan</label><select class="form-select" id="marketing-id-wilayah" name="id_wilayah" data-wilayah-select data-ajax-url="{{ route('pmb.reference.wilayahs') }}"><option value="">Cari nama kecamatan...</option></select><small class="text-muted">Ketik minimal 2 karakter.</small></div>
                                        @foreach($marketingOptionalBio as $field)
                                            @php($fieldId = 'marketing-'.str_replace('_', '-', $field[0]))
                                            <div class="col-6 col-md-3"><label class="form-label" for="{{ $fieldId }}">{{ $field[1] }}</label><input class="form-control" id="{{ $fieldId }}" name="{{ $field[0] }}" type="{{ $field[2] }}" maxlength="{{ $field[3] }}" @if($field[0] === 'id_negara') value="ID" @endif></div>
                                        @endforeach
                                    </div></div>
                                </details>
                            </div>

                            <div class="col-12">
                                <details class="marketing-optional-section">
                                    <summary><i class="ti ti-heart-handshake me-1 text-warning"></i>Data KIP dan kesejahteraan <span class="text-muted fw-normal">(opsional)</span></summary>
                                    <div class="marketing-optional-body"><div class="row g-2">
                                        @foreach([['nomor_daftar_kip','Nomor daftar KIP',90],['nomor_kip','Nomor KIP',50],['nomor_kks','Nomor KKS',50],['nomor_kps','Nomor KPS / PKH',50]] as $field)
                                            <div class="col-6 col-md-3"><label class="form-label" for="marketing-{{ $field[0] }}">{{ $field[1] }}</label><input class="form-control" id="marketing-{{ $field[0] }}" name="{{ $field[0] }}" maxlength="{{ $field[2] }}"></div>
                                        @endforeach
                                        <div class="col-6 col-md-3"><label class="form-label" for="marketing-penerima-kps">Penerima KPS</label><select class="form-select" id="marketing-penerima-kps" name="penerima_kps"><option value="0">Tidak</option><option value="1">Ya</option></select></div>
                                        <div class="col-6 col-md-3"><label class="form-label" for="marketing-dtks">Terdaftar DTKS</label><select class="form-select" id="marketing-dtks" name="terdaftar_dtks"><option value="0">Tidak</option><option value="1">Ya</option></select></div>
                                    </div></div>
                                </details>
                            </div>

                            @php($marketingFamilyFields = [
                                ['nik_ayah','NIK ayah','text',20], ['nama_ayah','Nama ayah','text',255], ['tanggal_lahir_ayah','Tanggal lahir ayah','date'], ['id_pendidikan_ayah','Kode pendidikan ayah','text',5], ['nama_pendidikan_ayah','Pendidikan ayah','text',255], ['id_pekerjaan_ayah','Kode pekerjaan ayah','text',5], ['nama_pekerjaan_ayah','Pekerjaan ayah','text',255], ['id_penghasilan_ayah','Kode penghasilan ayah','text',5], ['nama_penghasilan_ayah','Penghasilan ayah','text',255],
                                ['nik_ibu','NIK ibu','text',20], ['tanggal_lahir_ibu','Tanggal lahir ibu','date'], ['id_pendidikan_ibu','Kode pendidikan ibu','text',5], ['nama_pendidikan_ibu','Pendidikan ibu','text',255], ['id_pekerjaan_ibu','Kode pekerjaan ibu','text',5], ['nama_pekerjaan_ibu','Pekerjaan ibu','text',255], ['id_penghasilan_ibu','Kode penghasilan ibu','text',5], ['nama_penghasilan_ibu','Penghasilan ibu','text',255],
                                ['nama_wali','Nama wali','text',255], ['tanggal_lahir_wali','Tanggal lahir wali','date'], ['id_pendidikan_wali','Kode pendidikan wali','text',5], ['nama_pendidikan_wali','Pendidikan wali','text',255], ['id_pekerjaan_wali','Kode pekerjaan wali','text',5], ['nama_pekerjaan_wali','Pekerjaan wali','text',255], ['id_penghasilan_wali','Kode penghasilan wali','text',5], ['nama_penghasilan_wali','Penghasilan wali','text',255],
                            ])
                            <div class="col-12">
                                <details class="marketing-optional-section">
                                    <summary><i class="ti ti-users me-1 text-success"></i>Data keluarga <span class="text-muted fw-normal">(opsional)</span></summary>
                                    <div class="marketing-optional-body"><div class="row g-2">
                                        @foreach($marketingFamilyFields as $field)
                                            @php($fieldId = 'marketing-'.str_replace('_', '-', $field[0]))
                                            @if(str_starts_with($field[0], 'id_penghasilan_'))
                                                @php($incomeNameField = str_replace('id_', 'nama_', $field[0]))
                                                <div class="col-6 col-md-4"><label class="form-label" for="{{ $fieldId }}">{{ str_replace('Kode penghasilan', 'Penghasilan', $field[1]) }}</label><select class="form-select" id="{{ $fieldId }}" name="{{ $field[0] }}" data-income-select="{{ $field[0] }}"><option value="">Pilih penghasilan</option>@foreach($incomeOptions as $income)<option value="{{ $income->id_penghasilan }}" data-name="{{ $income->nama_penghasilan }}">{{ $income->nama_penghasilan }}</option>@endforeach</select><input type="hidden" name="{{ $incomeNameField }}" data-income-name="{{ $field[0] }}"></div>
                                            @elseif(str_starts_with($field[0], 'nama_penghasilan_'))
                                            @else
                                            <div class="col-6 col-md-4"><label class="form-label" for="{{ $fieldId }}">{{ $field[1] }}</label><input class="form-control" id="{{ $fieldId }}" name="{{ $field[0] }}" type="{{ $field[2] }}" @if(isset($field[3])) maxlength="{{ $field[3] }}" @endif @if($field[2] === 'date') max="{{ today()->subDay()->toDateString() }}" @endif></div>
                                            @endif
                                        @endforeach
                                    </div></div>
                                </details>
                            </div>
                            <div class="col-12"><label class="form-label" for="marketing-fee">Periode, jalur, dan program studi</label><select class="form-select" id="marketing-fee" name="fee_id" required><option value="">Pilih pendaftaran</option>@foreach($fees as $fee)<option value="{{ $fee->id }}" data-path-id="{{ $fee->path_id }}">{{ $fee->period?->nama_periode }} · {{ $fee->path?->nama_jalur }} · {{ $fee->program?->nama_prodi }} ({{ $fee->program?->jenjang }}) — Rp {{ number_format($fee->nominal_pendaftaran, 0, ',', '.') }}</option>@endforeach</select></div>
                            @php($marketingRequirements = $fees->pluck('path.documentRequirements')->flatten()->unique('id'))
                            @if($marketingRequirements->isNotEmpty())
                                <div class="col-12">
                                    <div class="border rounded p-3 bg-light">
                                        <div class="fw-semibold mb-1">Berkas pendaftaran <span class="text-muted fw-normal">(opsional, maksimal 10 MB per berkas)</span></div>
                                        <div class="small text-muted mb-3">Pilih dokumen yang sudah diterima. Jenis berkas akan mengikuti jalur pendaftaran.</div>
                                        <div class="row g-3" data-marketing-documents>
                                            @foreach($marketingRequirements as $requirement)
                                                <div class="col-md-6" data-requirement-path="{{ $requirement->path_id }}" hidden>
                                                    <label class="form-label" for="marketing-document-{{ $requirement->id }}">
                                                        {{ $requirement->nama_dokumen }}
                                                        @if($requirement->is_required)
                                                            <span class="text-danger">*</span>
                                                        @endif
                                                    </label>
                                                    <input class="form-control" id="marketing-document-{{ $requirement->id }}" type="file" name="documents[{{ $requirement->id }}]" accept="{{ collect(explode(',', $requirement->format_file))->map(fn($ext) => '.'.trim($ext))->implode(',') }}">
                                                    <small class="text-muted">
                                                        Format: {{ str_replace(',', ', ', $requirement->format_file) }}
                                                        @if($requirement->is_required)
                                                            · Persyaratan wajib
                                                        @else
                                                            · Opsional
                                                        @endif
                                                    </small>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="col-12 d-flex justify-content-end"><button class="btn btn-primary px-4" type="submit"><span class="small me-2" data-registration-loading hidden><span class="spinner-border spinner-border-sm me-1" role="status"></span>Menyimpan...</span><i class="ti ti-link me-1"></i>Buat pendaftaran dan link klaim</button></div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @endunless

    <div class="col-12">
        <div class="row g-3">
            <div class="col-12 col-xl-8">
                <div class="card border h-100">
                    <div class="card-header bg-transparent"><h5 class="card-title mb-0"><i class="ti ti-route me-2 text-primary"></i>Tahapan pendaftar referral</h5></div>
                    <div class="card-body"><div class="row g-2">
                        <div class="col-4 col-md-4"><div class="marketing-stage-tile rounded-3 p-2 p-md-3 h-100 bg-info-subtle"><div class="small text-muted">Menunggu klaim</div><div class="fs-4 fw-bold text-info">{{ $marketingPendingClaims }}</div></div></div>
                        @foreach($marketingStages as $stage)
                            <div class="col-4 col-md-4"><div class="marketing-stage-tile rounded-3 p-2 p-md-3 h-100 border bg-light"><div class="small text-muted">{{ $stage['label'] }}</div><div class="fs-4 fw-bold text-primary">{{ $stage['total'] }}</div></div></div>
                        @endforeach
                    </div></div>
                </div>
            </div>
            <div class="col-12 col-xl-4">
                <div class="card border h-100">
                    <div class="card-header bg-transparent"><h5 class="card-title mb-0"><i class="ti ti-chart-donut me-2 text-success"></i>Distribusi program studi</h5></div>
                    <div class="card-body"><div id="pmb-marketing-program-chart" data-labels="{{ json_encode($marketingProgramDistribution->pluck('label')->values(), JSON_HEX_APOS | JSON_HEX_QUOT) }}" data-series="{{ json_encode($marketingProgramDistribution->pluck('total')->values()) }}"></div>
                        @if($marketingProgramDistribution->isEmpty())<div class="text-center text-muted small">Belum ada pendaftar referral.</div>@endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($marketingDocumentFixCount > 0)
        <div class="col-12">
            <div class="alert alert-danger d-flex align-items-center gap-2 mb-0" role="alert">
                <i class="ti ti-alert-triangle fs-5"></i>
                <div><strong>{{ $marketingDocumentFixCount }} pendaftar</strong> memiliki berkas yang perlu diperbaiki. Buka detail pendaftar untuk melihat catatan verifikator dan bantu unggah ulang berkasnya.</div>
            </div>
        </div>
    @endif
    @if($marketingPaymentRejectedCount > 0)
        <div class="col-12">
            <div class="alert alert-warning d-flex align-items-center gap-2 mb-0" role="alert">
                <i class="ti ti-receipt-off fs-5"></i>
                <div><strong>{{ $marketingPaymentRejectedCount }} pendaftar</strong> memiliki bukti pembayaran yang ditolak. Buka detail pendaftar untuk melihat status dan membantu mengunggah bukti pengganti.</div>
            </div>
        </div>
    @endif

    <div class="col-12">
        <div class="card border mb-0">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center"><h5 class="card-title mb-0">Pendaftar dari referral Anda</h5><span class="badge bg-primary-subtle text-primary">{{ $registrations->count() }} pendaftar</span></div>
            <div class="card-body"><div class="table-responsive pmb-marketing-table-wrap">
                <table class="table table-hover align-middle pmb-data-table w-100">
                    <thead class="table-light"><tr><th>Mahasiswa</th><th>Periode dan pilihan</th><th>Nomor pendaftaran</th><th>Akun klaim</th><th data-orderable="false" data-searchable="false">Detail</th><th data-orderable="false" data-searchable="false">Aksi</th></tr></thead>
                    <tbody>
                        @foreach($registrations as $registration)
                            @php($claim = $registration->candidate?->claim)
                            @php($documentsNeedingFix = $registration->documents->where('status_verifikasi', 'invalid')->count())
                            @php($paymentsRejected = $registration->payments->where('status', 'ditolak')->count())
                            <tr>
                                <td><strong>{{ $registration->candidate?->nama_mahasiswa }}</strong><br><small class="text-muted">NIK: {{ $registration->candidate?->nik }} · {{ $registration->candidate?->email }} · {{ $registration->candidate?->handphone }}</small></td>
                                <td>{{ $registration->period?->nama_periode }}<br><small class="text-muted">{{ $registration->path?->nama_jalur }} · {{ $registration->program1?->nama_prodi }}</small></td>
                                <td><code>{{ $registration->nomor_pendaftaran }}</code></td>
                                <td>
                                    @if($registration->candidate?->user_id || $claim?->claimed_at)
                                        <span class="badge bg-success-subtle text-success"><i class="ti ti-circle-check me-1"></i>Sudah diklaim</span>
                                    @elseif($claim?->expires_at?->isPast())
                                        <span class="badge bg-warning-subtle text-warning"><i class="ti ti-clock me-1"></i>Link kedaluwarsa</span>
                                    @else
                                        <span class="badge bg-info-subtle text-info"><i class="ti ti-hourglass me-1"></i>Menunggu klaim</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm {{ $documentsNeedingFix || $paymentsRejected ? 'btn-outline-danger' : 'btn-outline-secondary' }}" data-marketing-registration-detail data-detail-url="{{ route('admin.pmb.marketing.registrations.show', $registration->id) }}" data-focus-tab="{{ $paymentsRejected ? 'marketing-detail-payments' : ($documentsNeedingFix ? 'marketing-detail-documents' : '') }}"><i class="ti {{ $documentsNeedingFix || $paymentsRejected ? 'ti-alert-triangle' : 'ti-user-search' }} me-1"></i>Detail @if($documentsNeedingFix)<span class="badge bg-danger ms-1">{{ $documentsNeedingFix }} berkas perlu perbaikan</span>@endif @if($paymentsRejected)<span class="badge bg-warning text-dark ms-1">{{ $paymentsRejected }} pembayaran ditolak</span>@endif</button>
                                </td>
                                <td>
                                    @if($claim && ! $claim->claimed_at && ! $registration->candidate?->user_id)
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-marketing-claim-link data-link-url="{{ route('admin.pmb.marketing.registrations.claim-link', $registration->id) }}"><i class="ti ti-link me-1"></i>Buat link klaim</button>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div></div>
        </div>
    </div>
</div>
