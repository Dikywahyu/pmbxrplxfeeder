<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3"><div class="card bg-primary-subtle shadow-none mb-0"><div class="card-body"><span class="text-muted">Total pendaftar</span><h3 class="mb-0">{{ array_sum($registrationCounts->all()) }}</h3></div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card bg-warning-subtle shadow-none mb-0"><div class="card-body"><span class="text-muted">Menunggu verifikasi</span><h3 class="mb-0">{{ $paymentCounts['menunggu_verifikasi'] ?? 0 }}</h3></div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card bg-success-subtle shadow-none mb-0"><div class="card-body"><span class="text-muted">Lulus seleksi</span><h3 class="mb-0">{{ $registrationCounts['lulus'] ?? 0 }}</h3></div></div></div>
    <div class="col-sm-6 col-xl-3"><div class="card bg-danger-subtle shadow-none mb-0"><div class="card-body"><span class="text-muted">Pembayaran ditolak</span><h3 class="mb-0">{{ $paymentCounts['ditolak'] ?? 0 }}</h3></div></div></div>
</div>
<div class="card border mb-0"><div class="card-body">
    <h5 class="card-title">Status pendaftaran</h5>
    <p class="text-muted">Distribusi calon mahasiswa menurut tahap seleksi.</p>
    <div id="pmb-status-chart"></div>
</div></div>
