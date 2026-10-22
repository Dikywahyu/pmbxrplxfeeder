@php($reportPeriodId = (string) optional($periods->firstWhere('is_active', true) ?? $periods->first())->id)
<div class="card border-0 shadow-sm mb-4 overflow-hidden">
    <div class="card-header border-0 bg-primary-subtle d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
        <div><h5 class="card-title mb-1"><i class="ti ti-chart-bar me-2 text-primary"></i>Laporan pendaftar per jalur</h5><small class="text-muted">Bandingkan jumlah pendaftar berdasarkan periode yang dipilih.</small></div>
        <div class="d-flex align-items-center gap-2"><label class="small fw-semibold text-muted" for="pmb-path-report-period">Periode</label><select class="form-select form-select-sm" id="pmb-path-report-period" style="min-width:190px"><option value="all">Semua periode</option>@foreach($periods as $period)<option value="{{ $period->id }}" @selected((string) $period->id === $reportPeriodId)>{{ $period->nama_periode }}</option>@endforeach</select></div>
    </div>
    <div class="card-body">
        <div id="pmb-path-registration-chart" data-path-ids="{{ json_encode($paths->pluck('id')->map(fn($id) => (string) $id)->values()) }}" data-path-labels="{{ json_encode($paths->pluck('nama_jalur')->values(), JSON_HEX_APOS | JSON_HEX_QUOT) }}" data-counts="{{ json_encode($registrationCounts) }}" data-selected-period="{{ $reportPeriodId }}"></div>
        @if($paths->isEmpty())<div class="text-center text-muted py-4">Tambahkan jalur pendaftaran untuk melihat laporan.</div>@endif
    </div>
</div>

<ul class="nav nav-pills pmb-fee-tabs gap-2 mb-3" id="pmb-fee-tabs" role="tablist">
    <li class="nav-item" role="presentation"><button class="nav-link active" id="pmb-fee-bulk-tab" data-bs-toggle="pill" data-bs-target="#pmb-fee-bulk-pane" type="button" role="tab"><i class="ti ti-cash-banknote me-1"></i>Biaya semua prodi</button></li>
    <li class="nav-item" role="presentation"><button class="nav-link" id="pmb-fee-schedule-tab" data-bs-toggle="pill" data-bs-target="#pmb-fee-schedule-pane" type="button" role="tab"><i class="ti ti-calendar-event me-1"></i>Jadwal jalur <span class="badge bg-white text-primary ms-1">{{ $schedules->count() }}</span></button></li>
    <li class="nav-item" role="presentation"><button class="nav-link" id="pmb-fee-list-tab" data-bs-toggle="pill" data-bs-target="#pmb-fee-list-pane" type="button" role="tab"><i class="ti ti-list-details me-1"></i>Daftar biaya <span class="badge bg-white text-primary ms-1">{{ $fees->count() }}</span></button></li>
</ul>
<div class="tab-content" id="pmb-fee-tab-content">
<div class="tab-pane fade show active" id="pmb-fee-bulk-pane" role="tabpanel" aria-labelledby="pmb-fee-bulk-tab">
<div class="card border-0 shadow-sm mb-4"><div class="card-header bg-transparent border-bottom"><h5 class="card-title mb-1"><i class="ti ti-cash-banknote me-2 text-primary"></i>Biaya semua program studi</h5><small class="text-muted">Pilih periode dan jalur sekali, lalu simpan biaya untuk seluruh prodi sekaligus.</small></div><div class="card-body">
    <form id="pmb-fee-bulk-form" method="POST" action="{{ route('admin.pmb.fees.bulk') }}" data-ajax-form data-fees="{{ json_encode($fees->map(fn($fee) => ['period_id' => $fee->period_id, 'path_id' => $fee->path_id, 'program_id' => $fee->program_id, 'nominal_pendaftaran' => $fee->nominal_pendaftaran, 'nominal_spp' => $fee->nominal_spp])->values()) }}">
        @csrf
        <div class="row g-3 mb-4">
            <div class="col-md-5"><label class="form-label" for="fee-bulk-period">Periode</label><select class="form-select" id="fee-bulk-period" name="period_id" required>@include('admin.pmb.partials.period-options')</select></div>
            <div class="col-md-5"><label class="form-label" for="fee-bulk-path">Jalur pendaftaran</label><select class="form-select" id="fee-bulk-path" name="path_id" required>@include('admin.pmb.partials.path-options')</select></div>
        </div>
        <div class="table-responsive"><table class="table table-sm table-hover align-middle w-100">
            <thead class="table-light"><tr><th>Program studi</th><th>Kode</th><th>Biaya pendaftaran (Rp)</th><th>SPP (Rp)</th><th>Status data</th></tr></thead>
            <tbody>
                @foreach($programs as $index => $program)
                    <tr data-fee-row data-program-id="{{ $program->id }}">
                        <td class="fw-semibold">{{ $program->nama_prodi }} <span class="text-muted">({{ $program->jenjang }})</span></td>
                        <td>{{ $program->kode_prodi }}</td>
                        <td><input class="form-control form-control-sm" type="number" name="fees[{{ $index }}][nominal_pendaftaran]" min="0" step="1000" placeholder="0" required><input type="hidden" name="fees[{{ $index }}][program_id]" value="{{ $program->id }}"></td>
                        <td><input class="form-control form-control-sm" type="number" name="fees[{{ $index }}][nominal_spp]" min="0" step="1000" placeholder="0" required></td>
                        <td><span class="badge bg-secondary-subtle text-secondary" data-fee-state>Pilih periode dan jalur</span></td>
                    </tr>
                @endforeach
                @if($programs->isEmpty())<tr><td colspan="5" class="text-center text-muted">Tambahkan program studi terlebih dahulu.</td></tr>@endif
            </tbody>
        </table></div>
        @if($programs->isNotEmpty())
            <button class="btn btn-primary" type="submit"><i class="ti ti-device-floppy me-1"></i>Simpan biaya semua prodi</button>
        @endif
    </form>
</div></div>

 </div>
<div class="tab-pane fade" id="pmb-fee-schedule-pane" role="tabpanel" aria-labelledby="pmb-fee-schedule-tab">
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent d-flex align-items-start gap-2"><span class="d-grid rounded-3 bg-info-subtle text-info flex-shrink-0" style="width:40px;height:40px;place-items:center"><i class="ti ti-calendar-event fs-5"></i></span><div><h5 class="card-title mb-1">Jadwal pembukaan per jalur</h5><small class="text-muted">Atur tanggal buka dan tutup untuk setiap kombinasi periode dan jalur. Jumlah pendaftar ditampilkan pada tabel.</small></div></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.pmb.schedules.save') }}" data-ajax-form class="row g-3 align-items-end mb-4">
            @csrf
            <div class="col-12 col-md-3"><label class="form-label" for="schedule-period">Periode</label><select class="form-select" id="schedule-period" name="period_id" required>@include('admin.pmb.partials.period-options')</select></div>
            <div class="col-12 col-md-3"><label class="form-label" for="schedule-path">Jalur</label><select class="form-select" id="schedule-path" name="path_id" required>@include('admin.pmb.partials.path-options')</select></div>
            <div class="col-6 col-md-2"><label class="form-label" for="schedule-start">Mulai dibuka</label><input class="form-control" id="schedule-start" type="date" name="tanggal_mulai" required></div>
            <div class="col-6 col-md-2"><label class="form-label" for="schedule-end">Tutup pendaftaran</label><input class="form-control" id="schedule-end" type="date" name="tanggal_selesai" required></div>
            <div class="col-6 col-md-1"><label class="form-label" for="schedule-active">Status</label><select class="form-select" id="schedule-active" name="is_active" required><option value="1">Aktif</option><option value="0">Nonaktif</option></select></div>
            <div class="col-6 col-md-1"><button class="btn btn-primary w-100" type="submit"><i class="ti ti-device-floppy"></i></button></div>
        </form>
        <div class="table-responsive"><table class="table table-sm table-hover align-middle w-100 pmb-data-table">
            <thead class="table-light"><tr><th>Periode</th><th>Jalur</th><th>Jadwal pembukaan</th><th>Pendaftar</th><th>Status</th><th data-orderable="false" data-searchable="false">Aksi</th></tr></thead><tbody>
                @foreach($schedules as $schedule)
                    <tr>
                        <td>{{ $schedule->period?->nama_periode }}</td><td>{{ $schedule->path?->nama_jalur }}</td>
                        <td>{{ $schedule->tanggal_mulai->format('d/m/Y') }} – {{ $schedule->tanggal_selesai->format('d/m/Y') }}</td>
                        <td><span class="badge rounded-pill bg-primary-subtle text-primary"><i class="ti ti-users me-1"></i>{{ $registrationCounts->get("{$schedule->period_id}:{$schedule->path_id}", 0) }}</span></td>
                        <td><span class="badge {{ $schedule->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">{{ $schedule->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td><button type="button" class="btn btn-sm btn-light-primary text-primary" data-pmb-edit data-edit-schema="schedule" data-update-url="{{ route('admin.pmb.schedules.update') }}" data-payload="{{ json_encode(['record' => ['period_id' => (string) $schedule->period_id, 'path_id' => (string) $schedule->path_id, 'tanggal_mulai' => $schedule->tanggal_mulai->format('Y-m-d'), 'tanggal_selesai' => $schedule->tanggal_selesai->format('Y-m-d'), 'is_active' => $schedule->is_active ? '1' : '0'], 'options' => ['period_id' => $periods->map(fn($item) => [(string) $item->id, $item->nama_periode])->values(), 'path_id' => $paths->map(fn($item) => [(string) $item->id, $item->nama_jalur])->values()]]) }}"><i class="ti ti-edit"></i> Ubah jadwal</button></td>
                    </tr>
                @endforeach
            </tbody>
        </table></div>
    </div>
</div>

 </div>
<div class="tab-pane fade" id="pmb-fee-list-pane" role="tabpanel" aria-labelledby="pmb-fee-list-tab">
<div class="card border-0 shadow-sm mb-0"><div class="card-header bg-transparent border-bottom"><h5 class="card-title mb-1"><i class="ti ti-list-details me-2 text-info"></i>Daftar biaya</h5><small class="text-muted">Edit atau hapus satu kombinasi biaya dari tabel ini.</small></div><div class="card-body">
    <div class="table-responsive"><table class="table table-sm table-hover align-middle w-100 pmb-data-table"><thead class="table-light"><tr><th>Periode</th><th>Jalur</th><th>Program studi</th><th>Pendaftaran</th><th>SPP</th><th class="text-end" data-orderable="false" data-searchable="false">Aksi</th></tr></thead><tbody>
        @foreach($fees as $fee)
            <tr>
                <td>{{ $fee->period?->nama_periode ?? 'Periode tidak tersedia' }}</td><td>{{ $fee->path?->nama_jalur ?? 'Jalur tidak tersedia' }}</td><td class="fw-semibold">{{ $fee->program?->nama_prodi ?? 'Prodi tidak tersedia' }}</td>
                <td>Rp {{ number_format($fee->nominal_pendaftaran, 0, ',', '.') }}</td><td>Rp {{ number_format($fee->nominal_spp, 0, ',', '.') }}</td>
                <td class="text-end text-nowrap">
                    <button type="button" class="btn btn-sm btn-light-primary text-primary" data-pmb-edit data-update-url="{{ route('admin.pmb.fees.update', $fee->id) }}" data-payload="{{ json_encode(['record' => ['period_id' => $fee->period_id, 'path_id' => $fee->path_id, 'program_id' => $fee->program_id, 'nominal_pendaftaran' => $fee->nominal_pendaftaran, 'nominal_spp' => $fee->nominal_spp], 'options' => ['period_id' => $periods->map(fn($item) => [$item->id, $item->nama_periode])->values(), 'path_id' => $paths->map(fn($item) => [$item->id, $item->nama_jalur])->values(), 'program_id' => $programs->map(fn($item) => [$item->id, $item->nama_prodi])->values()]]) }}"><i class="ti ti-edit"></i> Edit</button>
                    <button type="button" class="btn btn-sm btn-light-danger text-danger" data-pmb-delete data-delete-url="{{ route('admin.pmb.fees.destroy', $fee->id) }}"><i class="ti ti-trash"></i> Hapus</button>
                </td>
            </tr>
        @endforeach
    </tbody></table></div>
</div></div>
</div>
</div>
