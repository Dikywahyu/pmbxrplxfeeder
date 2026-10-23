@extends('layouts.auth')

@section('title', 'Klaim Akun Marketing PMB')

@section('content')
<div class="forny-form">
    <div class="text-center mb-4">
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary mb-3" style="width:64px;height:64px;font-size:1.8rem"><i class="ti ti-speakerphone"></i></div>
        <h4 class="mb-2">Klaim akun marketing PMB</h4>
        <p class="text-muted mb-0">Undangan untuk <strong>{{ $referral->marketing_name }}</strong></p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger" data-app-toast><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    @if($state === 'expired')
        <div class="alert alert-warning">Link klaim sudah kedaluwarsa. Silakan hubungi admin PMB untuk meminta link baru.</div>
    @elseif($state === 'claimed')
        <div class="alert alert-info">Undangan ini sudah diklaim dan tidak dapat digunakan lagi.</div>
        <a class="btn btn-primary w-100" href="{{ route('login') }}">Masuk ke akun</a>
    @else
        <div class="alert alert-primary">Buat akun untuk mengakses dashboard marketing. Kode referral dan harga khusus jalur/periode sudah disiapkan oleh admin.</div>
        <form method="POST" action="{{ route('pmb.marketing-claim.store', ['token' => request()->route('token')]) }}">
            @csrf
            <div class="mb-3"><label class="form-label" for="marketing-claim-email">Email akun</label><input class="form-control" id="marketing-claim-email" type="email" name="email" value="{{ old('email', $referral->contact_email) }}" maxlength="255" autocomplete="email" required></div>
            <div class="mb-3"><label class="form-label" for="marketing-claim-password">Kata sandi</label><input class="form-control" id="marketing-claim-password" type="password" name="password" minlength="8" autocomplete="new-password" required></div>
            <div class="mb-4"><label class="form-label" for="marketing-claim-confirmation">Ulangi kata sandi</label><input class="form-control" id="marketing-claim-confirmation" type="password" name="password_confirmation" minlength="8" autocomplete="new-password" required></div>
            <button class="btn btn-primary w-100 py-2" type="submit">Buat dan klaim akun</button>
        </form>
    @endif

    <div class="text-center mt-4"><a class="small text-muted" href="{{ route('login') }}">Kembali ke halaman masuk</a></div>
</div>
@endsection
