<div class="row g-3">
    <div class="col-12">
        <div class="alert alert-primary border-0 d-flex gap-2 align-items-start mb-0">
            <i class="ti ti-info-circle fs-5"></i>
            <div>Masukkan data dasar calon mahasiswa dan pilihan pendaftarannya. Mahasiswa menerima link pribadi untuk membuat atau menghubungkan akun, lalu melengkapi biodata dan berkasnya sendiri.</div>
        </div>
    </div>

    @unless($referral)
        <div class="col-12"><div class="alert alert-warning mb-0">Akun marketing Anda belum memiliki kode referral aktif. Hubungi Admin PMB agar kode referral diaktifkan.</div></div>
    @else
        <div class="col-12">
            <div class="card border mb-0">
                <div class="card-header bg-transparent"><h5 class="card-title mb-0"><i class="ti ti-user-plus me-2 text-primary"></i>Input calon mahasiswa</h5></div>
                <div class="card-body">
                    @if($fees->isEmpty())
                        <div class="alert alert-warning mb-0">Belum ada kombinasi periode, jalur, program studi, dan biaya yang sedang dibuka.</div>
                    @else
                        <form action="{{ route('admin.pmb.marketing.registrations.store') }}" method="POST" class="row g-3" data-marketing-registration-form>
                            @csrf
                            <div class="col-md-6"><label class="form-label" for="marketing-name">Nama lengkap</label><input class="form-control" id="marketing-name" name="nama_mahasiswa" maxlength="255" required></div>
                            <div class="col-md-6"><label class="form-label" for="marketing-email">Email mahasiswa</label><input class="form-control" id="marketing-email" type="email" name="email" maxlength="255" required><small class="text-muted">Pastikan email benar. Email ini dipakai untuk mengklaim akun.</small></div>
                            <div class="col-md-4"><label class="form-label" for="marketing-phone">Nomor HP</label><input class="form-control" id="marketing-phone" name="handphone" maxlength="20" required></div>
                            <div class="col-md-4"><label class="form-label" for="marketing-nik">NIK <span class="text-muted fw-normal">(opsional)</span></label><input class="form-control" id="marketing-nik" name="nik" inputmode="numeric" maxlength="16"></div>
                            <div class="col-md-4"><label class="form-label" for="marketing-gender">Jenis kelamin <span class="text-muted fw-normal">(opsional)</span></label><select class="form-select" id="marketing-gender" name="jenis_kelamin"><option value="">Pilih</option><option value="L">Laki-laki</option><option value="P">Perempuan</option></select></div>
                            <div class="col-12"><label class="form-label" for="marketing-fee">Periode, jalur, dan program studi</label><select class="form-select" id="marketing-fee" name="fee_id" required><option value="">Pilih pendaftaran</option>@foreach($fees as $fee)<option value="{{ $fee->id }}">{{ $fee->period?->nama_periode }} · {{ $fee->path?->nama_jalur }} · {{ $fee->program?->nama_prodi }} ({{ $fee->program?->jenjang }}) — Rp {{ number_format($fee->nominal_pendaftaran, 0, ',', '.') }}</option>@endforeach</select></div>
                            <div class="col-12 d-flex justify-content-end"><button class="btn btn-primary px-4" type="submit"><i class="ti ti-link me-1"></i>Buat pendaftaran dan link klaim</button></div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @endunless

    <div class="col-12">
        <div class="card border mb-0">
            <div class="card-header bg-transparent"><h5 class="card-title mb-0">Pendaftar dari referral Anda <span class="badge bg-primary-subtle text-primary ms-1">{{ $registrations->count() }}</span></h5></div>
            <div class="card-body"><div class="table-responsive">
                <table class="table table-hover align-middle pmb-data-table w-100">
                    <thead class="table-light"><tr><th>Mahasiswa</th><th>Periode dan pilihan</th><th>Nomor pendaftaran</th><th>Akun klaim</th><th data-orderable="false" data-searchable="false">Aksi</th></tr></thead>
                    <tbody>
                        @foreach($registrations as $registration)
                            @php($claim = $registration->candidate?->claim)
                            <tr>
                                <td><strong>{{ $registration->candidate?->nama_mahasiswa }}</strong><br><small class="text-muted">{{ $registration->candidate?->email }} · {{ $registration->candidate?->handphone }}</small></td>
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
