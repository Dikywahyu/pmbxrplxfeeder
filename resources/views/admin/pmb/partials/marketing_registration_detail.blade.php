@php
    $candidate = $registration->candidate;
    $biodata = [
        'Nama lengkap' => $candidate?->nama_mahasiswa,
        'NIK' => $candidate?->nik,
        'NISN' => $candidate?->nisn,
        'NPWP' => $candidate?->npwp,
        'Jenis kelamin' => $candidate?->jenis_kelamin === 'L' ? 'Laki-laki' : ($candidate?->jenis_kelamin === 'P' ? 'Perempuan' : null),
        'Tempat lahir' => $candidate?->tempat_lahir,
        'Tanggal lahir' => $candidate?->tanggal_lahir?->format('d/m/Y'),
        'Kode agama' => $candidate?->id_agama,
        'Email' => $candidate?->email,
        'Handphone' => $candidate?->handphone,
        'Telepon' => $candidate?->telepon,
        'Alamat' => collect([$candidate?->jalan, $candidate?->dusun, $candidate?->rt ? 'RT '.$candidate->rt : null, $candidate?->rw ? 'RW '.$candidate->rw : null, $candidate?->kelurahan, $candidate?->kode_pos])->filter()->implode(', '),
        'Kode wilayah' => $candidate?->id_wilayah,
        'Kode negara' => $candidate?->id_negara,
        'Periode' => $registration->period?->nama_periode,
        'Jalur' => $registration->path?->nama_jalur,
        'Program studi' => $registration->program1?->nama_prodi,
        'SPP per semester' => $registration->nominal_spp !== null ? 'Rp '.number_format((float) $registration->nominal_spp, 0, ',', '.') : null,
        'Nomor pendaftaran' => $registration->nomor_pendaftaran,
    ];
    $family = [
        'Nama ayah' => $candidate?->nama_ayah,
        'NIK ayah' => $candidate?->nik_ayah,
        'Tanggal lahir ayah' => $candidate?->tanggal_lahir_ayah,
        'Pendidikan ayah' => $candidate?->nama_pendidikan_ayah,
        'Pekerjaan ayah' => $candidate?->nama_pekerjaan_ayah,
        'Penghasilan ayah' => $candidate?->nama_penghasilan_ayah,
        'Nama ibu kandung' => $candidate?->nama_ibu_kandung,
        'NIK ibu' => $candidate?->nik_ibu,
        'Tanggal lahir ibu' => $candidate?->tanggal_lahir_ibu,
        'Pendidikan ibu' => $candidate?->nama_pendidikan_ibu,
        'Pekerjaan ibu' => $candidate?->nama_pekerjaan_ibu,
        'Penghasilan ibu' => $candidate?->nama_penghasilan_ibu,
        'Nama wali' => $candidate?->nama_wali,
        'Tanggal lahir wali' => $candidate?->tanggal_lahir_wali,
        'Pendidikan wali' => $candidate?->nama_pendidikan_wali,
        'Pekerjaan wali' => $candidate?->nama_pekerjaan_wali,
        'Penghasilan wali' => $candidate?->nama_penghasilan_wali,
    ];
    $social = [
        'Penerima KPS' => $candidate?->penerima_kps ? 'Ya' : 'Tidak',
        'Terdaftar DTKS' => $candidate?->terdaftar_dtks ? 'Ya' : 'Tidak',
        'Nomor KIP' => $candidate?->nomor_kip,
        'Nomor daftar KIP' => $candidate?->nomor_daftar_kip,
        'Nomor KKS' => $candidate?->nomor_kks,
        'Nomor KPS' => $candidate?->nomor_kps,
    ];
    $requirementCount = $registration->path?->documentRequirements->count() ?? 0;
    $uploadedDocumentCount = $registration->documents->count();
    $documentsNeedingFix = $registration->documents->where('status_verifikasi', 'invalid')->count();
    $paymentCount = $registration->payments->count();
    $paymentsRejected = $registration->payments->where('status', 'ditolak')->count();
@endphp

<div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-3">
    <div>
        <h5 class="mb-1">{{ $candidate?->nama_mahasiswa ?? 'Data calon mahasiswa' }}</h5>
        <div class="small text-muted">{{ $registration->nomor_pendaftaran }} · {{ $registration->path?->nama_jalur }} · {{ $registration->program1?->nama_prodi }}</div>
    </div>
    <span class="badge bg-primary-subtle text-primary">{{ $registration->period?->nama_periode }}</span>
</div>

<ul class="nav nav-pills gap-2 p-2 mb-3 rounded-3 bg-light" role="tablist">
    <li class="nav-item" role="presentation"><button class="nav-link active" id="marketing-detail-biodata-tab" data-bs-toggle="tab" data-bs-target="#marketing-detail-biodata" type="button" role="tab"><i class="ti ti-user me-1"></i>Biodata</button></li>
    <li class="nav-item" role="presentation"><button class="nav-link" id="marketing-detail-documents-tab" data-bs-toggle="tab" data-bs-target="#marketing-detail-documents" type="button" role="tab"><i class="ti ti-files me-1"></i>Berkas <span class="badge bg-white text-primary ms-1">{{ $uploadedDocumentCount }}/{{ $requirementCount }}</span>@if($documentsNeedingFix)<span class="badge bg-danger ms-1">{{ $documentsNeedingFix }} perlu perbaikan</span>@endif</button></li>
    <li class="nav-item" role="presentation"><button class="nav-link" id="marketing-detail-payments-tab" data-bs-toggle="tab" data-bs-target="#marketing-detail-payments" type="button" role="tab"><i class="ti ti-credit-card me-1"></i>Pembayaran <span class="badge bg-white text-primary ms-1">{{ $paymentCount }}</span>@if($paymentsRejected)<span class="badge bg-danger ms-1">{{ $paymentsRejected }} ditolak</span>@endif</button></li>
</ul>

<div class="tab-content">
<div class="tab-pane fade show active" id="marketing-detail-biodata" role="tabpanel" aria-labelledby="marketing-detail-biodata-tab">
<div class="row g-3">
    @foreach(['Biodata dan pendaftaran' => $biodata, 'Data keluarga' => $family, 'Data bantuan sosial' => $social] as $title => $fields)
        <div class="col-12 col-xl-6">
            <div class="border rounded-3 h-100">
                <div class="px-3 py-2 border-bottom bg-light fw-semibold">{{ $title }}</div>
                <dl class="row g-0 mb-0 px-3 py-2">
                    @foreach($fields as $label => $value)
                        <dt class="col-5 py-1 small text-muted fw-normal">{{ $label }}</dt>
                        <dd class="col-7 py-1 small mb-0">{{ filled($value) ? $value : '—' }}</dd>
                    @endforeach
                </dl>
            </div>
        </div>
    @endforeach
</div>
</div>

<div class="tab-pane fade" id="marketing-detail-documents" role="tabpanel" aria-labelledby="marketing-detail-documents-tab">
<div class="row g-3">
    @if($registration->path?->documentRequirements->isEmpty())
        <div class="col-12"><div class="alert alert-info border-0 mb-0"><i class="ti ti-info-circle me-1"></i>Belum ada persyaratan berkas untuk jalur ini.</div></div>
    @else
        @foreach($registration->path->documentRequirements as $requirement)
            @php
                $document = $registration->documents->firstWhere('requirement_id', $requirement->id);
                $statusLabel = match($document?->status_verifikasi) {
                    'valid' => 'Valid',
                    'invalid' => 'Perlu perbaikan',
                    default => $document ? 'Menunggu verifikasi' : 'Belum diunggah',
                };
                $statusClass = match($document?->status_verifikasi) {
                    'valid' => 'success',
                    'invalid' => 'danger',
                    default => $document ? 'warning' : 'secondary',
                };
                $documentAccept = collect(explode(',', $requirement->format_file))->map(fn($ext) => '.'.trim($ext))->implode(',');
            @endphp
            <div class="col-12 col-xl-6">
                <div class="card h-100 border-0 shadow-sm pmb-upload-card pmb-upload-document">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                            <div class="d-flex gap-2 align-items-center"><span class="pmb-upload-icon"><i class="ti ti-file-text"></i></span><div><h6 class="mb-1">{{ $requirement->nama_dokumen }} @if($requirement->is_required)<span class="text-danger">*</span>@endif</h6><small class="text-muted">{{ str_replace(',', ', ', $requirement->format_file) }} · Maks. 10 MB</small></div></div>
                            <span class="badge rounded-pill bg-{{ $statusClass }}-subtle text-{{ $statusClass }}">{{ $statusLabel }}</span>
                        </div>
                        @if($document?->catatan_admin)<div class="alert alert-warning py-2 small">{{ $document->catatan_admin }}</div>@endif
                        @if($document)
                            <a class="btn btn-primary w-100 d-flex align-items-center justify-content-between mb-3" href="{{ route('admin.pmb.marketing.registrations.documents.preview', [$registration->id, $document->id]) }}" target="_blank" rel="noopener">
                                <span><i class="ti ti-eye me-2"></i>Lihat berkas saat ini</span><i class="ti ti-external-link"></i>
                            </a>
                        @endif
                        <form action="{{ route('admin.pmb.marketing.registrations.documents.store', [$registration->id, $requirement->id]) }}" method="POST" enctype="multipart/form-data" data-marketing-file-upload>
                            @csrf
                            <input class="visually-hidden" id="marketing-document-file-{{ $requirement->id }}" type="file" name="file" accept="{{ $documentAccept }}" required aria-label="{{ $requirement->nama_dokumen }}">
                            <label class="pmb-upload-dropzone" for="marketing-document-file-{{ $requirement->id }}" data-upload-dropzone>
                                <i class="ti ti-cloud-upload pmb-upload-cloud"></i>
                                <span data-upload-filename>Seret file ke sini atau <strong>klik untuk memilih</strong></span>
                                <small>File akan dikirim untuk verifikasi setelah tombol ditekan.</small>
                            </label>
                            <div class="d-flex justify-content-between align-items-center gap-2 mt-3">
                                <span class="small text-primary" data-upload-loading hidden><span class="spinner-border spinner-border-sm me-1" role="status"></span>Mengunggah berkas...</span>
                                <button class="btn btn-sm btn-primary ms-auto" type="submit"><i class="ti ti-upload me-1"></i>{{ $document ? 'Ganti berkas' : 'Unggah berkas' }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
</div>

<div class="tab-pane fade" id="marketing-detail-payments" role="tabpanel" aria-labelledby="marketing-detail-payments-tab">
<div class="row g-3">
    @if($registration->payments->isEmpty())
        <div class="col-12"><div class="alert alert-info border-0 mb-0"><i class="ti ti-info-circle me-1"></i>Belum ada tagihan pembayaran.</div></div>
    @else
        @foreach($registration->payments as $payment)
            @php
                [$paymentLabel, $paymentClass] = match($payment->status) {
                    'lunas' => ['Lunas', 'success'],
                    'ditolak' => ['Ditolak', 'danger'],
                    'menunggu_verifikasi' => ['Menunggu verifikasi', 'warning'],
                    default => ['Belum dibayar', 'secondary'],
                };
            @endphp
            <div class="col-12 col-xl-6">
                <div class="card h-100 border-0 shadow-sm pmb-upload-card pmb-upload-payment">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                            <div class="d-flex gap-2 align-items-center"><span class="pmb-upload-icon"><i class="ti ti-credit-card"></i></span><div><h6 class="mb-1">{{ ucfirst(str_replace('_', ' ', $payment->jenis_tagihan)) }}</h6><strong>Rp {{ number_format((float) $payment->nominal_tagihan, 0, ',', '.') }}</strong></div></div>
                            <span class="badge rounded-pill bg-{{ $paymentClass }}-subtle text-{{ $paymentClass }}">{{ $paymentLabel }}</span>
                        </div>
                        @if($payment->file_bukti_bayar)
                            <a class="btn btn-warning text-dark w-100 d-flex align-items-center justify-content-between mb-3" href="{{ route('admin.pmb.marketing.registrations.payments.proof', [$registration->id, $payment->id]) }}" target="_blank" rel="noopener">
                                <span><i class="ti ti-receipt me-2"></i>Lihat bukti bayar saat ini</span><i class="ti ti-external-link"></i>
                            </a>
                        @endif
                        @if(in_array($payment->status, ['pending', 'menunggu_verifikasi', 'ditolak'], true))
                            <form action="{{ route('admin.pmb.marketing.registrations.payments.proof.store', [$registration->id, $payment->id]) }}" method="POST" enctype="multipart/form-data" data-marketing-file-upload>
                                @csrf
                                <input class="visually-hidden" id="marketing-payment-file-{{ $payment->id }}" type="file" name="bukti_bayar" accept=".pdf,.jpg,.jpeg,.png" required aria-label="Bukti bayar {{ $payment->jenis_tagihan }}">
                                <label class="pmb-upload-dropzone" for="marketing-payment-file-{{ $payment->id }}" data-upload-dropzone>
                                    <i class="ti ti-cloud-upload pmb-upload-cloud"></i>
                                    <span data-upload-filename>Seret bukti ke sini atau <strong>klik untuk memilih</strong></span>
                                    <small>PDF/JPG/PNG · Maks. 5 MB</small>
                                </label>
                                <div class="d-flex justify-content-between align-items-center gap-2 mt-3">
                                    <span class="small text-primary" data-upload-loading hidden><span class="spinner-border spinner-border-sm me-1" role="status"></span>Mengunggah bukti bayar...</span>
                                    <button class="btn btn-sm btn-primary ms-auto" type="submit"><i class="ti ti-upload me-1"></i>{{ $payment->file_bukti_bayar ? 'Ganti bukti' : 'Unggah bukti bayar' }}</button>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-success py-2 small mb-0">Tagihan ini sudah lunas dan tidak dapat diubah.</div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
</div>
</div>
