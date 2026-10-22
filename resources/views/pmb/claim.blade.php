@extends('layouts.auth')

@section('title', 'Klaim Akun PMB')

@section('content')
<div class="forny-form">
    <div class="text-center mb-4">
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary mb-3" style="width:64px;height:64px;font-size:1.8rem"><i class="ti ti-user-check"></i></div>
        <h4 class="mb-2">Klaim akun PMB</h4>
        @if(isset($candidate))
            <p class="text-muted mb-0">Pendaftaran untuk <strong>{{ $candidate->nama_mahasiswa }}</strong></p>
        @else
            <p class="text-muted mb-0">Hubungkan akun Anda ke pendaftaran mahasiswa.</p>
        @endif
    </div>

    @if($errors->any())
        <div class="alert alert-danger" data-app-toast><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    @if($state === 'expired')
        <div class="alert alert-warning">Link klaim ini sudah kedaluwarsa. Hubungi marketing yang mendaftarkan Anda untuk meminta link baru.</div>
    @elseif($state === 'nik_missing')
        <div class="alert alert-warning">NIK belum tercatat pada pendaftaran ini. Hubungi marketing agar data NIK diperbarui sebelum klaim.</div>
    @elseif($state === 'claimed')
        <div class="alert alert-info">Pendaftaran ini sudah diklaim. Jika ini pendaftaran Anda, masuklah menggunakan akun mahasiswa Anda.</div>
        <a class="btn btn-primary w-100" href="{{ route('login') }}">Masuk ke akun</a>
    @elseif($state === 'ready')
        <p class="text-muted text-center">Masukkan NIK pendaftaran untuk menghubungkan pendaftaran dengan akun <strong>{{ auth()->user()->email }}</strong>.</p>
        <form method="POST" action="{{ route('pmb.claim.store', ['token' => request()->route('token')]) }}">
            @csrf
            <div class="mb-4"><label class="form-label" for="claim-nik">NIK mahasiswa</label><input class="form-control" id="claim-nik" type="text" name="nik" inputmode="numeric" maxlength="16" minlength="16" autocomplete="off" required></div>
            <button class="btn btn-primary w-100 py-2" type="submit">Klaim pendaftaran</button>
        </form>
    @else
        <p class="text-muted text-center">Gunakan NIK yang didaftarkan marketing. Masukkan email yang akan Anda gunakan untuk akun PMB, lalu buat kata sandi.</p>
        <form method="POST" action="{{ route('pmb.claim.store', ['token' => request()->route('token')]) }}">
            @csrf
            <div class="mb-3"><label class="form-label" for="claim-nik">NIK mahasiswa</label><input class="form-control" id="claim-nik" type="text" name="nik" inputmode="numeric" maxlength="16" minlength="16" autocomplete="off" required></div>
            <div class="mb-3"><label class="form-label" for="claim-email">Email akun yang akan digunakan</label><input class="form-control" id="claim-email" type="email" name="email" maxlength="255" autocomplete="email" required></div>
            <div class="mb-3"><label class="form-label" for="claim-password">Kata sandi baru</label><input class="form-control" id="claim-password" type="password" name="password" minlength="8" autocomplete="new-password" required></div>
            <div class="mb-4"><label class="form-label" for="claim-password-confirmation">Ulangi kata sandi</label><input class="form-control" id="claim-password-confirmation" type="password" name="password_confirmation" minlength="8" autocomplete="new-password" required></div>
            <button class="btn btn-primary w-100 py-2" type="submit">Buat akun dan klaim</button>
        </form>
    @endif

    <div class="text-center mt-4"><a class="small text-muted" href="{{ route('login') }}">Kembali ke halaman masuk</a></div>
</div>
@endsection
