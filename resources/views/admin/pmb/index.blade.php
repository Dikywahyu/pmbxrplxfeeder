@extends('layouts.app')

@section('title', 'Administrasi PMB')

@section('content')
<div class="container-fluid" id="pmb-admin"
    data-section="{{ $section }}"
    data-data-url="{{ route('admin.pmb.data', ['section' => '__SECTION__']) }}">
    <div class="card bg-info-subtle shadow-none position-relative overflow-hidden mb-4">
        <div class="card-body px-4 py-3">
            <div class="row align-items-center">
                <div class="col-9">
                    @php
                        $sectionTitles = ['overview' => 'Ringkasan PMB', 'registrations' => 'Data Pendaftar', 'periods' => 'Periode Pendaftaran', 'paths' => 'Jalur Pendaftaran', 'programs' => 'Program Studi', 'fees' => 'Pengaturan Biaya', 'referrals' => 'Marketing & Referral'];
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
            ['status', 'Status pendaftaran', 'select', [['draft', 'Draft'], ['berkas_masuk', 'Berkas masuk'], ['review', 'Dalam review'], ['lulus', 'Lulus'], ['ditolak', 'Ditolak']]],
        ],
        fees: [
            ['period_id', 'Periode', 'select'], ['path_id', 'Jalur', 'select'], ['program_id', 'Program studi', 'select'],
            ['nominal_pendaftaran', 'Biaya pendaftaran (Rp)', 'number'], ['nominal_spp', 'SPP (Rp)', 'number'],
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
                responsive: true,
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
