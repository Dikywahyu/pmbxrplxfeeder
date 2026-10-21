@extends('layouts.auth')

@section('title', 'Klaim Akun PMB')

@section('content')
@php
    $emailParts = isset($candidate) ? explode('@', $candidate->email ?? '') : [];
    $emailHint = count($emailParts) === 2
        ? mb_substr($emailParts[0], 0, 1).'•••@'.$emailParts[1]
        : '';
@endphp
<div class="forny-form">
    <div class="text-center mb-4">
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary mb-3" style="width:64px;height:64px;font-size:1.8rem"><i class="ti ti-user-check"></i></div>
        <h4 class="mb-2">Klaim akun PMB</h4>
        @if(isset($candidate))
            <p class="text-muted mb-0">Pendaftaran untuk <strong>{{ $candidate->nama_mahasiswa }}</strong></p>
            <small class="text-muted">{{ $emailHint }}</small>
        @else
            <p class="text-muted mb-0">Hubungkan akun Anda ke pendaftaran mahasiswa.</p>
        @endif
    </div>

    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    @if($state === 'expired')
        <div class="alert alert-warning">Link klaim ini sudah kedaluwarsa. Hubungi marketing yang mendaftarkan Anda untuk meminta link baru.</div>
    @elseif($state === 'claimed')
        <div class="alert alert-info">Pendaftaran ini sudah diklaim. Jika ini pendaftaran Anda, masuklah menggunakan akun mahasiswa Anda.</div>
        <a class="btn btn-primary w-100" href="{{ route('login') }}">Masuk ke akun</a>
    @elseif($state === 'email_mismatch')
        <div class="alert alert-warning">Anda sedang masuk menggunakan email yang berbeda. Keluar, lalu buka kembali link ini dengan akun yang sesuai.</div>
        <form method="POST" action="{{ route('auth.logout') }}">@csrf<button class="btn btn-outline-primary w-100" type="submit">Keluar dari akun</button></form>
    @elseif($state === 'ready')
        <p class="text-muted text-center">Akun Anda cocok dengan email pendaftaran. Konfirmasi untuk menghubungkan akun.</p>
        <form method="POST" action="{{ route('pmb.claim.store', ['token' => request()->route('token')]) }}">
            @csrf
            <button class="btn btn-primary w-100 py-2" type="submit">Klaim pendaftaran</button>
        </form>
    @else
        <p class="text-muted text-center">Buat kata sandi untuk mengaktifkan akun, lalu lengkapi biodata dan berkas Anda.</p>
        <form method="POST" action="{{ route('pmb.claim.store', ['token' => request()->route('token')]) }}">
            @csrf
            <div class="mb-3"><label class="form-label" for="claim-password">Kata sandi baru</label><input class="form-control" id="claim-password" type="password" name="password" minlength="8" autocomplete="new-password" required></div>
            <div class="mb-4"><label class="form-label" for="claim-password-confirmation">Ulangi kata sandi</label><input class="form-control" id="claim-password-confirmation" type="password" name="password_confirmation" minlength="8" autocomplete="new-password" required></div>
            <button class="btn btn-primary w-100 py-2" type="submit">Buat akun dan klaim</button>
        </form>
    @endif

    <div class="text-center mt-4"><a class="small text-muted" href="{{ route('login') }}">Kembali ke halaman masuk</a></div>
</div>
@endsection
