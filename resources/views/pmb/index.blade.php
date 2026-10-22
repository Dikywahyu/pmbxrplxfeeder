@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('dist/assets/libs/select2/dist/css/select2.min.css') }}">
<style>
    .pmb-select2-wrap .select2-container { width:100%!important; }
    .pmb-select2-wrap .select2-container .select2-selection--single { height:38px; border:1px solid #dfe5ef; border-radius:.375rem; }
    .pmb-select2-wrap .select2-container--default .select2-selection--single .select2-selection__rendered { line-height:36px; color:#5a6a85; padding-left:.75rem; }
    .pmb-select2-wrap .select2-container--default .select2-selection--single .select2-selection__arrow { height:36px; }
</style>
@endpush

@push('scripts')
<script src="{{ asset('dist/assets/libs/select2/dist/js/select2.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (!window.jQuery || !jQuery.fn.select2) return;
        const $wilayah = jQuery('[data-wilayah-select]');
        if (!$wilayah.length) return;
        $wilayah.select2({
            width: '100%',
            placeholder: 'Cari nama kecamatan...',
            allowClear: true,
            minimumInputLength: 2,
            ajax: {
                url: $wilayah.data('ajax-url'),
                dataType: 'json',
                delay: 250,
                data: (params) => ({ q: params.term || '', page: params.page || 1 }),
                processResults: (data) => data,
                cache: true,
            },
            language: { inputTooShort: () => 'Ketik minimal 2 karakter untuk mencari kecamatan.' },
        });

        document.querySelectorAll('[data-income-select]').forEach((select) => {
            const syncIncomeName = () => {
                const nameInput = document.querySelector(`[data-income-name="${select.dataset.incomeSelect}"]`);
                if (nameInput) nameInput.value = select.selectedOptions[0]?.dataset.name || '';
            };
            select.addEventListener('change', syncIncomeName);
            syncIncomeName();
        });
    });
</script>
@endpush

@section('content')
<div class="container-fluid">
    <div class="card bg-primary-subtle shadow-none mb-4">
        <div class="card-body px-4 py-3">
            <h4 class="fw-semibold mb-1">Penerimaan Mahasiswa Baru</h4>
            <p class="mb-0 text-muted">Daftar, kirim bukti pembayaran, dan pantau proses penerimaan Anda.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success" data-app-toast>{{ session('success') }}</div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger" data-app-toast>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @php
    $hasRegistration = $registrations->isNotEmpty();
    $requiredDocuments = $registrations->flatMap(fn($registration) => $registration->path?->documentRequirements?->where('is_required', true) ?? collect());
    $allDocumentsReady = $hasRegistration && $requiredDocuments->every(fn($requirement) => $registrations->flatMap->documents->contains(fn($document) => $document->requirement_id === $requirement->id && $document->status_verifikasi === 'valid'));
    $allPaymentsReady = $hasRegistration && $registrations->flatMap->payments->isNotEmpty() && $registrations->flatMap->payments->every(fn($payment) => $payment->status === 'lunas');
    $selectionStarted = $registrations->contains(fn($registration) => in_array($registration->status, ['review', 'sudah_ujian', 'lulus', 'diterima', 'ditolak', 'mundur'], true));
    $roadmap = [
    ['title' => 'Lengkapi data diri', 'done' => (bool) $profileComplete],
    ['title' => 'Pilih pendaftaran', 'done' => $hasRegistration],
    ['title' => 'Unggah berkas', 'done' => $allDocumentsReady],
    ['title' => 'Selesaikan pembayaran', 'done' => $allPaymentsReady],
    ['title' => 'Pantau seleksi', 'done' => $selectionStarted],
    ];
    $currentRoadmapStep = collect($roadmap)->search(fn($step) => !$step['done']);
    if ($currentRoadmapStep === false) $currentRoadmapStep = count($roadmap) - 1;
    @endphp

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div>
                    <h5 class="mb-1">Roadmap pendaftaran</h5>
                    <p class="text-muted mb-0">Ikuti tahapan ini sampai proses seleksi selesai.</p>
                </div>
                <span class="badge {{ $profileComplete ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }}">{{ $profileComplete ? 'Progres tersimpan' : 'Mulai dari data diri' }}</span>
            </div>
            <div class="row g-2">
                @foreach($roadmap as $index => $step)
                <div class="col-12 col-sm-6 col-xl">
                    <button type="button" data-roadmap-go="{{ $index }}" class="btn text-start d-flex align-items-center gap-2 h-100 w-100 rounded border p-3 {{ $step['done'] ? 'border-success bg-success-subtle' : ($currentRoadmapStep === $index ? 'border-primary bg-primary-subtle' : 'bg-light') }}">
                        <span class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0 {{ $step['done'] ? 'bg-success text-white' : ($currentRoadmapStep === $index ? 'bg-primary text-white' : 'bg-white text-muted border') }}" style="width:30px;height:30px">{{ $step['done'] ? '✓' : $index + 1 }}</span>
                        <span class="small fw-semibold text-dark">{{ $step['title'] }}</span>
                    </button>
                </div>
                @endforeach
            </div>
            @if(!$hasRegistration)
            <div class="alert alert-info py-2 mt-3 mb-0">
                @if(!$profileComplete)
                Lengkapi dan simpan biodata pada langkah pertama untuk melanjutkan.
                @elseif($admissionCards->isNotEmpty())
                Biodata tersimpan. Pilih kartu jalur pada langkah berikutnya untuk membuat pendaftaran.
                @else
                Biodata tersimpan. Kartu jalur akan muncul saat periode dan jadwal jalurnya dibuka serta biaya pendaftaran tersedia.
                @endif
            </div>
            @endif
        </div>
    </div>

    <div class="card mb-4" id="biodata" data-roadmap-panel="0" @if($currentRoadmapStep !==0) hidden @endif>
        <div class="card-header">
            <h5 class="card-title mb-0">1. Data diri calon mahasiswa</h5><small class="text-muted">Data akan tersimpan dan dapat dilengkapi kembali.</small>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('pmb.profile.update') }}">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-12">
                        <h6 class="text-primary mb-0">Identitas dan kelahiran</h6>
                    </div>
                    <div class="col-md-4"><label class="form-label">Nama lengkap sesuai identitas</label><input class="form-control" name="nama_mahasiswa" value="{{ old('nama_mahasiswa', $candidate->nama_mahasiswa ?? auth()->user()->name) }}" required maxlength="255"></div>
                    <div class="col-md-4"><label class="form-label">NIK</label><input class="form-control" name="nik" value="{{ old('nik', $candidate->nik ?? '') }}" inputmode="numeric" maxlength="16" placeholder="16 angka"></div>
                    <div class="col-md-4"><label class="form-label">NISN</label><input class="form-control" name="nisn" value="{{ old('nisn', $candidate->nisn ?? '') }}"></div>
                    <div class="col-md-3"><label class="form-label">NPWP</label><input class="form-control" name="npwp" value="{{ old('npwp', $candidate->npwp ?? '') }}"></div>
                    <div class="col-md-3"><label class="form-label">Jenis kelamin</label><select class="form-select" name="jenis_kelamin">
                            <option value="">Pilih</option>
                            <option value="L" @selected(old('jenis_kelamin', $candidate->jenis_kelamin ?? '') === 'L')>Laki-laki</option>
                            <option value="P" @selected(old('jenis_kelamin', $candidate->jenis_kelamin ?? '') === 'P')>Perempuan</option>
                        </select></div>
                    <div class="col-md-3"><label class="form-label">Tempat lahir</label><input class="form-control" name="tempat_lahir" value="{{ old('tempat_lahir', $candidate->tempat_lahir ?? '') }}"></div>
                    <div class="col-md-3"><label class="form-label">Tanggal lahir</label><input class="form-control" type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $candidate?->tanggal_lahir?->format('Y-m-d')) }}"></div>
                    <div class="col-md-3"><label class="form-label">Agama</label><select class="form-select" name="id_agama"><option value="">Pilih agama</option>@foreach($agamaOptions as $agama)<option value="{{ $agama->id_agama }}" @selected(old('id_agama', $candidate->id_agama ?? '') == $agama->id_agama)>{{ $agama->nama_agama }}</option>@endforeach</select></div>
                    <div class="col-md-3"><label class="form-label">Alat transportasi</label><select class="form-select" name="id_alat_transportasi"><option value="">Pilih transportasi</option>@foreach($transportOptions as $transport)<option value="{{ $transport->id_alat_transportasi }}" @selected(old('id_alat_transportasi', $candidate->id_alat_transportasi ?? '') == $transport->id_alat_transportasi)>{{ $transport->nama_alat_transportasi }}</option>@endforeach</select></div>

                    <div class="col-12 mt-4">
                        <h6 class="text-primary mb-0">Alamat dan kontak</h6>
                    </div>
                    <div class="col-md-6"><label class="form-label">Jalan</label><input class="form-control" name="jalan" value="{{ old('jalan', $candidate->jalan ?? '') }}"></div>
                    <div class="col-md-3"><label class="form-label">Dusun</label><input class="form-control" name="dusun" value="{{ old('dusun', $candidate->dusun ?? '') }}"></div>
                    <div class="col-md-1"><label class="form-label">RT</label><input class="form-control" name="rt" value="{{ old('rt', $candidate->rt ?? '') }}"></div>
                    <div class="col-md-1"><label class="form-label">RW</label><input class="form-control" name="rw" value="{{ old('rw', $candidate->rw ?? '') }}"></div>
                    <div class="col-md-1"><label class="form-label">Kode pos</label><input class="form-control" name="kode_pos" value="{{ old('kode_pos', $candidate->kode_pos ?? '') }}"></div>
                    <div class="col-md-4"><label class="form-label">Kelurahan / desa</label><input class="form-control" name="kelurahan" value="{{ old('kelurahan', $candidate->kelurahan ?? '') }}"></div>
                    <div class="col-md-4 pmb-select2-wrap"><label class="form-label">Kecamatan</label><select class="form-select" name="id_wilayah" data-wilayah-select data-ajax-url="{{ route('pmb.reference.wilayahs') }}"><option value="">Cari nama kecamatan...</option>@if($candidateRegion)<option value="{{ $candidateRegion->id_wilayah }}" selected>{{ $candidateRegion->nama_wilayah }}</option>@endif</select><small class="text-muted">Ketik minimal 2 karakter untuk mencari wilayah.</small></div>
                    <div class="col-md-4"><label class="form-label">Kode negara</label><input class="form-control" name="id_negara" value="{{ old('id_negara', $candidate->id_negara ?? 'ID') }}" maxlength="5"></div>
                    <div class="col-md-4"><label class="form-label">Nomor HP</label><input class="form-control" name="handphone" value="{{ old('handphone', $candidate->handphone ?? '') }}"></div>
                    <div class="col-md-4"><label class="form-label">Telepon</label><input class="form-control" name="telepon" value="{{ old('telepon', $candidate->telepon ?? '') }}"></div>
                    <div class="col-md-4"><label class="form-label">Email</label><input class="form-control" value="{{ auth()->user()->email }}" readonly><small class="text-muted">Mengikuti email akun.</small></div>

                    <div class="col-12 mt-4">
                        <h6 class="text-primary mb-0">Data KIP dan kesejahteraan</h6>
                    </div>
                    <div class="col-md-3"><label class="form-label">Nomor daftar KIP</label><input class="form-control" name="nomor_daftar_kip" value="{{ old('nomor_daftar_kip', $candidate->nomor_daftar_kip ?? '') }}"></div>
                    <div class="col-md-3"><label class="form-label">Nomor KIP</label><input class="form-control" name="nomor_kip" value="{{ old('nomor_kip', $candidate->nomor_kip ?? '') }}"></div>
                    <div class="col-md-3"><label class="form-label">Nomor KKS</label><input class="form-control" name="nomor_kks" value="{{ old('nomor_kks', $candidate->nomor_kks ?? '') }}"></div>
                    <div class="col-md-3"><label class="form-label">Nomor KPS / PKH</label><input class="form-control" name="nomor_kps" value="{{ old('nomor_kps', $candidate->nomor_kps ?? '') }}"></div>
                    <div class="col-md-3"><label class="form-label">Penerima KPS</label><select class="form-select" name="penerima_kps">
                            <option value="0">Tidak</option>
                            <option value="1" @selected(old('penerima_kps', $candidate->penerima_kps ?? false))>Ya</option>
                        </select></div>
                    <div class="col-md-3"><label class="form-label">Terdaftar DTKS</label><select class="form-select" name="terdaftar_dtks">
                            <option value="0">Tidak</option>
                            <option value="1" @selected(old('terdaftar_dtks', $candidate->terdaftar_dtks ?? false))>Ya</option>
                        </select></div>

                    @foreach([
                    'ayah' => ['title' => 'Data ayah', 'fields' => [['nik_ayah','NIK'],['nama_ayah','Nama'],['tanggal_lahir_ayah','Tanggal lahir','date'],['id_pendidikan_ayah','Kode pendidikan'],['nama_pendidikan_ayah','Pendidikan'],['id_pekerjaan_ayah','Kode pekerjaan'],['nama_pekerjaan_ayah','Pekerjaan'],['id_penghasilan_ayah','Kode penghasilan'],['nama_penghasilan_ayah','Penghasilan']]],
                    'ibu' => ['title' => 'Data ibu kandung', 'fields' => [['nik_ibu','NIK'],['nama_ibu_kandung','Nama'],['tanggal_lahir_ibu','Tanggal lahir','date'],['id_pendidikan_ibu','Kode pendidikan'],['nama_pendidikan_ibu','Pendidikan'],['id_pekerjaan_ibu','Kode pekerjaan'],['nama_pekerjaan_ibu','Pekerjaan'],['id_penghasilan_ibu','Kode penghasilan'],['nama_penghasilan_ibu','Penghasilan']]],
                    'wali' => ['title' => 'Data wali (jika ada)', 'fields' => [['nama_wali','Nama'],['tanggal_lahir_wali','Tanggal lahir','date'],['id_pendidikan_wali','Kode pendidikan'],['nama_pendidikan_wali','Pendidikan'],['id_pekerjaan_wali','Kode pekerjaan'],['nama_pekerjaan_wali','Pekerjaan'],['id_penghasilan_wali','Kode penghasilan'],['nama_penghasilan_wali','Penghasilan']]],
                    ] as $group)
                    <div class="col-12 mt-4">
                        <h6 class="text-primary mb-0">{{ $group['title'] }}</h6>
                    </div>
                    @foreach($group['fields'] as $field)
                    @if(str_starts_with($field[0], 'id_penghasilan_'))
                    @php($incomeNameField = str_replace('id_', 'nama_', $field[0]))
                    <div class="col-md-3"><label class="form-label">Penghasilan {{ strtolower($group['title'] === 'Data ayah' ? 'ayah' : ($group['title'] === 'Data ibu kandung' ? 'ibu' : 'wali')) }}</label><select class="form-select" name="{{ $field[0] }}" data-income-select="{{ $field[0] }}"><option value="">Pilih penghasilan</option>@foreach($incomeOptions as $income)<option value="{{ $income->id_penghasilan }}" data-name="{{ $income->nama_penghasilan }}" @selected(old($field[0], $candidate->{$field[0]} ?? '') == $income->id_penghasilan)>{{ $income->nama_penghasilan }}</option>@endforeach</select><input type="hidden" name="{{ $incomeNameField }}" value="{{ old($incomeNameField, $candidate->{$incomeNameField} ?? '') }}" data-income-name="{{ $field[0] }}"></div>
                    @elseif(str_starts_with($field[0], 'nama_penghasilan_'))
                    @else
                    <div class="col-md-3"><label class="form-label">{{ $field[1] }}</label><input class="form-control" name="{{ $field[0] }}" type="{{ $field[2] ?? 'text' }}" value="{{ old($field[0], $candidate->{$field[0]} ?? '') }}"></div>
                    @endif
                    @endforeach
                    @endforeach

                    <div class="col-12 mt-4">
                        <h6 class="text-primary mb-0">Data akademik dan verifikasi</h6>
                    </div>
                    <div class="col-md-3"><label class="form-label">NIM</label><input class="form-control" value="{{ $candidate->nim ?? 'Belum diterbitkan' }}" readonly></div>
                    <div class="col-md-5"><label class="form-label">ID mahasiswa DIKTI</label><input class="form-control" value="{{ $candidate->id_mahasiswa ?? 'Belum tersinkronisasi' }}" readonly></div>
                    <div class="col-md-2"><label class="form-label">Status verifikasi KTP</label><input class="form-control" value="{{ ($candidate?->is_ktp_verified) ? 'Terverifikasi' : 'Belum diverifikasi' }}" readonly></div>
                    <div class="col-md-2"><label class="form-label">Berkas KTP</label><input class="form-control" value="{{ $candidate?->file_ktp_path ? 'Sudah tercatat' : 'Belum diunggah' }}" readonly></div>
                    <div class="col-md-6"><label class="form-label">Terakhir sinkron feeder</label><input class="form-control" value="{{ $candidate?->last_update_feeder ?? 'Belum tersinkronisasi' }}" readonly></div>
                    <div class="col-md-6"><label class="form-label">Dibuat di feeder</label><input class="form-control" value="{{ $candidate?->tgl_create_feeder ?? 'Belum tersinkronisasi' }}" readonly></div>
                    <div class="col-12">
                        <div class="alert alert-light border mb-0 small">Data akademik, berkas KTP, dan status sinkronisasi dikelola sistem/admin agar tidak berubah tanpa proses verifikasi.</div>
                    </div>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-3 mt-3"><button class="btn btn-primary" type="submit"><i class="ti ti-device-floppy me-1"></i>Simpan data diri</button><span class="small text-muted">Email akun: {{ auth()->user()->email }}</span></div>
            </form>
        </div>
    </div>

    <div class="row" id="pendaftaran">
        <div class="col-12" data-roadmap-panel="1" @if($currentRoadmapStep !==1) hidden @endif>
            <div class="d-flex flex-wrap align-items-end justify-content-between gap-2 mb-3">
                <div>
                    <h5 class="mb-1">2. Pilih jalur pendaftaran</h5>
                    <p class="text-muted mb-0">Pilih kartu jalur yang tersedia, lalu tentukan periode dan program studi.</p>
                </div>
            </div>
            @if($admissionCards->isNotEmpty())
            <div class="row g-3 mb-4">
                @foreach($admissionCards as $card)
                @php($selectedRegistration = $registrations->first(fn($registration) => (string) $registration->path_id === (string) $card['path']->id))
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 border shadow-sm {{ (string) $card['path']->id === $selectedPathId ? 'border-danger border-2' : '' }}">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                <h5 class="card-title mb-0">{{ $card['path']->nama_jalur }}</h5><span class="badge {{ (string) $card['path']->id === $selectedPathId ? 'bg-danger' : 'bg-primary-subtle text-primary' }}">{{ (string) $card['path']->id === $selectedPathId ? 'Jalur dipilih' : $card['options']->count().' pilihan' }}</span>
                            </div>
                            @if($card['path']->deskripsi)<p class="text-muted small">{{ $card['path']->deskripsi }}</p>@endif
                            <form method="POST" action="{{ route('pmb.registrations.store') }}" class="mt-auto" data-admission-form>
                                @csrf
                                <input type="hidden" name="referral_code" value="{{ old('referral_code', session('pmb_referral_code')) }}">
                                <input type="hidden" name="path_id" value="{{ $card['path']->id }}">
                                <input type="hidden" name="period_id">
                                <input type="hidden" name="prodi_id">
                                <label class="form-label">Periode pendaftaran</label>
                                <select class="form-select mb-2" data-admission-period required>
                                    <option value="">Pilih periode</option>
                                    @foreach($card['options']->unique('period_id') as $option)
                                    <option value="{{ $option['period_id'] }}" @selected(((string) $card['path']->id === $selectedPathId && (string) $option['period_id'] === $selectedPeriodId) || ($selectedPathId === '' && $selectedRegistration && (string) $selectedRegistration->period_id === (string) $option['period_id']))>{{ $option['period_name'] }} ({{ $option['schedule_start'] }}–{{ $option['schedule_end'] }})</option>
                                    @endforeach
                                </select>
                                <label class="form-label">Program studi</label>
                                <select class="form-select mb-2" data-admission-program required disabled>
                                    <option value="">Pilih periode terlebih dahulu</option>
                                    @foreach($card['options'] as $option)
                                    <option value="{{ $option['program_id'] }}" data-period="{{ $option['period_id'] }}" data-amount="{{ $option['amount'] }}" data-spp="{{ $option['amount_spp'] }}" @selected($selectedPathId === '' && $selectedRegistration && (string) $selectedRegistration->period_id === (string) $option['period_id'] && (string) $selectedRegistration->prodi_id_1 === (string) $option['program_id'])>{{ $option['program_name'] }}</option>
                                    @endforeach
                                </select>
                                <div class="alert alert-info py-2 small mb-3" data-admission-fee>Pilih program untuk melihat biaya pendaftaran dan SPP per semester.</div>
                                <button class="btn btn-primary w-100" type="submit" @disabled(!$profileComplete)>{{ $profileComplete ? 'Daftar melalui jalur ini' : 'Lengkapi biodata terlebih dahulu' }}</button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="alert alert-info mb-4">
                @if($periods->isEmpty())
                Belum ada periode yang sedang dibuka. Periode PMB perlu aktif dan tanggal hari ini harus berada di antara tanggal mulai dan selesai.
                @elseif($paths->isEmpty() || $programs->isEmpty())
                Jalur atau program studi belum disiapkan admin PMB.
                @else
                Belum ada kombinasi jalur dan program studi dengan biaya untuk periode aktif. Minta admin melengkapi pengaturan biaya PMB.
                @endif
            </div>
            @endif
        </div>

        <div class="col-12" id="registration-tracking" @if($currentRoadmapStep < 2) hidden @endif>
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tahapan pendaftaran saya</h5>
                </div>
                <div class="card-body">
                    @forelse($registrations as $registration)
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex flex-wrap justify-content-between gap-2 mb-2" data-roadmap-panel="4" @if($currentRoadmapStep !==4) hidden @endif>
                            <div>
                                <h6 class="mb-1">{{ $registration->nomor_pendaftaran }}</h6>
                                <span class="text-muted">
                                    {{ $registration->period?->nama_periode ?? 'Periode tidak tersedia' }} ·
                                    {{ $registration->path?->nama_jalur ?? 'Jalur tidak tersedia' }} ·
                                    {{ $registration->program1?->nama_prodi ?? 'Program studi tidak tersedia' }}
                                </span>
                                @if($registration->nominal_spp !== null)
                                <div class="small text-muted mt-1">SPP per semester: <strong class="text-dark">Rp {{ number_format((float) $registration->nominal_spp, 0, ',', '.') }}</strong></div>
                                @endif
                            </div>
                            <span class="badge bg-primary-subtle text-primary align-self-start">{{ str_replace('_', ' ', ucfirst($registration->status)) }}</span>
                        </div>

                        @foreach($registration->payments as $payment)
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 border-top pt-3 mt-3" data-roadmap-panel="3" @if($currentRoadmapStep !==3) hidden @endif>
                            <div class="w-100"><strong>{{ $registration->nomor_pendaftaran }}</strong></div>
                            <div>
                                <strong>Biaya pendaftaran</strong><br>
                                <span>Rp {{ number_format($payment->nominal_tagihan, 0, ',', '.') }}</span>
                                <span class="badge bg-secondary-subtle text-secondary ms-1">{{ str_replace('_', ' ', ucfirst($payment->status)) }}</span>
                            </div>
                            @if($payment->file_bukti_bayar)
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('pmb.payments.download', $payment->id) }}">Lihat bukti</a>
                            @endif
                            @if(in_array($payment->status, ['pending', 'menunggu_verifikasi', 'ditolak'], true) && (float) $payment->nominal_tagihan > 0)
                            <form method="POST" action="{{ route('pmb.payments.proof', [$registration->id, $payment->id]) }}" enctype="multipart/form-data" class="d-flex flex-wrap gap-2">
                                @csrf
                                <input class="form-control form-control-sm" type="file" name="bukti_bayar" accept=".pdf,.jpg,.jpeg,.png" required>
                                <button class="btn btn-primary btn-sm" type="submit">{{ $payment->file_bukti_bayar ? 'Ganti bukti' : 'Kirim bukti' }}</button>
                                <small class="w-100 text-muted">PDF, JPG, JPEG, PNG; maksimal 5 MB.</small>
                            </form>
                            @endif
                        </div>
                        @endforeach

                        @if($registration->path?->documentRequirements?->isNotEmpty())
                        <div class="border-top pt-3 mt-3" data-roadmap-panel="2" @if($currentRoadmapStep !==2) hidden @endif>
                            <strong class="d-block mb-2">Berkas persyaratan</strong>
                            @foreach($registration->path->documentRequirements as $requirement)
                            @php($uploaded = $registration->documents->firstWhere('requirement_id', $requirement->id))
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 border rounded p-2 mb-2">
                                <div><span class="fw-semibold">{{ $requirement->nama_dokumen }}</span> @if($requirement->is_required)<span class="badge bg-light text-dark">Wajib</span>@endif<br><small class="text-muted">Format: {{ strtoupper(str_replace(',', ', ', $requirement->format_file)) }}</small>
                                    @if($uploaded)<br><small>Status: {{ str_replace('_', ' ', ucfirst($uploaded->status_verifikasi)) }}</small>@if($uploaded->catatan_admin) <small class="text-danger">· {{ $uploaded->catatan_admin }}</small>@endif @endif
                                </div>
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    @if($uploaded)<a class="btn btn-outline-secondary btn-sm" href="{{ route('pmb.documents.download', $uploaded->id) }}">Lihat berkas</a>@endif
                                    @if(!$uploaded || $uploaded->status_verifikasi === 'invalid')
                                    <form method="POST" action="{{ route('pmb.documents.upload', [$registration->id, $requirement->id]) }}" enctype="multipart/form-data" class="d-flex gap-2">@csrf<input class="form-control form-control-sm" type="file" name="file" accept="{{ collect(explode(',', $requirement->format_file))->map(fn($ext) => '.'.trim($ext))->implode(',') }}" required><button class="btn btn-primary btn-sm" type="submit">{{ $uploaded ? 'Unggah ulang' : 'Unggah' }}</button></form>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                        @if($registration->path?->documentRequirements?->isEmpty())
                        <div class="alert alert-info mb-0" data-roadmap-panel="2" @if($currentRoadmapStep !==2) hidden @endif>Tidak ada berkas persyaratan yang perlu diunggah untuk jalur ini.</div>
                        @endif
                    </div>
                    @empty
                    @if($admissionCards->isNotEmpty())
                    <p class="text-muted mb-0">Belum ada nomor pendaftaran pada akun ini. Pilih salah satu kartu jalur di atas untuk memulai.</p>
                    @else
                    <p class="text-muted mb-0">Belum ada nomor pendaftaran pada akun ini. Kartu jalur akan muncul setelah periode dan biaya pendaftaran tersedia.</p>
                    @endif
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (() => {
        const panels = document.querySelectorAll('[data-roadmap-panel]');
        const tracking = document.getElementById('registration-tracking');
        const roadmapButtons = document.querySelectorAll('[data-roadmap-go]');
        let activeStep = {
            {
                (int) $currentRoadmapStep
            }
        };

        const showStep = (step) => {
            activeStep = Number(step);
            panels.forEach((panel) => {
                const isActive = Number(panel.dataset.roadmapPanel) === activeStep;
                panel.hidden = !isActive;
                panel.style.display = isActive ? '' : 'none';
            });
            if (tracking) {
                tracking.hidden = activeStep < 2;
                tracking.style.display = activeStep < 2 ? 'none' : '';
            }
            roadmapButtons.forEach((button) => {
                if (Number(button.dataset.roadmapGo) === activeStep) button.setAttribute('aria-current', 'step');
                else button.removeAttribute('aria-current');
            });
        };
        roadmapButtons.forEach((button) => button.addEventListener('click', () => showStep(button.dataset.roadmapGo)));
        showStep(activeStep);

        document.querySelectorAll('[data-admission-form]').forEach((form) => {
            const periodChoice = form.querySelector('[data-admission-period]');
            const programChoice = form.querySelector('[data-admission-program]');
            const period = form.querySelector('[name="period_id"]');
            const program = form.querySelector('[name="prodi_id"]');
            const feeLabel = form.querySelector('[data-admission-fee]');
            const updatePrograms = (preserveSelection = true) => {
                const selectedPeriod = periodChoice.value;
                const previouslySelected = preserveSelection ? programChoice.value : '';
                let visibleOptions = 0;
                [...programChoice.options].forEach((option, index) => {
                    if (index === 0) return;
                    const matches = option.dataset.period === selectedPeriod;
                    option.hidden = !matches;
                    option.disabled = !matches;
                    if (matches) visibleOptions++;
                });
                programChoice.disabled = !selectedPeriod || visibleOptions === 0;
                programChoice.options[0].textContent = selectedPeriod ? 'Pilih program studi' : 'Pilih periode terlebih dahulu';
                if (!previouslySelected || ![...programChoice.options].some((option) => option.value === previouslySelected && option.dataset.period === selectedPeriod)) {
                    programChoice.value = '';
                } else {
                    programChoice.value = previouslySelected;
                }
                period.value = selectedPeriod;
                program.value = programChoice.value;
                updateFee();
            };
            const updateFee = () => {
                const option = programChoice.selectedOptions[0];
                program.value = option?.value ?? '';
                feeLabel.textContent = option?.dataset.amount ?
                    `Biaya pendaftaran: Rp ${new Intl.NumberFormat('id-ID').format(Number(option.dataset.amount))} · SPP per semester: Rp ${new Intl.NumberFormat('id-ID').format(Number(option.dataset.spp || 0))}` :
                    'Pilih program studi untuk melihat biaya pendaftaran dan SPP per semester.';
            };
            periodChoice.addEventListener('change', () => updatePrograms(false));
            programChoice.addEventListener('change', updateFee);
            updatePrograms(true);
        });
    })();
</script>
@endsection
