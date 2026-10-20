@extends('layouts.app')

@section('title', 'Administrasi PMB')

@section('content')
@push('styles')
<style>
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
    .pmb-document-preview { display: flex; min-height: 220px; max-height: 68vh; align-items: center; justify-content: center; overflow: auto; border: 1px solid #e8edf5; border-radius: .75rem; background: #f5f7fb; }
    .pmb-document-preview:empty::before { content: 'Pratinjau berkas akan tampil di sini'; color: #8490a3; font-size: .9rem; }
    .pmb-document-preview img { display: block; max-width: 100%; max-height: 68vh; object-fit: contain; }
    .pmb-document-preview iframe { display: block; width: 100%; height: 68vh; min-height: 420px; border: 0; background: #fff; }
    .pmb-status-select { min-width: 145px; border-color: #dce4f0; font-size: .82rem; }
    .pmb-status-select:focus { border-color: #5d87ff; box-shadow: 0 0 0 .2rem rgba(93, 135, 255, .14); }
    @media (max-width: 991.98px) {
        .pmb-registrations-responsive { overflow: visible; border: 0; }
        .pmb-registrations-responsive table { min-width: 0 !important; width: 100% !important; }
        .pmb-registrations-responsive thead { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0; }
        .pmb-registrations-responsive tbody { display: block; }
        .pmb-registrations-responsive tbody tr { display: block; margin-bottom: .9rem; padding: .35rem .75rem; border: 1px solid #e8edf5; border-radius: .85rem; background: #fff; box-shadow: 0 4px 14px rgba(31, 54, 91, .06); }
        .pmb-registrations-responsive tbody td { display: flex !important; align-items: flex-start; justify-content: space-between; gap: 1rem; width: 100% !important; padding: .65rem 0; border: 0 !important; border-bottom: 1px solid #f0f2f6 !important; white-space: normal !important; }
        .pmb-registrations-responsive tbody td::before { content: attr(data-label); flex: 0 0 32%; color: #7a869a; font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .025em; }
        .pmb-registrations-responsive tbody td[data-label="Status seleksi"] form { flex: 1; }
        .pmb-registrations-responsive tbody td[data-label="Status seleksi"] select { width: 100%; }
        .pmb-registrations-responsive tbody td:last-child { border-bottom: 0 !important; }
        .pmb-document-chip { white-space: normal; }
    }
</style>
@endpush
<div class="container-fluid" id="pmb-admin"
    data-section="{{ $section }}"
    data-data-url="{{ route('admin.pmb.data', ['section' => '__SECTION__']) }}">
    <div class="card bg-info-subtle shadow-none position-relative overflow-hidden mb-4">
        <div class="card-body px-4 py-3">
            <div class="row align-items-center">
                <div class="col-9">
                    @php
                        $sectionTitles = ['overview' => 'Ringkasan PMB', 'registrations' => 'Data Pendaftar', 'periods' => 'Periode Pendaftaran', 'paths' => 'Jalur Pendaftaran', 'programs' => 'Program Studi', 'fees' => 'Pengaturan Biaya', 'referrals' => 'Marketing & Referral', 'requirements' => 'Persyaratan Berkas'];
                    @endphp
                    <h4 class="fw-semibold mb-1">{{ $sectionTitles[$section] ?? 'Administrasi PMB' }}</h4>
                    <p class="text-muted mb-0">Administrasi penerimaan mahasiswa baru.</p>
                </div>
                <div class="col-3 text-center"><i class="ti ti-school text-primary" style="font-size:3rem"></i></div>
            </div>
        </div>
    </div>

    <div id="pmb-feedback" class="alert d-none" role="alert" aria-live="polite"></div>

    <div class="card">
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

@endsection

@push('scripts')
<script>
(() => {
    const root = document.getElementById('pmb-admin');
    if (!root) return;

    const content = document.getElementById('pmb-content');
    const feedback = document.getElementById('pmb-feedback');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    let activeSection = root.dataset.section;
    let chart = null;
    let feeBulkSelection = null;
    const editModal = new bootstrap.Modal(document.getElementById('pmb-edit-modal'));
    const editForm = document.getElementById('pmb-edit-form');
    const editFields = document.getElementById('pmb-edit-fields');
    const documentModalElement = document.getElementById('pmb-document-review-modal');
    const documentModal = new bootstrap.Modal(documentModalElement);
    const documentReviewForm = document.getElementById('pmb-document-review-form');
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
            ['status', 'Status pendaftaran', 'select', [['draft', 'Draft'], ['berkas_masuk', 'Berkas masuk'], ['review', 'Dalam review'], ['sudah_ujian', 'Sudah ujian'], ['lulus', 'Lulus'], ['diterima', 'Diterima'], ['ditolak', 'Ditolak'], ['mundur', 'Mundur']]],
        ],
        fees: [
            ['period_id', 'Periode', 'select'], ['path_id', 'Jalur', 'select'], ['program_id', 'Program studi', 'select'],
            ['nominal_pendaftaran', 'Biaya pendaftaran (Rp)', 'number'], ['nominal_spp', 'SPP (Rp)', 'number'],
        ],
        requirements: [
            ['path_id', 'Jalur pendaftaran', 'select'], ['nama_dokumen', 'Nama berkas', 'text'],
            ['is_required', 'Wajib diunggah', 'select', [['1', 'Ya'], ['0', 'Tidak']]],
            ['format_file', 'Format berkas (pisahkan koma)', 'text'],
        ],
    };

    const message = (text, error = false) => {
        feedback.textContent = text;
        feedback.className = `alert alert-${error ? 'danger' : 'success'}`;
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
        initializeTables();
        if (section === 'fees') setupFeeBulk();
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
                    colors: ['#6c757d', '#539bff', '#ffae1f', '#13deb9', '#fa896b'],
                });
                chart.render();
            }
        }
    };

    document.getElementById('pmb-reload').addEventListener('click', async () => {
        try { await loadSection(); message('Data berhasil diperbarui.'); }
        catch (error) { message(error.message, true); }
    });

    root.addEventListener('change', async (event) => {
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

        const copyButton = event.target.closest('[data-copy-link]');
        if (copyButton) {
            try {
                await navigator.clipboard.writeText(copyButton.dataset.copyLink);
                message('Link referral berhasil disalin.');
            } catch (error) { message('Gagal menyalin link referral.', true); }
            return;
        }

        const editButton = event.target.closest('[data-pmb-edit]');
        if (editButton) {
            const payload = JSON.parse(editButton.dataset.payload);
            editForm.action = editButton.dataset.updateUrl;
            editFields.replaceChildren();
            (schemas[activeSection] || []).forEach(([name, label, type, choices]) => {
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
