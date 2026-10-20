@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card bg-primary-subtle shadow-none mb-4">
        <div class="card-body px-4 py-3">
            <h4 class="fw-semibold mb-1">Penerimaan Mahasiswa Baru</h4>
            <p class="mb-0 text-muted">Daftar, kirim bukti pembayaran, dan pantau proses penerimaan Anda.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        @if($periods->isNotEmpty() && $paths->isNotEmpty() && $programs->isNotEmpty())
            <div class="col-xl-5 col-12">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Formulir Pendaftaran</h5></div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('pmb.registrations.store') }}">
                            @csrf
                            <input type="hidden" name="referral_code" value="{{ old('referral_code', session('pmb_referral_code')) }}">
                            <div class="mb-3">
                                <label class="form-label" for="nama_mahasiswa">Nama lengkap</label>
                                <input class="form-control" id="nama_mahasiswa" name="nama_mahasiswa" value="{{ old('nama_mahasiswa', $candidate->nama_mahasiswa ?? auth()->user()->name) }}" maxlength="255" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="nik">NIK (16 angka)</label>
                                <input class="form-control" id="nik" name="nik" value="{{ old('nik', $candidate->nik ?? '') }}" inputmode="numeric" pattern="[0-9]{16}" maxlength="16">
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="jenis_kelamin">Jenis kelamin</label>
                                    <select class="form-select" id="jenis_kelamin" name="jenis_kelamin">
                                        <option value="">Pilih</option>
                                        <option value="L" @selected(old('jenis_kelamin', $candidate->jenis_kelamin ?? '') === 'L')>Laki-laki</option>
                                        <option value="P" @selected(old('jenis_kelamin', $candidate->jenis_kelamin ?? '') === 'P')>Perempuan</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="handphone">Nomor HP</label>
                                    <input class="form-control" id="handphone" name="handphone" value="{{ old('handphone', $candidate->handphone ?? '') }}" maxlength="20">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="period_id">Periode</label>
                                <select class="form-select" id="period_id" name="period_id" required>
                                    <option value="">Pilih periode</option>
                                    @foreach($periods as $period)
                                        <option value="{{ $period->id }}" @selected(old('period_id') == $period->id)>{{ $period->nama_periode }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="path_id">Jalur pendaftaran</label>
                                <select class="form-select" id="path_id" name="path_id" required>
                                    <option value="">Pilih jalur</option>
                                    @foreach($paths as $path)
                                        <option value="{{ $path->id }}" @selected(old('path_id') == $path->id)>{{ $path->nama_jalur }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="prodi_id">Program studi pilihan</label>
                                <select class="form-select" id="prodi_id" name="prodi_id" required>
                                    <option value="">Pilih program studi</option>
                                    @foreach($programs as $program)
                                        <option value="{{ $program->id }}" @selected(old('prodi_id') == $program->id)>{{ $program->nama_prodi }} ({{ $program->jenjang }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="alert alert-info py-2" id="registration-fee">Pilih periode, jalur, dan program studi untuk melihat biaya pendaftaran.</div>
                            <button class="btn btn-primary" type="submit">Buat Pendaftaran</button>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <div class="col-12">
                <div class="alert alert-info">
                    Pendaftaran belum dibuka atau data periode, jalur, program studi, dan biaya belum disiapkan oleh admin PMB.
                </div>
            </div>
        @endif

        <div class="col-xl-7 col-12">
            <div class="card">
                <div class="card-header"><h5 class="card-title mb-0">Pendaftaran Saya</h5></div>
                <div class="card-body">
                    @forelse($registrations as $registration)
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
                                <div>
                                    <h6 class="mb-1">{{ $registration->nomor_pendaftaran }}</h6>
                                    <span class="text-muted">
                                        {{ $registration->period?->nama_periode ?? 'Periode tidak tersedia' }} ·
                                        {{ $registration->path?->nama_jalur ?? 'Jalur tidak tersedia' }} ·
                                        {{ $registration->program1?->nama_prodi ?? 'Program studi tidak tersedia' }}
                                    </span>
                                </div>
                                <span class="badge bg-primary-subtle text-primary align-self-start">{{ str_replace('_', ' ', ucfirst($registration->status)) }}</span>
                            </div>

                            @foreach($registration->payments as $payment)
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 border-top pt-3 mt-3">
                                    <div>
                                        <strong>Biaya pendaftaran</strong><br>
                                        <span>Rp {{ number_format($payment->nominal_tagihan, 0, ',', '.') }}</span>
                                        <span class="badge bg-secondary-subtle text-secondary ms-1">{{ str_replace('_', ' ', ucfirst($payment->status)) }}</span>
                                    </div>
                                    @if($payment->file_bukti_bayar)
                                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('pmb.payments.download', $payment->id) }}">Lihat bukti</a>
                                    @endif
                                    @if(in_array($payment->status, ['pending', 'ditolak'], true) && (float) $payment->nominal_tagihan > 0)
                                        <form method="POST" action="{{ route('pmb.payments.proof', [$registration->id, $payment->id]) }}" enctype="multipart/form-data" class="d-flex flex-wrap gap-2">
                                            @csrf
                                            <input class="form-control form-control-sm" type="file" name="bukti_bayar" accept=".pdf,.jpg,.jpeg,.png" required>
                                            <button class="btn btn-primary btn-sm" type="submit">Kirim bukti</button>
                                            <small class="w-100 text-muted">PDF, JPG, JPEG, PNG; maksimal 5 MB.</small>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @empty
                        <p class="text-muted mb-0">Belum ada pendaftaran. Isi formulir untuk memulai.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (() => {
        const fees = @json($feeOptions);
        const fields = ['period_id', 'path_id', 'prodi_id'].map((id) => document.getElementById(id));
        const label = document.getElementById('registration-fee');
        if (!label || fields.some((field) => !field)) return;

        const updateFee = () => {
            const fee = fees.find((item) =>
                String(item.period_id) === fields[0].value
                && String(item.path_id) === fields[1].value
                && String(item.program_id) === fields[2].value
            );
            label.textContent = fee
                ? `Biaya pendaftaran: Rp ${new Intl.NumberFormat('id-ID').format(fee.amount)}`
                : 'Biaya untuk pilihan ini belum tersedia.';
        };
        fields.forEach((field) => field.addEventListener('change', updateFee));
        updateFee();
    })();
</script>
@endsection
