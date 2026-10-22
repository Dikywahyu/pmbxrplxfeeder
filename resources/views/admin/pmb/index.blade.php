@extends('layouts.app')

@section('title', 'Administrasi PMB')

@section('content')
@push('styles')
<link rel="stylesheet" href="{{ asset('dist/assets/libs/select2/dist/css/select2.min.css') }}">
<style>
    #pmb-admin .pmb-admin-hero { position: relative; overflow: hidden; border: 0; border-radius: 1rem; background: linear-gradient(115deg, #315efb 0%, #6545d8 52%, #9b4bd6 100%); box-shadow: 0 12px 30px rgba(72, 84, 210, .2); }
    #pmb-admin .pmb-admin-hero::before, #pmb-admin .pmb-admin-hero::after { position: absolute; content: ''; border: 1px solid rgba(255,255,255,.16); border-radius: 50%; pointer-events: none; }
    #pmb-admin .pmb-admin-hero::before { width: 270px; height: 270px; right: 7%; top: -185px; }
    #pmb-admin .pmb-admin-hero::after { width: 190px; height: 190px; right: 2%; bottom: -145px; }
    #pmb-admin .pmb-admin-hero .pmb-hero-icon { display: grid; width: 62px; height: 62px; place-items: center; border: 1px solid rgba(255,255,255,.28); border-radius: 1.1rem; background: rgba(255,255,255,.16); color: #fff; font-size: 2rem; backdrop-filter: blur(5px); }
    #pmb-admin .pmb-admin-hero .pmb-hero-eyebrow { color: rgba(255,255,255,.78); font-size: .72rem; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; }
    #pmb-admin .pmb-admin-hero h4 { color: #fff; font-size: clamp(1.25rem, 2vw, 1.65rem); }
    #pmb-admin .pmb-admin-hero p { color: rgba(255,255,255,.82) !important; }
    #pmb-admin .pmb-shell-card { border: 1px solid #edf0f7; border-radius: 1rem; box-shadow: 0 8px 24px rgba(33, 52, 91, .055); }
    #pmb-content .select2-container { width:100%!important; }
    #pmb-content .select2-container .select2-selection--single { height:38px; border:1px solid #dfe5ef; border-radius:.375rem; }
    #pmb-content .select2-container--default .select2-selection--single .select2-selection__rendered { line-height:36px; color:#5a6a85; padding-left:.75rem; }
    #pmb-content .select2-container--default .select2-selection--single .select2-selection__arrow { height:36px; }
    #pmb-content .pmb-stat-card { position: relative; min-height: 150px; overflow: hidden; border: 0; border-radius: 1rem; box-shadow: 0 8px 20px rgba(31, 49, 81, .08); transition: transform .18s ease, box-shadow .18s ease; }
    #pmb-content .pmb-stat-card:hover { transform: translateY(-3px); box-shadow: 0 13px 25px rgba(31, 49, 81, .14); }
    #pmb-content .pmb-stat-card::after { position: absolute; width: 130px; height: 130px; right: -48px; bottom: -67px; border: 20px solid rgba(255,255,255,.17); border-radius: 50%; content: ''; }
    #pmb-content .pmb-stat-card .pmb-stat-icon { display: grid; width: 42px; height: 42px; place-items: center; border: 1px solid rgba(255,255,255,.28); border-radius: .85rem; background: rgba(255,255,255,.17); color: #fff; font-size: 1.35rem; }
    #pmb-content .pmb-stat-card .pmb-stat-label { color: rgba(255,255,255,.84); font-size: .8rem; font-weight: 600; }
    #pmb-content .pmb-stat-card h3 { color: #fff; font-size: 1.85rem; font-weight: 750; }
    #pmb-content .pmb-stat-card small { color: rgba(255,255,255,.78); }
    #pmb-content .pmb-stat-blue { background: linear-gradient(135deg, #4387ff, #3154d9); }
    #pmb-content .pmb-stat-amber { background: linear-gradient(135deg, #ffb547, #f07b38); }
    #pmb-content .pmb-stat-green { background: linear-gradient(135deg, #32c995, #119b79); }
    #pmb-content .pmb-stat-red { background: linear-gradient(135deg, #ff7d91, #e34c69); }
    #pmb-content .pmb-stat-violet { background: linear-gradient(135deg, #a27aff, #744de0); }
    #pmb-content .pmb-stat-cyan { background: linear-gradient(135deg, #35bfd1, #168da9); }
    #pmb-content .pmb-overview-chart { overflow: hidden; border: 1px solid #edf0f7; border-radius: 1rem; background: linear-gradient(145deg, #fff 58%, #f4f7ff); box-shadow: 0 8px 24px rgba(33, 52, 91, .055); }
    #pmb-content .pmb-overview-chart .card-header { border-bottom: 1px solid #edf0f7; background: transparent; }
    .pmb-registrations-responsive { overflow-x: auto; border: 1px solid #edf0f5; border-radius: .75rem; }
    .pmb-registrations-responsive table { min-width: 1100px; margin-bottom: 0; }
    .pmb-registrations-responsive thead th { padding: .85rem .75rem; color: #44516a; font-size: .76rem; font-weight: 700; text-transform: uppercase; letter-spacing: .035em; white-space: nowrap; }
    .pmb-registrations-responsive tbody td { padding: .8rem .75rem; }
    .pmb-document-chip { display: inline-flex; align-items: center; gap: .38rem; margin: .12rem .2rem .12rem 0; padding: .38rem .58rem; border: 1px solid #e0e7f3; border-radius: 999px; background: #f6f8fc; color: #344054; font: inherit; font-size: .76rem; font-weight: 600; line-height: 1; white-space: nowrap; transition: transform .15s ease, box-shadow .15s ease; }
    .pmb-document-chip:hover { transform: translateY(-1px); box-shadow: 0 3px 10px rgba(40, 70, 120, .12); }
    .pmb-document-chip small { padding: .2rem .38rem; border-radius: 999px; background: #e9efff; color: #4263a8; font-size: .65rem; font-weight: 700; }
    .pmb-document-chip[data-kind="identity"] { border-color: #b9d5ff; background: #f1f7ff; color: #2459a6; }
    .pmb-document-chip[data-kind="family"] { border-color: #d7c8ff; background: #f7f3ff; color: #6842ad; }
    .pmb-document-chip[data-kind="poverty"] { border-color: #ffd79b; background: #fff8eb; color: #9a5a00; }
    .pmb-document-chip[data-kind="diploma"] { border-color: #a9e5d2; background: #effbf6; color: #147653; }
    .pmb-document-chip[data-kind="kip"] { border-color: #a8e5ed; background: #effbfd; color: #087b8b; }
    .pmb-document-chip[data-kind="aid"] { border-color: #f3bfd9; background: #fff3f8; color: #a43c71; }
    .pmb-document-chip[data-status="valid"] small { background: #d9f5e8; color: #11764e; }
    .pmb-document-chip[data-status="invalid"] small { background: #ffe2dc; color: #b54735; }
    .pmb-document-chip[data-status="pending"] small { background: #e4edff; color: #3159a5; }
    .pmb-document-chevron { opacity: .5; }
    .pmb-registration-documents { display: flex; flex-wrap: wrap; align-items: flex-start; gap: .35rem; min-width: 0; }
    .pmb-document-preview { display: flex; min-height: 220px; max-height: 68vh; align-items: center; justify-content: center; overflow: auto; border: 1px solid #e8edf5; border-radius: .75rem; background: #f5f7fb; }
    .pmb-document-preview:empty::before { content: 'Pratinjau berkas akan tampil di sini'; color: #8490a3; font-size: .9rem; }
    .pmb-document-preview img { display: block; max-width: 100%; max-height: 68vh; object-fit: contain; }
    .pmb-document-preview iframe { display: block; width: 100%; height: 68vh; min-height: 420px; border: 0; background: #fff; }
    #pmb-marketing-registration-detail-body .nav-pills { border: 1px solid #e6ebf5; background: linear-gradient(110deg, #f1f5ff, #f7f1ff) !important; }
    #pmb-marketing-registration-detail-body .nav-pills .nav-link { border: 1px solid transparent; color: #52617a; font-weight: 650; transition: color .18s ease, background .18s ease, transform .18s ease; }
    #pmb-marketing-registration-detail-body .nav-pills .nav-link:hover { transform: translateY(-1px); background: #fff; color: #315efb; }
    #pmb-marketing-registration-detail-body .nav-pills .nav-link.active { color: #fff; background: linear-gradient(120deg, #3867ed, #7251d5); box-shadow: 0 5px 14px rgba(71, 92, 211, .22); }
    #pmb-marketing-registration-detail-body .tab-pane > .row > .col-12 > .border { border-color: #e7ebf3 !important; box-shadow: 0 6px 18px rgba(38, 58, 99, .045); }
    #pmb-marketing-registration-detail-body .pmb-upload-card { border-radius: 1rem; transition: transform .18s ease, box-shadow .18s ease; }
    #pmb-marketing-registration-detail-body .pmb-upload-card:hover { transform: translateY(-2px); box-shadow: 0 12px 25px rgba(38, 58, 99, .1) !important; }
    #pmb-marketing-registration-detail-body .pmb-upload-document { background: linear-gradient(145deg, #fff, #f6f9ff); }
    #pmb-marketing-registration-detail-body .pmb-upload-payment { background: linear-gradient(145deg, #fff, #fff8f1); }
    #pmb-marketing-registration-detail-body .pmb-upload-icon { display: grid; width: 42px; height: 42px; flex: 0 0 42px; place-items: center; border-radius: .85rem; background: #e9efff; color: #4265dc; font-size: 1.3rem; }
    #pmb-marketing-registration-detail-body .pmb-upload-payment .pmb-upload-icon { background: #fff0dc; color: #d17a19; }
    #pmb-marketing-registration-detail-body .pmb-upload-dropzone { display: flex; min-height: 116px; cursor: pointer; flex-direction: column; align-items: center; justify-content: center; gap: .25rem; padding: 1rem; border: 1.5px dashed #9bb6f4; border-radius: .9rem; background: rgba(238, 244, 255, .8); color: #425d9b; text-align: center; transition: border-color .18s ease, background .18s ease, transform .18s ease; }
    #pmb-marketing-registration-detail-body .pmb-upload-payment .pmb-upload-dropzone { border-color: #e8bd85; background: rgba(255, 247, 235, .9); color: #9b6422; }
    #pmb-marketing-registration-detail-body .pmb-upload-dropzone:hover, #pmb-marketing-registration-detail-body .pmb-upload-dropzone.is-dragover { transform: translateY(-1px); border-color: #4265dc; background: #eaf0ff; }
    #pmb-marketing-registration-detail-body .pmb-upload-dropzone.is-selected { border-style: solid; border-color: #28a879; background: #effbf6; color: #187b5a; }
    #pmb-marketing-registration-detail-body .pmb-upload-cloud { font-size: 1.6rem; }
    #pmb-marketing-registration-detail-body [data-upload-loading]:not([hidden]) { display: inline-flex !important; align-items: center; }
    #pmb-marketing-registration-detail-body [data-upload-loading][hidden] { display: none !important; }
    #pmb-marketing-registration-detail-body form.is-uploading .pmb-upload-dropzone { cursor: progress; opacity: .65; }
    #pmb-content .marketing-optional-section { overflow: hidden; border: 1px solid #e8edf5; border-radius: .75rem; background: #fff; }
    #pmb-content .marketing-optional-section summary { display: flex; min-height: 46px; cursor: pointer; align-items: center; justify-content: space-between; gap: .5rem; padding: .7rem .9rem; color: #40516d; font-weight: 650; list-style: none; }
    #pmb-content .marketing-optional-section summary::-webkit-details-marker { display: none; }
    #pmb-content .marketing-optional-section summary::after { content: '+'; color: #5d87ff; font-size: 1.2rem; }
    #pmb-content .marketing-optional-section[open] summary::after { content: '−'; }
    #pmb-content .marketing-optional-body { padding: .25rem .9rem .9rem; }
    #pmb-content .marketing-stage-tile { min-height: 70px; }
    #pmb-content .marketing-stage-tile .small { min-height: 2.2em; line-height: 1.1; }
    #pmb-content .pmb-fee-tabs { padding: .35rem; border: 1px solid #e8edf5; border-radius: .9rem; background: #f5f7fc; }
    #pmb-content .pmb-fee-tabs .nav-link { min-height: 42px; padding: .6rem 1rem; border-radius: .65rem; color: #5b667a; font-weight: 650; white-space: nowrap; }
    #pmb-content .pmb-fee-tabs .nav-link.active { color: #fff; background: linear-gradient(120deg, #3867ed, #7251d5); box-shadow: 0 4px 12px rgba(71, 92, 211, .2); }
    #pmb-content .pmb-fee-tabs .nav-link.active .badge { color: #315efb !important; }
    .pmb-status-select { min-width: 145px; border-color: #dce4f0; font-size: .82rem; }
    .pmb-status-select:focus { border-color: #5d87ff; box-shadow: 0 0 0 .2rem rgba(93, 135, 255, .14); }
    .pmb-desil-select { width: 145px; border-color: #dce4f0; color: #4265dc; font-size: .8rem; }
    .pmb-desil-select:focus { border-color: #5d87ff; box-shadow: 0 0 0 .2rem rgba(93, 135, 255, .14); }
    @media (max-width: 991.98px) {
        .pmb-registrations-responsive,
        .pmb-registrations-responsive .dataTables_wrapper { width: 100%; max-width: 100%; min-width: 0; overflow: visible; border: 0; }
        .pmb-registrations-responsive table { display: block; table-layout: fixed; min-width: 0 !important; max-width: 100% !important; width: 100% !important; }
        .pmb-registrations-responsive thead { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0; }
        .pmb-registrations-responsive tbody { display: block; }
        .pmb-registrations-responsive tbody tr { display: block; box-sizing: border-box; width: 100%; max-width: 100%; margin-bottom: .9rem; padding: .45rem .75rem; overflow: hidden; border: 1px solid #e8edf5; border-radius: .85rem; background: #fff; box-shadow: 0 4px 14px rgba(31, 54, 91, .06); }
        .pmb-registrations-responsive tbody td { display: block !important; box-sizing: border-box; width: 100% !important; max-width: 100%; padding: .65rem 0; overflow-wrap: anywhere; border: 0 !important; border-bottom: 1px solid #f0f2f6 !important; white-space: normal !important; }
        .pmb-registrations-responsive tbody td::before { display: block; margin-bottom: .45rem; content: attr(data-label); color: #7a869a; font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .025em; }
        .pmb-registrations-responsive tbody td[data-label="Status seleksi"] form { width: 100%; }
        .pmb-registrations-responsive tbody td[data-label="Status seleksi"] select { width: 100%; }
        .pmb-registrations-responsive .pmb-registration-documents { width: 100%; }
        .pmb-registrations-responsive .pmb-document-chip { max-width: 100%; white-space: normal; overflow-wrap: anywhere; }
        .pmb-registrations-responsive tbody td[data-label="Pembayaran"] > div { width: 100%; }
        .pmb-registrations-responsive tbody td[data-label="Aksi"] { display: flex !important; flex-wrap: wrap; gap: .5rem; }
        .pmb-registrations-responsive tbody td[data-label="Aksi"]::before { flex: 0 0 100%; }
        .pmb-registrations-responsive tbody td:last-child { border-bottom: 0 !important; }
        .pmb-registrations-responsive .dataTables_wrapper .row { --bs-gutter-x: .5rem; max-width: 100%; }
        .pmb-registrations-responsive .dt-buttons { display: flex; flex-wrap: wrap; gap: .35rem; max-width: 100%; }
        .pmb-registrations-responsive .dataTables_filter { width: 100%; margin-top: .35rem; text-align: left !important; }
        .pmb-registrations-responsive .dataTables_filter label,
        .pmb-registrations-responsive .dataTables_filter input { width: 100%; margin-left: 0 !important; }
        .pmb-registrations-responsive .dataTables_info { white-space: normal; }
    }
    @media (max-width: 575.98px) {
        #pmb-admin .pmb-admin-hero { margin-bottom: .75rem !important; border-radius: .8rem; }
        #pmb-admin .pmb-admin-hero .card-body { padding: .8rem 1rem !important; }
        #pmb-admin .pmb-admin-hero .pmb-hero-icon { width: 42px; height: 42px; border-radius: .75rem; font-size: 1.4rem; }
        #pmb-admin .pmb-shell-card > .card-body { padding: .75rem; }
        #pmb-admin .pmb-shell-card .card-body > .d-flex { margin-bottom: .75rem !important; }
        #pmb-content > .row { --bs-gutter-x: .55rem; --bs-gutter-y: .55rem; }
        #pmb-content .card-header { padding: .7rem .8rem; }
        #pmb-content .card-body { padding: .75rem; }
        #pmb-content .marketing-stage-tile { min-height: 62px; }
        #pmb-content .marketing-stage-tile .small { min-height: 2.4em; font-size: .68rem; }
        #pmb-content .marketing-stage-tile .fs-4 { font-size: 1.15rem !important; }
        #pmb-content [data-marketing-registration-form] .form-label { margin-bottom: .2rem; font-size: .75rem; }
        #pmb-content [data-marketing-registration-form] .form-control,
        #pmb-content [data-marketing-registration-form] .form-select { min-height: 36px; padding: .35rem .55rem; font-size: .82rem; }
        #pmb-content [data-marketing-registration-form] .form-text,
        #pmb-content [data-marketing-registration-form] small { font-size: .68rem; }
        #pmb-content .pmb-marketing-table-wrap { overflow-x: auto; }
        #pmb-content .pmb-marketing-table-wrap table { min-width: 680px; }
        #pmb-content .pmb-fee-tabs { flex-wrap: nowrap; overflow-x: auto; scrollbar-width: thin; }
        #pmb-content .pmb-fee-tabs .nav-link { padding: .55rem .7rem; font-size: .78rem; }
    }
</style>
@endpush
<div class="container-fluid" id="pmb-admin"
    data-section="{{ $section }}"
    data-data-url="{{ route('admin.pmb.data', ['section' => '__SECTION__']) }}">
    <div class="card pmb-admin-hero position-relative overflow-hidden mb-4">
        <div class="card-body px-4 py-3">
            <div class="row align-items-center">
                <div class="col-9 position-relative" style="z-index:1">
                    @php
                        $sectionTitles = ['overview' => 'Ringkasan PMB', 'registrations' => 'Data Pendaftar', 'periods' => 'Periode Pendaftaran', 'paths' => 'Jalur Pendaftaran', 'programs' => 'Program Studi', 'fees' => 'Pengaturan Biaya', 'referrals' => 'Marketing & Referral', 'requirements' => 'Persyaratan Berkas', 'marketing' => 'Input Pendaftar PMB'];
                    @endphp
                    <span class="pmb-hero-eyebrow">Portal administrasi</span>
                    <h4 class="fw-semibold mb-1">{{ $sectionTitles[$section] ?? 'Administrasi PMB' }}</h4>
                    <p class="mb-0">Kelola proses penerimaan mahasiswa baru dalam satu tempat.</p>
                </div>
                <div class="col-3 d-flex justify-content-center position-relative" style="z-index:1"><span class="pmb-hero-icon"><i class="ti ti-school"></i></span></div>
            </div>
        </div>
    </div>

    <div id="pmb-feedback" class="alert d-none" role="alert" aria-live="polite"></div>

    <div id="pmb-marketing-claim-result" class="card border-primary-subtle shadow-sm mb-4 d-none" aria-live="polite">
        <div class="card-body">
            <div class="d-flex gap-3 align-items-start">
                <span class="d-grid rounded-circle bg-success-subtle text-success" style="width:44px;height:44px;place-items:center"><i class="ti ti-link fs-5"></i></span>
                <div class="flex-grow-1"><h5 class="mb-1">Link klaim siap dibagikan</h5><p class="text-muted small mb-2" id="pmb-marketing-claim-number"></p>
                    <div class="input-group"><input type="text" class="form-control" id="pmb-marketing-claim-url" readonly aria-label="Link klaim mahasiswa"><button type="button" class="btn btn-primary" id="pmb-marketing-copy-claim" data-copy-link=""><i class="ti ti-copy me-1"></i>Salin link</button></div>
                    <small class="text-warning-emphasis d-block mt-2">Link pribadi ini hanya dapat digunakan sekali dan berlaku 14 hari. Bagikan langsung kepada mahasiswa.</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card pmb-shell-card">
        <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                <div><h5 class="card-title mb-1">{{ $sectionTitles[$section] ?? 'Manajemen Penerimaan' }}</h5><span class="text-muted small">Kelola data kategori ini.</span></div>
                <button type="button" class="btn btn-light-primary text-primary" id="pmb-reload">
                    <i class="ti ti-refresh me-1"></i> Muat ulang
                </button>
            </div>

            <div class="mb-4"><a class="btn btn-sm btn-light-secondary" href="{{ route('admin.pmb.index') }}"><i class="ti ti-arrow-left me-1"></i>Ringkasan Admin PMB</a></div>

            <div id="pmb-content" aria-live="polite">
                <div class="text-center text-muted py-5"><span class="spinner-border spinner-border-sm me-2"></span>Memuat data...</div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="pmb-document-review-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered"><div class="modal-content">
        <div class="modal-header"><div><h5 class="modal-title" id="pmb-document-review-title">Review berkas</h5><small class="text-muted" id="pmb-document-review-status"></small></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
        <div class="modal-body">
            <div id="pmb-document-preview" class="pmb-document-preview mb-3" aria-live="polite"></div>
            <a class="btn btn-outline-primary btn-sm mb-3" id="pmb-document-review-download" href="#" target="_blank" rel="noopener">Unduh berkas</a>
            @can('pmb-review')
                <form id="pmb-document-review-form">
                    @csrf
                    <input type="hidden" name="status_verifikasi">
                    <label class="form-label" for="pmb-document-review-note">Catatan perbaikan</label>
                    <textarea class="form-control" id="pmb-document-review-note" name="catatan_admin" rows="3" placeholder="Isi catatan bila berkas perlu diperbaiki"></textarea>
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button class="btn btn-outline-danger" type="submit" value="invalid" data-document-status-submit>Perlu perbaikan</button>
                        <button class="btn btn-success" type="submit" value="valid" data-document-status-submit>Setujui berkas</button>
                    </div>
                </form>
            @endcan
        </div>
    </div></div>
</div>

<div class="modal fade" id="pmb-edit-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
        <form id="pmb-edit-form">
            <div class="modal-header"><h5 class="modal-title">Edit data PMB</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
            <div class="modal-body" id="pmb-edit-fields"></div>
            <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan perubahan</button></div>
        </form>
    </div></div>
</div>

<div class="modal fade" id="pmb-marketing-spp-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
        <form id="pmb-marketing-spp-form" action="{{ route('admin.pmb.referrals.spp-rates.bulk') }}" method="POST">
            @csrf
            <div class="modal-header"><div><h5 class="modal-title">Atur harga SPP marketing</h5><small class="text-muted">Harga diterapkan ke semua prodi pada periode dan jalur terpilih.</small></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
            <div class="modal-body" id="pmb-marketing-spp-fields"></div>
            <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>Simpan harga</button></div>
        </form>
    </div></div>
</div>

<div class="modal fade" id="pmb-marketing-registration-detail-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable"><div class="modal-content">
        <div class="modal-header"><div><h5 class="modal-title">Detail pendaftar referral</h5><small class="text-muted">Biodata dan berkas yang sudah diunggah</small></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
        <div class="modal-body" id="pmb-marketing-registration-detail-body"></div>
    </div></div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('dist/assets/libs/select2/dist/js/select2.min.js') }}"></script>
<script>
(() => {
    const root = document.getElementById('pmb-admin');
    if (!root) return;

    const content = document.getElementById('pmb-content');
    const feedback = document.getElementById('pmb-feedback');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    let activeSection = root.dataset.section;
    let chart = null;
    let marketingChart = null;
    let pathReportChart = null;
    let feeBulkSelection = null;
    let feeActiveTab = '#pmb-fee-bulk-pane';
    let referralActiveTab = '#pmb-referrals-marketing-pane';
    const editModal = new bootstrap.Modal(document.getElementById('pmb-edit-modal'));
    const editForm = document.getElementById('pmb-edit-form');
    const editFields = document.getElementById('pmb-edit-fields');
    const marketingSppModalElement = document.getElementById('pmb-marketing-spp-modal');
    const marketingSppModal = new bootstrap.Modal(marketingSppModalElement);
    const marketingSppForm = document.getElementById('pmb-marketing-spp-form');
    const marketingSppFields = document.getElementById('pmb-marketing-spp-fields');
    const documentModalElement = document.getElementById('pmb-document-review-modal');
    const documentModal = new bootstrap.Modal(documentModalElement);
    const documentReviewForm = document.getElementById('pmb-document-review-form');
    const marketingClaimResult = document.getElementById('pmb-marketing-claim-result');
    const marketingDetailModalElement = document.getElementById('pmb-marketing-registration-detail-modal');
    const marketingDetailModal = new bootstrap.Modal(marketingDetailModalElement);
    const marketingDetailBody = document.getElementById('pmb-marketing-registration-detail-body');
    const schemas = {
        periods: [
            ['nama_periode', 'Nama periode', 'text'], ['tanggal_mulai', 'Tanggal mulai', 'date'],
            ['tanggal_selesai', 'Tanggal selesai', 'date'], ['is_active', 'Status periode', 'select', [['1', 'Aktif'], ['0', 'Nonaktif']]],
        ],
        paths: [['nama_jalur', 'Nama jalur', 'text'], ['deskripsi', 'Deskripsi', 'textarea']],
        programs: [
            ['id_dikti', 'ID Program Studi DIKTI (UUID)', 'text'], ['kode_prodi', 'Kode program studi', 'text'], ['nama_prodi', 'Nama program studi', 'text'],
            ['jenjang', 'Jenjang', 'text'], ['is_active', 'Status', 'select', [['1', 'Aktif'], ['0', 'Nonaktif']]],
        ],
        registrations: [
            ['nama_mahasiswa', 'Nama pendaftar', 'text'], ['nik', 'NIK', 'text'],
            ['jenis_kelamin', 'Jenis kelamin', 'select', [['', 'Belum diisi'], ['L', 'Laki-laki'], ['P', 'Perempuan']]],
            ['handphone', 'Nomor handphone', 'text'],
            ['desil', 'Kategori desil', 'select', [['', 'Belum ditentukan'], ...Array.from({ length: 10 }, (_, index) => [String(index + 1), `Desil ${index + 1}`])]],
            ['status', 'Status pendaftaran', 'select', [['draft', 'Draft'], ['berkas_masuk', 'Berkas masuk'], ['review', 'Dalam review'], ['sudah_ujian', 'Sudah ujian'], ['lulus', 'Lulus'], ['diterima', 'Diterima'], ['ditolak', 'Ditolak'], ['mundur', 'Mundur']]],
        ],
        fees: [
            ['period_id', 'Periode', 'select'], ['path_id', 'Jalur', 'select'], ['program_id', 'Program studi', 'select'],
            ['nominal_pendaftaran', 'Biaya pendaftaran (Rp)', 'number'], ['nominal_spp', 'SPP (Rp)', 'number'],
        ],
        schedule: [
            ['period_id', 'Periode', 'select'], ['path_id', 'Jalur pendaftaran', 'select'],
            ['tanggal_mulai', 'Tanggal mulai jalur', 'date'], ['tanggal_selesai', 'Tanggal tutup jalur', 'date'],
            ['is_active', 'Status jadwal', 'select', [['1', 'Aktif'], ['0', 'Nonaktif']]],
        ],
        referralSppRate: [
            ['nominal_spp', 'Harga SPP khusus (Rp)', 'number'],
        ],
        requirements: [
            ['path_id', 'Jalur pendaftaran', 'select'], ['nama_dokumen', 'Nama berkas', 'text'],
            ['is_required', 'Wajib diunggah', 'select', [['1', 'Ya'], ['0', 'Tidak']]],
            ['format_file', 'Format berkas (pisahkan koma)', 'text'],
        ],
    };

    const message = (text, error = false) => {
        if (window.Swal) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: error ? 'error' : 'success',
                title: text,
                showConfirmButton: false,
                timer: error ? 5000 : 3200,
                timerProgressBar: true,
            });
            feedback.className = 'alert d-none';
            return;
        }

        feedback.textContent = text;
        feedback.className = `alert alert-${error ? 'danger' : 'success'}`;
    };

    const showMarketingClaimLink = (result) => {
        document.getElementById('pmb-marketing-claim-number').textContent = `Nomor pendaftaran: ${result.registration_number}`;
        document.getElementById('pmb-marketing-claim-url').value = result.claim_url;
        document.getElementById('pmb-marketing-copy-claim').dataset.copyLink = result.claim_url;
        marketingClaimResult.classList.remove('d-none');
        marketingClaimResult.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    };

    const initializeTables = () => {
        if (!window.jQuery || !$.fn.DataTable) return;
        $(content).find('.pmb-data-table').each(function () {
            $(this).DataTable({
                scrollX: activeSection !== 'registrations',
                scrollCollapse: true,
                autoWidth: false,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Semua']],
                order: [],
                dom: '<"d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3"Bf>rt<"d-flex flex-wrap justify-content-between align-items-center mt-3"ip>',
                buttons: [
                    { extend: 'pageLength', className: 'btn btn-outline-secondary btn-sm' },
                    {
                        extend: 'excelHtml5', text: '<i class="ti ti-file-spreadsheet me-1"></i>Excel',
                        className: 'btn btn-outline-success btn-sm',
                        exportOptions: { columns: ':visible:not(:last-child)', format: { body: exportCell } },
                    },
                    {
                        extend: 'csvHtml5', text: '<i class="ti ti-file-text me-1"></i>CSV',
                        className: 'btn btn-outline-primary btn-sm',
                        exportOptions: { columns: ':visible:not(:last-child)', format: { body: exportCell } },
                    },
                    {
                        extend: 'colvis', text: '<i class="ti ti-columns me-1"></i>Kolom',
                        className: 'btn btn-outline-secondary btn-sm', columns: ':not(:last-child)',
                    },
                ],
                language: {
                    search: '', searchPlaceholder: 'Cari data...',
                    info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                    infoEmpty: 'Tidak ada data', zeroRecords: 'Data tidak ditemukan',
                    emptyTable: 'Belum ada data',
                    paginate: { previous: 'Sebelumnya', next: 'Berikutnya' },
                },
            });
        });
    };

    function exportCell(value) {
        const cell = document.createElement('div');
        cell.innerHTML = value;
        cell.querySelectorAll('form, button').forEach((element) => element.remove());
        return cell.textContent.trim().replace(/\s+/g, ' ');
    }

    const setupFeeBulk = () => {
        const form = document.getElementById('pmb-fee-bulk-form');
        if (!form) return;
        const period = form.querySelector('#fee-bulk-period');
        const path = form.querySelector('#fee-bulk-path');
        const fees = JSON.parse(form.dataset.fees || '[]');
        if (feeBulkSelection) {
            period.value = feeBulkSelection.periodId;
            path.value = feeBulkSelection.pathId;
        }
        const updateRows = () => {
            const hasSelection = period.value && path.value;
            form.querySelectorAll('[data-fee-row]').forEach((row) => {
                const fee = fees.find((item) => String(item.period_id) === period.value
                    && String(item.path_id) === path.value
                    && String(item.program_id) === row.dataset.programId);
                const inputs = row.querySelectorAll('input[type="number"]');
                inputs[0].value = fee?.nominal_pendaftaran ?? '';
                inputs[1].value = fee?.nominal_spp ?? '';
                const badge = row.querySelector('[data-fee-state]');
                badge.textContent = !hasSelection ? 'Pilih periode dan jalur' : (fee ? 'Biaya tersedia' : 'Belum diatur');
                badge.className = `badge ${!hasSelection ? 'bg-secondary-subtle text-secondary' : (fee ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning')}`;
            });
        };
        period.addEventListener('change', updateRows);
        path.addEventListener('change', updateRows);
        updateRows();
    };

    const loadSection = async (section = activeSection, url = null) => {
        if (!section) return;
        if (activeSection === 'fees') {
            feeActiveTab = content.querySelector('.pmb-fee-tabs .nav-link.active')?.dataset.bsTarget || feeActiveTab;
        }
        if (activeSection === 'referrals') {
            referralActiveTab = content.querySelector('.pmb-fee-tabs .nav-link.active')?.dataset.bsTarget || referralActiveTab;
        }
        activeSection = section;
        content.innerHTML = '<div class="text-center text-muted py-5"><span class="spinner-border spinner-border-sm me-2"></span>Memuat data...</div>';
        const endpoint = url || root.dataset.dataUrl.replace('__SECTION__', encodeURIComponent(section));
        const response = await fetch(endpoint, {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        });
        const result = await response.json();
        if (!response.ok) throw new Error(result.message || 'Gagal memuat data kategori.');
        if (window.jQuery && $.fn.dataTable) {
            $(content).find('.pmb-data-table').each(function () {
                if ($.fn.dataTable.isDataTable(this)) $(this).DataTable().destroy();
            });
        }
        content.innerHTML = result.html;
        initializeReferenceInputs();
        initializeTables();
        if (section === 'fees') {
            setupFeeBulk();
            const activeTab = content.querySelector(`.pmb-fee-tabs [data-bs-target="${feeActiveTab}"]`);
            if (activeTab) bootstrap.Tab.getOrCreateInstance(activeTab).show();
        }
        if (section === 'referrals') {
            const activeTab = content.querySelector(`.pmb-fee-tabs [data-bs-target="${referralActiveTab}"]`);
            if (activeTab) bootstrap.Tab.getOrCreateInstance(activeTab).show();
        }
        if (marketingChart) { marketingChart.destroy(); marketingChart = null; }
        if (pathReportChart) { pathReportChart.destroy(); pathReportChart = null; }
        if (section === 'fees' && window.ApexCharts) {
            const target = document.getElementById('pmb-path-registration-chart');
            const periodFilter = document.getElementById('pmb-path-report-period');
            if (target && periodFilter) {
                const pathIds = JSON.parse(target.dataset.pathIds || '[]');
                const labels = JSON.parse(target.dataset.pathLabels || '[]');
                const counts = JSON.parse(target.dataset.counts || '{}');
                periodFilter.value = target.dataset.selectedPeriod || 'all';
                const getSeries = () => pathIds.map((pathId) => {
                    if (periodFilter.value === 'all') {
                        return Object.entries(counts).reduce((sum, [key, total]) => sum + (key.endsWith(`:${pathId}`) ? Number(total) : 0), 0);
                    }
                    return Number(counts[`${periodFilter.value}:${pathId}`] || 0);
                });
                pathReportChart = new ApexCharts(target, {
                    chart: { type: 'bar', height: Math.max(230, labels.length * 48), toolbar: { show: false } },
                    series: [{ name: 'Jumlah pendaftar', data: getSeries() }],
                    plotOptions: { bar: { horizontal: true, borderRadius: 6, barHeight: '55%', distributed: true, dataLabels: { position: 'right' } } },
                    dataLabels: { enabled: true, formatter: (value) => Math.round(value), offsetX: 8, style: { colors: ['#344054'] } },
                    xaxis: { categories: labels, min: 0, forceNiceScale: true, labels: { formatter: (value) => Math.round(value) } },
                    yaxis: { labels: { maxWidth: 220 } },
                    grid: { borderColor: '#edf0f7', strokeDashArray: 4 },
                    colors: ['#5d87ff', '#13deb9', '#ffae1f', '#fa896b', '#a27aff', '#49beff'],
                    legend: { show: false },
                    noData: { text: 'Belum ada pendaftar pada jalur ini' },
                    tooltip: { y: { formatter: (value) => `${Math.round(value)} pendaftar` } },
                });
                pathReportChart.render();
                periodFilter.addEventListener('change', () => pathReportChart?.updateSeries([{ name: 'Jumlah pendaftar', data: getSeries() }]));
            }
        }
        if (section === 'marketing' && window.ApexCharts) {
            const target = document.getElementById('pmb-marketing-program-chart');
            if (target) {
                const labels = JSON.parse(target.dataset.labels || '[]');
                const series = JSON.parse(target.dataset.series || '[]').map(Number);
                marketingChart = new ApexCharts(target, {
                    chart: { type: 'donut', height: 280, toolbar: { show: false } },
                    responsive: [{ breakpoint: 576, options: { chart: { height: 190 }, legend: { position: 'bottom', fontSize: '11px' } } }],
                    labels, series,
                    legend: { position: 'bottom' },
                    dataLabels: { enabled: true },
                    noData: { text: 'Belum ada data pendaftar' },
                    stroke: { colors: ['#fff'], width: 3 },
                    colors: ['#5d87ff', '#49beff', '#13deb9', '#ffae1f', '#fa896b', '#a27aff', '#e34c69'],
                });
                marketingChart.render();
            }
        }
        if (section === 'overview' && result.chart && window.ApexCharts) {
            const target = document.getElementById('pmb-status-chart');
            if (target) {
                if (chart) chart.destroy();
                chart = new ApexCharts(target, {
                    chart: { type: 'donut', height: 300, toolbar: { show: false } },
                    labels: result.chart.labels,
                    series: result.chart.series,
                    legend: { position: 'bottom' },
                    dataLabels: { enabled: true },
                    noData: { text: 'Belum ada data pendaftar' },
                    stroke: { colors: ['#fff'], width: 3 },
                    colors: ['#94a3b8', '#4387ff', '#ffb547', '#35bfd1', '#32c995', '#a27aff', '#ff7d91', '#e34c69'],
                });
                chart.render();
            }
        }
    };

    const initializeReferenceInputs = () => {
        if (!window.jQuery || !$.fn.select2) return;
        $(content).find('[data-wilayah-select]').each(function () {
            const select = $(this);
            if (select.hasClass('select2-hidden-accessible')) return;
            select.select2({
                width: '100%',
                placeholder: 'Cari nama kecamatan...',
                allowClear: true,
                minimumInputLength: 2,
                dropdownParent: $(content),
                ajax: {
                    url: select.data('ajax-url'),
                    dataType: 'json',
                    delay: 250,
                    data: (params) => ({ q: params.term || '', page: params.page || 1 }),
                    processResults: (data) => data,
                    cache: true,
                },
            });
        });
        $(content).find('[data-income-select]').each(function () {
            const select = this;
            $(select).off('change.pmbIncome').on('change.pmbIncome', () => {
                const target = content.querySelector(`[data-income-name="${select.dataset.incomeSelect}"]`);
                if (target) target.value = select.selectedOptions[0]?.dataset.name || '';
            });
        });
    };

    document.getElementById('pmb-reload').addEventListener('click', async () => {
        try { await loadSection(); message('Data berhasil diperbarui.'); }
        catch (error) { message(error.message, true); }
    });

    root.addEventListener('change', async (event) => {
        const desilSelect = event.target.closest('[data-registration-desil]');
        if (desilSelect) {
            const form = desilSelect.form;
            const previousDesil = desilSelect.dataset.currentDesil;
            const nextDesil = desilSelect.value;
            const confirmation = await Swal.fire({
                title: 'Simpan perubahan desil?',
                text: nextDesil ? `Kategori mahasiswa akan diubah menjadi Desil ${nextDesil}.` : 'Kategori desil mahasiswa akan dikosongkan.',
                icon: 'question', showCancelButton: true,
                confirmButtonText: 'Ya, simpan', cancelButtonText: 'Batal', confirmButtonColor: '#5d87ff',
            });
            if (!confirmation.isConfirmed) {
                desilSelect.value = previousDesil;
                return;
            }

            desilSelect.disabled = true;
            try {
                const response = await fetch(form.action, {
                    method: 'POST', body: new FormData(form),
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf },
                    credentials: 'same-origin',
                });
                const result = await response.json();
                if (!response.ok) {
                    const errors = Object.values(result.errors || {}).flat();
                    throw new Error(errors.join(' ') || result.message || 'Desil gagal diperbarui.');
                }
                desilSelect.dataset.currentDesil = nextDesil;
                message(result.message || 'Desil calon mahasiswa berhasil diperbarui.');
            } catch (error) {
                desilSelect.value = previousDesil;
                message(error.message || 'Desil gagal diperbarui.', true);
            } finally {
                desilSelect.disabled = false;
            }
            return;
        }

        const select = event.target.closest('[data-registration-status-form] select[name="status"]');
        if (!select) return;

        const form = select.form;
        const previousStatus = select.dataset.currentStatus;
        const nextStatus = select.value;
        const nextLabel = select.options[select.selectedIndex]?.text || nextStatus;
        const confirmation = await Swal.fire({
            title: 'Ubah status pendaftar?',
            text: `Status pendaftar akan diubah menjadi "${nextLabel}".`,
            icon: ['ditolak', 'mundur'].includes(nextStatus) ? 'warning' : 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, ubah status',
            cancelButtonText: 'Batal',
            confirmButtonColor: ['ditolak', 'mundur'].includes(nextStatus) ? '#fa896b' : '#5d87ff',
        });

        if (!confirmation.isConfirmed) {
            select.value = previousStatus;
            return;
        }

        const body = new FormData(form);
        select.disabled = true;
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body,
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf },
                credentials: 'same-origin',
            });
            const result = await response.json();
            if (!response.ok) {
                const errors = Object.values(result.errors || {}).flat();
                throw new Error(errors.join(' ') || result.message || 'Status pendaftar gagal diperbarui.');
            }
            await loadSection();
            message(result.message || 'Status pendaftar berhasil diperbarui.');
        } catch (error) {
            select.value = previousStatus;
            select.disabled = false;
            message(error.message || 'Status pendaftar gagal diperbarui.', true);
        }
    });

    root.addEventListener('submit', async (event) => {
        const form = event.target.closest('[data-ajax-form]');
        if (!form) return;
        event.preventDefault();
        const button = form.querySelector('[type="submit"]');
        if (button) button.disabled = true;
        feedback.className = 'alert d-none';
        try {
            if (form.id === 'pmb-fee-bulk-form') {
                const values = new FormData(form);
                feeBulkSelection = { periodId: values.get('period_id'), pathId: values.get('path_id') };
            }
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf },
                credentials: 'same-origin',
            });
            const result = await response.json();
            if (!response.ok) {
                const errors = Object.values(result.errors || {}).flat();
                throw new Error(errors.join(' ') || result.message || 'Permintaan gagal diproses.');
            }
            if (!form.querySelector('input[name="_method"]')) form.reset();
            await loadSection();
            message(result.message || 'Perubahan berhasil disimpan.');
        } catch (error) {
            message(error.message || 'Terjadi kesalahan saat menghubungi server.', true);
        } finally {
            if (button) button.disabled = false;
        }
    });

    root.addEventListener('click', async (event) => {
        const marketingSppButton = event.target.closest('[data-marketing-spp-edit]');
        if (marketingSppButton) {
            const payload = JSON.parse(marketingSppButton.dataset.payload);
            marketingSppFields.replaceChildren();
            const addField = (name, label, type, options = []) => {
                const wrapper = document.createElement('div'); wrapper.className = 'mb-3';
                const caption = document.createElement('label'); caption.className = 'form-label'; caption.textContent = label; caption.htmlFor = `marketing-spp-${name}`;
                const input = document.createElement(type === 'select' ? 'select' : 'input'); input.className = type === 'select' ? 'form-select' : 'form-control'; input.id = caption.htmlFor; input.name = name; input.required = true;
                if (type === 'number') { input.type = 'number'; input.min = '0'; input.step = '1000'; }
                if (type === 'select') {
                    const placeholder = document.createElement('option'); placeholder.value = ''; placeholder.textContent = `Pilih ${label.toLowerCase()}`; input.append(placeholder);
                    options.forEach(([value, text]) => { const option = document.createElement('option'); option.value = value; option.textContent = text; input.append(option); });
                }
                wrapper.append(caption, input); marketingSppFields.append(wrapper); return input;
            };
            const marketing = document.createElement('div'); marketing.className = 'alert alert-primary py-2'; marketing.textContent = `Marketing: ${payload.marketing_name}`; marketingSppFields.append(marketing);
            const referral = document.createElement('input'); referral.type = 'hidden'; referral.name = 'referral_id'; referral.value = payload.referral_id; marketingSppFields.append(referral);
            addField('period_id', 'Periode', 'select', payload.periods);
            addField('path_id', 'Jalur pendaftaran', 'select', payload.paths);
            addField('nominal_spp', 'Harga SPP (Rp)', 'number');
            marketingSppModal.show();
            return;
        }

        const formToggle = event.target.closest('[data-marketing-form-toggle]');
        if (formToggle) {
            const panel = document.getElementById('marketing-registration-form-panel');
            if (panel) {
                const show = formToggle.dataset.marketingFormToggle === 'show';
                panel.hidden = !show;
                panel.classList.toggle('d-none', !show);
                panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
            return;
        }

        const registrationDetailButton = event.target.closest('[data-marketing-registration-detail]');
        if (registrationDetailButton) {
            marketingDetailModalElement.dataset.detailUrl = registrationDetailButton.dataset.detailUrl;
            marketingDetailModal.show();
            marketingDetailBody.innerHTML = '<div class="text-center text-muted py-5"><span class="spinner-border spinner-border-sm me-2"></span>Memuat biodata dan berkas...</div>';
            try {
                const response = await fetch(registrationDetailButton.dataset.detailUrl, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                });
                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Detail pendaftar gagal dimuat.');
                marketingDetailBody.innerHTML = result.html;
                if (registrationDetailButton.dataset.focusTab) {
                    const tab = marketingDetailBody.querySelector(`#${registrationDetailButton.dataset.focusTab}-tab`);
                    if (tab) bootstrap.Tab.getOrCreateInstance(tab).show();
                }
            } catch (error) {
                const alert = document.createElement('div');
                alert.className = 'alert alert-danger mb-0';
                alert.textContent = error.message || 'Detail pendaftar gagal dimuat.';
                marketingDetailBody.replaceChildren(alert);
            }
            return;
        }

        const documentButton = event.target.closest('[data-document-review]');
        if (documentButton) {
            documentReviewForm && (documentReviewForm.action = documentButton.dataset.reviewUrl);
            documentModalElement.querySelector('#pmb-document-review-title').textContent = documentButton.dataset.documentName;
            documentModalElement.querySelector('#pmb-document-review-status').textContent = `Status saat ini: ${documentButton.dataset.documentStatus}`;
            documentModalElement.querySelector('#pmb-document-review-download').href = documentButton.dataset.documentUrl;
            const preview = documentModalElement.querySelector('#pmb-document-preview');
            preview.replaceChildren();
            const extension = documentButton.dataset.documentExtension;
            const previewUrl = documentButton.dataset.documentPreviewUrl;
            if (['jpg', 'jpeg', 'png', 'webp'].includes(extension)) {
                const image = document.createElement('img');
                image.src = previewUrl;
                image.alt = `Pratinjau ${documentButton.dataset.documentName}`;
                preview.append(image);
            } else if (extension === 'pdf') {
                const frame = document.createElement('iframe');
                frame.src = previewUrl;
                frame.title = `Pratinjau ${documentButton.dataset.documentName}`;
                frame.loading = 'lazy';
                preview.append(frame);
            } else {
                preview.textContent = 'Format ini belum mendukung pratinjau. Silakan unduh berkas.';
            }
            if (documentReviewForm) {
                documentReviewForm.reset();
                documentReviewForm.querySelector('[name="status_verifikasi"]').value = '';
            }
            documentModal.show();
            return;
        }

        const claimLinkButton = event.target.closest('[data-marketing-claim-link]');
        if (claimLinkButton) {
            const confirmation = await Swal.fire({
                title: 'Buat link klaim baru?',
                text: 'Link yang dibuat sebelumnya akan langsung tidak berlaku.',
                icon: 'question', showCancelButton: true,
                confirmButtonText: 'Buat link baru', cancelButtonText: 'Batal',
                confirmButtonColor: '#5d87ff',
            });
            if (!confirmation.isConfirmed) return;
            claimLinkButton.disabled = true;
            try {
                const response = await fetch(claimLinkButton.dataset.linkUrl, {
                    method: 'POST',
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf },
                    credentials: 'same-origin',
                });
                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Link klaim gagal dibuat.');
                showMarketingClaimLink(result);
                message(result.message || 'Link klaim berhasil dibuat.');
                await loadSection();
            } catch (error) {
                message(error.message || 'Link klaim gagal dibuat.', true);
                claimLinkButton.disabled = false;
            }
            return;
        }

        const copyButton = event.target.closest('[data-copy-link]');
        if (copyButton) {
            try {
                await navigator.clipboard.writeText(copyButton.dataset.copyLink);
                message(copyButton.id === 'pmb-marketing-copy-claim' ? 'Link klaim berhasil disalin.' : 'Link referral berhasil disalin.');
            } catch (error) { message('Gagal menyalin link.', true); }
            return;
        }

        const editButton = event.target.closest('[data-pmb-edit]');
        if (editButton) {
            const payload = JSON.parse(editButton.dataset.payload);
            editForm.action = editButton.dataset.updateUrl;
            editFields.replaceChildren();
            (schemas[editButton.dataset.editSchema || activeSection] || []).forEach(([name, label, type, choices]) => {
                const wrapper = document.createElement('div');
                wrapper.className = 'mb-3';
                const caption = document.createElement('label');
                caption.className = 'form-label';
                caption.textContent = label;
                const input = document.createElement(type === 'textarea' ? 'textarea' : (type === 'select' ? 'select' : 'input'));
                input.className = 'form-control';
                input.name = name;
                if (type === 'number') { input.type = 'number'; input.min = '0'; input.step = '1000'; }
                if (type === 'date') input.type = 'date';
                if (type === 'select') {
                    input.classList.add('form-select');
                    const options = choices || payload.options?.[name] || [];
                    options.forEach(([value, text]) => {
                        const option = document.createElement('option'); option.value = value; option.textContent = text; input.append(option);
                    });
                }
                input.value = payload.record[name] ?? '';
                caption.htmlFor = `pmb-edit-${name}`; input.id = caption.htmlFor;
                wrapper.append(caption, input); editFields.append(wrapper);
            });
            editModal.show();
            return;
        }

        const deleteButton = event.target.closest('[data-pmb-delete]');
        if (deleteButton) {
            const confirm = await Swal.fire({
                title: 'Hapus data ini?', text: 'Tindakan ini tidak dapat dibatalkan.', icon: 'warning',
                showCancelButton: true, confirmButtonText: 'Ya, hapus', cancelButtonText: 'Batal',
                confirmButtonColor: '#fa896b',
            });
            if (!confirm.isConfirmed) return;
            try {
                const response = await fetch(deleteButton.dataset.deleteUrl, {
                    method: 'DELETE', headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf },
                    credentials: 'same-origin',
                });
                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Data gagal dihapus.');
                await loadSection(); message(result.message || 'Data berhasil dihapus.');
            } catch (error) { message(error.message, true); }
            return;
        }

    });

    marketingDetailBody.addEventListener('submit', async (event) => {
        const form = event.target.closest('[data-marketing-file-upload]');
        if (!form) return;
        event.preventDefault();
        const activeTabTarget = marketingDetailBody.querySelector('.nav-link.active')?.dataset.bsTarget;
        const submitButton = form.querySelector('[type="submit"]');
        const loadingIndicator = form.querySelector('[data-upload-loading]');
        form.classList.add('is-uploading');
        submitButton.disabled = true;
        if (loadingIndicator) loadingIndicator.hidden = false;
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf },
                credentials: 'same-origin',
            });
            const result = await response.json();
            if (!response.ok) {
                const errors = Object.values(result.errors || {}).flat();
                throw new Error(errors.join(' ') || result.message || 'Berkas gagal diunggah.');
            }

            const detailResponse = await fetch(marketingDetailModalElement.dataset.detailUrl, {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            const detail = await detailResponse.json();
            if (!detailResponse.ok) throw new Error(detail.message || 'File tersimpan, tetapi detail belum dapat dimuat ulang.');
            marketingDetailBody.innerHTML = detail.html;
            if (activeTabTarget) {
                const activeTab = marketingDetailBody.querySelector(`[data-bs-target="${activeTabTarget}"]`);
                if (activeTab) bootstrap.Tab.getOrCreateInstance(activeTab).show();
            }
            const notice = document.createElement('div');
            notice.className = 'alert alert-success py-2';
            notice.textContent = result.message || 'File berhasil diunggah.';
            marketingDetailBody.prepend(notice);
        } catch (error) {
            const notice = document.createElement('div');
            notice.className = 'alert alert-danger py-2';
            notice.textContent = error.message || 'File gagal diunggah.';
            marketingDetailBody.prepend(notice);
            submitButton.disabled = false;
            if (loadingIndicator) loadingIndicator.hidden = true;
            form.classList.remove('is-uploading');
        }
    });

    marketingDetailBody.addEventListener('change', (event) => {
        const input = event.target.closest('input[type="file"]');
        if (!input) return;
        const zone = input.closest('form')?.querySelector('[data-upload-dropzone]');
        const filename = zone?.querySelector('[data-upload-filename]');
        const file = input.files?.[0];
        if (filename) filename.textContent = file ? file.name : 'Seret file ke sini atau klik untuk memilih';
        zone?.classList.toggle('is-selected', Boolean(file));
    });

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach((eventName) => {
        marketingDetailBody.addEventListener(eventName, (event) => {
            const zone = event.target.closest('[data-upload-dropzone]');
            if (!zone) return;
            event.preventDefault();
            if (eventName === 'dragenter' || eventName === 'dragover') zone.classList.add('is-dragover');
            if (eventName === 'dragleave' || eventName === 'drop') zone.classList.remove('is-dragover');
            if (eventName === 'drop') {
                const input = document.getElementById(zone.htmlFor);
                if (input && event.dataTransfer?.files?.length) {
                    input.files = event.dataTransfer.files;
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }
        });
    });

    root.addEventListener('submit', async (event) => {
        const form = event.target.closest('[data-marketing-registration-form]');
        if (!form) return;

        event.preventDefault();
        const submitButton = form.querySelector('[type="submit"]');
        const loadingIndicator = form.querySelector('[data-registration-loading]');
        submitButton.disabled = true;
        if (loadingIndicator) loadingIndicator.hidden = false;
        feedback.className = 'alert d-none';
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf },
                credentials: 'same-origin',
            });
            const result = await response.json();
            if (!response.ok) {
                const errors = Object.values(result.errors || {}).flat();
                throw new Error(errors.join(' ') || result.message || 'Pendaftaran calon mahasiswa gagal dibuat.');
            }
            await loadSection();
            showMarketingClaimLink(result);
            message(result.message || 'Pendaftar berhasil dibuat.');
        } catch (error) {
            message(error.message || 'Pendaftaran calon mahasiswa gagal dibuat.', true);
            submitButton.disabled = false;
            if (loadingIndicator) loadingIndicator.hidden = true;
        }
    });

    root.addEventListener('change', (event) => {
        if (event.target.id !== 'marketing-fee') return;
        const pathId = event.target.selectedOptions[0]?.dataset.pathId;
        const form = event.target.closest('form');
        form?.querySelectorAll('[data-requirement-path]').forEach((field) => {
            const visible = Boolean(pathId) && field.dataset.requirementPath === pathId;
            field.hidden = !visible;
            const input = field.querySelector('input[type="file"]');
            if (input) {
                input.disabled = !visible;
                if (!visible) input.value = '';
            }
        });
    });

    root.addEventListener('shown.bs.tab', (event) => {
        if (!['fees', 'referrals'].includes(activeSection) || !window.jQuery || !$.fn.dataTable) return;
        window.setTimeout(() => {
            $(content).find('.tab-pane.active .pmb-data-table').each(function () {
                if ($.fn.dataTable.isDataTable(this)) $(this).DataTable().columns.adjust().draw(false);
            });
        }, 0);
    });

    if (documentReviewForm) {
        documentReviewForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const submitButton = event.submitter;
            const status = submitButton?.value;
            const note = documentReviewForm.querySelector('[name="catatan_admin"]');
            if (!status) return;
            if (status === 'invalid' && !note.value.trim()) {
                note.focus();
                message('Isi catatan agar pendaftar mengetahui perbaikan yang diperlukan.', true);
                return;
            }
            const body = new FormData(documentReviewForm);
            body.set('status_verifikasi', status);
            body.append('_method', 'PATCH');
            submitButton.disabled = true;
            try {
                const response = await fetch(documentReviewForm.action, {
                    method: 'POST', body,
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf },
                    credentials: 'same-origin',
                });
                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Status berkas gagal diperbarui.');
                documentModal.hide();
                await loadSection();
                message(result.message || 'Status berkas berhasil diperbarui.');
            } catch (error) {
                message(error.message || 'Status berkas gagal diperbarui.', true);
            } finally {
                submitButton.disabled = false;
            }
        });
    }

    marketingSppForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const submit = marketingSppForm.querySelector('[type="submit"]'); submit.disabled = true;
        try {
            const response = await fetch(marketingSppForm.action, {
                method: 'POST', body: new FormData(marketingSppForm),
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf },
                credentials: 'same-origin',
            });
            const result = await response.json();
            if (!response.ok) {
                const errors = Object.values(result.errors || {}).flat();
                throw new Error(errors.join(' ') || result.message || 'Harga SPP gagal disimpan.');
            }
            marketingSppModal.hide();
            await loadSection();
            referralActiveTab = '#pmb-referrals-spp-pane';
            const sppTab = content.querySelector('.pmb-fee-tabs [data-bs-target="#pmb-referrals-spp-pane"]');
            if (sppTab) bootstrap.Tab.getOrCreateInstance(sppTab).show();
            message(result.message || 'Harga SPP marketing berhasil disimpan.');
        } catch (error) { message(error.message || 'Harga SPP gagal disimpan.', true); }
        finally { submit.disabled = false; }
    });

    editForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const submit = editForm.querySelector('[type="submit"]'); submit.disabled = true;
        const body = new FormData(editForm); body.append('_method', 'PUT');
        try {
            const response = await fetch(editForm.action, {
                method: 'POST', body,
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf },
                credentials: 'same-origin',
            });
            const result = await response.json();
            if (!response.ok) {
                const errors = Object.values(result.errors || {}).flat();
                throw new Error(errors.join(' ') || result.message || 'Perubahan gagal disimpan.');
            }
            editModal.hide(); await loadSection(); message(result.message || 'Perubahan berhasil disimpan.');
        } catch (error) { message(error.message, true); }
        finally { submit.disabled = false; }
    });

    loadSection().catch((error) => message(error.message, true));
})();
</script>
@endpush
