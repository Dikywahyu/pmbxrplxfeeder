<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card pmb-stat-card pmb-stat-blue mb-0"><div class="card-body position-relative" style="z-index:1">
            <div class="d-flex justify-content-between align-items-start mb-3"><span class="pmb-stat-label">Total pendaftar</span><span class="pmb-stat-icon"><i class="ti ti-users"></i></span></div>
            <h3 class="mb-1">{{ array_sum($registrationCounts->all()) }}</h3><small>Seluruh pendaftaran</small>
        </div></div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card pmb-stat-card pmb-stat-amber mb-0"><div class="card-body position-relative" style="z-index:1">
            <div class="d-flex justify-content-between align-items-start mb-3"><span class="pmb-stat-label">Verifikasi bayar</span><span class="pmb-stat-icon"><i class="ti ti-receipt"></i></span></div>
            <h3 class="mb-1">{{ $paymentCounts['menunggu_verifikasi'] ?? 0 }}</h3><small>Menunggu pemeriksaan</small>
        </div></div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card pmb-stat-card pmb-stat-cyan mb-0"><div class="card-body position-relative" style="z-index:1">
            <div class="d-flex justify-content-between align-items-start mb-3"><span class="pmb-stat-label">Sudah ujian</span><span class="pmb-stat-icon"><i class="ti ti-clipboard-check"></i></span></div>
            <h3 class="mb-1">{{ $registrationCounts['sudah_ujian'] ?? 0 }}</h3><small>Peserta menyelesaikan ujian</small>
        </div></div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card pmb-stat-card pmb-stat-green mb-0"><div class="card-body position-relative" style="z-index:1">
            <div class="d-flex justify-content-between align-items-start mb-3"><span class="pmb-stat-label">Lulus seleksi</span><span class="pmb-stat-icon"><i class="ti ti-award"></i></span></div>
            <h3 class="mb-1">{{ $registrationCounts['lulus'] ?? 0 }}</h3><small>Lulus tahap seleksi</small>
        </div></div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card pmb-stat-card pmb-stat-violet mb-0"><div class="card-body position-relative" style="z-index:1">
            <div class="d-flex justify-content-between align-items-start mb-3"><span class="pmb-stat-label">Diterima</span><span class="pmb-stat-icon"><i class="ti ti-school"></i></span></div>
            <h3 class="mb-1">{{ $registrationCounts['diterima'] ?? 0 }}</h3><small>Calon mahasiswa diterima</small>
        </div></div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card pmb-stat-card pmb-stat-red mb-0"><div class="card-body position-relative" style="z-index:1">
            <div class="d-flex justify-content-between align-items-start mb-3"><span class="pmb-stat-label">Ditolak / mundur</span><span class="pmb-stat-icon"><i class="ti ti-user-x"></i></span></div>
            <h3 class="mb-1">{{ ($registrationCounts['ditolak'] ?? 0) + ($registrationCounts['mundur'] ?? 0) }}</h3><small>Perlu tindak lanjut</small>
        </div></div>
    </div>
</div>

<div class="card pmb-overview-chart mb-0">
    <div class="card-header px-4 py-3">
        <div class="d-flex align-items-center gap-3">
            <span class="pmb-stat-icon bg-primary-subtle text-primary rounded-3 d-grid" style="width:42px;height:42px;place-items:center"><i class="ti ti-chart-donut-3 fs-5"></i></span>
            <div><h5 class="card-title mb-1">Perkembangan pendaftaran</h5><p class="text-muted small mb-0">Distribusi pendaftar di setiap tahap seleksi.</p></div>
        </div>
    </div>
    <div class="card-body px-4 py-3"><div id="pmb-status-chart"></div></div>
</div>
