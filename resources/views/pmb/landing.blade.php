@extends('layouts.pmb-public')

@section('title', 'Penerimaan Mahasiswa Baru | Universitas Cipta Wacana')

@push('styles')
<style>
    :root {
        --cwcu-red: #d71920;
        --cwcu-deep: #8d1118;
        --cwcu-ink: #203d48;
        --cwcu-muted: #6d7b80;
        --cwcu-soft: #fff7f6;
    }

    body {
        color: var(--cwcu-ink);
        background: #fff;
    }

    .pmb-utility {
        min-height: 34px;
        background: #f7f7f7;
        color: #58666b;
        font-size: .72rem;
    }

    .pmb-utility a {
        color: inherit;
        text-decoration: none;
    }

    .pmb-public-nav {
        position: sticky;
        z-index: 20;
        top: 0;
        border-bottom: 1px solid #eee;
        background: rgba(255, 255, 255, .97);
        backdrop-filter: blur(12px);
    }

    .pmb-public-nav .container {
        min-height: 76px;
    }

    .pmb-public-logo {
        width: 54px;
        height: 54px;
        object-fit: contain;
    }

    .pmb-public-brand {
        color: #263d43;
        font-size: .98rem;
        font-weight: 800;
        line-height: 1.12;
    }

    .pmb-public-brand small {
        display: block;
        margin-top: .25rem;
        color: #77858a;
        font-size: .65rem;
        font-weight: 600;
        letter-spacing: .08em;
    }

    .pmb-nav-links a:not(.btn) {
        color: #4d5c60;
        font-size: .85rem;
        font-weight: 600;
        text-decoration: none;
    }

    .btn-cwcu {
        border-color: var(--cwcu-red);
        background: var(--cwcu-red);
        color: #fff;
    }

    .btn-cwcu:hover {
        border-color: var(--cwcu-deep);
        background: var(--cwcu-deep);
        color: #fff;
    }

    .pmb-hero {
        position: relative;
        overflow: hidden;
        min-height: 465px;
        display: flex;
        align-items: center;
        color: #fff;
        background: linear-gradient(105deg, rgba(125, 8, 15, .96), rgba(211, 16, 25, .9) 52%, rgba(239, 63, 48, .75)), radial-gradient(ellipse at 80% 20%, #ffb052, transparent 32%), linear-gradient(135deg, #a90f17, #ed382b);
    }

    .pmb-hero::before {
        position: absolute;
        inset: 0;
        content: '';
        opacity: .2;
        background: repeating-linear-gradient(145deg, transparent 0 42px, rgba(255, 255, 255, .17) 43px 45px, transparent 46px 86px);
        mask-image: linear-gradient(90deg, transparent, #000);
    }

    .pmb-hero::after {
        position: absolute;
        width: 580px;
        height: 580px;
        right: -110px;
        top: -260px;
        border: 1px solid rgba(255, 255, 255, .25);
        border-radius: 50%;
        box-shadow: 0 0 0 55px rgba(255, 255, 255, .04), 0 0 0 110px rgba(255, 255, 255, .04);
        content: '';
    }

    .pmb-hero-content {
        position: relative;
        z-index: 1;
        padding-top: 4.6rem;
        padding-bottom: 4.6rem;
    }

    .pmb-hero-kicker {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        color: #ffe6bb;
        font-size: .76rem;
        font-weight: 800;
        letter-spacing: .14em;
        text-transform: uppercase;
    }

    .pmb-hero h1 {
        max-width: 760px;
        margin: 1rem 0;
        color: #fff;
        font-size: clamp(2.45rem, 5.2vw, 4.5rem);
        font-weight: 850;
        letter-spacing: -.045em;
        line-height: 1.03;
    }

    .pmb-hero p {
        max-width: 610px;
        color: rgba(255, 255, 255, .88);
        font-size: clamp(1rem, 1.7vw, 1.18rem);
        line-height: 1.75;
    }

    .pmb-hero-actions .btn-light {
        color: var(--cwcu-deep);
    }

    .pmb-hero-mark {
        position: absolute;
        z-index: 0;
        right: 9%;
        bottom: 0;
        display: flex;
        width: min(35vw, 330px);
        height: 85%;
        align-items: flex-end;
        justify-content: center;
        opacity: .19;
    }

    .pmb-hero-mark img {
        width: 100%;
        max-height: 100%;
        object-fit: contain;
        object-position: bottom;
        filter: grayscale(1) brightness(0) invert(1);
    }

    .pmb-section {
        padding: 4.4rem 0;
    }

    .pmb-welcome {
        padding-top: 4.8rem;
    }

    .pmb-welcome-art {
        position: relative;
        min-height: 300px;
        display: grid;
        place-items: center;
        overflow: hidden;
        border-radius: 1.4rem;
        background: linear-gradient(145deg, #fff1ed, #ffe3dc);
    }

    .pmb-welcome-art::before,
    .pmb-welcome-art::after {
        position: absolute;
        width: 230px;
        height: 230px;
        border: 1px solid rgba(195, 32, 35, .15);
        border-radius: 50%;
        content: '';
    }

    .pmb-welcome-art::before {
        left: -80px;
        top: -100px;
        box-shadow: 0 0 0 30px rgba(195, 32, 35, .035), 0 0 0 60px rgba(195, 32, 35, .035);
    }

    .pmb-welcome-art::after {
        right: -100px;
        bottom: -130px;
    }

    .pmb-welcome-art img {
        position: relative;
        z-index: 1;
        width: 158px;
        height: 158px;
        object-fit: contain;
        filter: drop-shadow(0 14px 18px rgba(99, 21, 23, .12));
    }

    .pmb-welcome-copy h2 {
        color: var(--cwcu-ink);
        font-size: clamp(1.8rem, 3vw, 2.6rem);
        font-weight: 820;
        letter-spacing: -.035em;
    }

    .pmb-welcome-copy p {
        color: var(--cwcu-muted);
        line-height: 1.8;
    }

    .pmb-section-heading {
        max-width: 720px;
        margin: 0 auto 2rem;
        text-align: center;
    }

    .pmb-section-heading .eyebrow {
        color: var(--cwcu-red);
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .16em;
    }

    .pmb-section-heading h2 {
        margin: .45rem 0 .6rem;
        color: var(--cwcu-ink);
        font-size: clamp(1.75rem, 3vw, 2.4rem);
        font-weight: 820;
        letter-spacing: -.03em;
    }

    .pmb-section-heading p {
        color: var(--cwcu-muted);
    }

    .pmb-program-section {
        background-color: #fff9f7;
        background-image: radial-gradient(rgba(190, 35, 38, .075) 1px, transparent 1px);
        background-size: 20px 20px;
    }

    .pmb-program-card {
        position: relative;
        height: 100%;
        min-height: 205px;
        overflow: hidden;
        padding: 1.4rem;
        border-radius: 1.1rem;
        background: linear-gradient(145deg, #e9272b, #b30e1b);
        color: #fff;
        box-shadow: 0 12px 24px rgba(138, 17, 24, .13);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .pmb-program-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 17px 30px rgba(138, 17, 24, .2);
    }

    .pmb-program-card::after {
        position: absolute;
        width: 150px;
        height: 150px;
        right: -70px;
        bottom: -80px;
        border: 1px solid rgba(255, 255, 255, .3);
        border-radius: 50%;
        box-shadow: 0 0 0 20px rgba(255, 255, 255, .06);
        content: '';
    }

    .pmb-program-card .icon {
        display: grid;
        width: 45px;
        height: 45px;
        margin-bottom: 2.1rem;
        place-items: center;
        border: 1px solid rgba(255, 255, 255, .3);
        border-radius: 13px;
        background: rgba(255, 255, 255, .13);
        font-size: 1.3rem;
    }

    .pmb-program-card h3 {
        position: relative;
        z-index: 1;
        margin: 0;
        color: #fff;
        font-size: 1.02rem;
        font-weight: 750;
    }

    .pmb-program-card p {
        position: relative;
        z-index: 1;
        margin: .4rem 0 0;
        color: rgba(255, 255, 255, .77);
        font-size: .78rem;
    }

    .pmb-flow-card,
    .pmb-help-card {
        height: 100%;
        border: 1px solid #f0e7e5;
        border-radius: 1.15rem;
        background: #fff;
        box-shadow: 0 12px 30px rgba(50, 42, 41, .055);
    }

    .pmb-flow-card {
        padding: clamp(1rem, 3vw, 2rem);
    }

    .pmb-schedule-panel {
        padding: 1.25rem;
        border: 1px solid #f0e7e5;
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 10px 25px rgba(50, 42, 41, .045);
    }

    .pmb-schedule-item {
        padding: 1rem 0;
        border-top: 1px solid #f1eceb;
    }

    .pmb-schedule-dates {
        color: #53666c;
        font-size: .8rem;
    }

    .pmb-schedule-empty {
        display: flex;
        min-height: 115px;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        border-top: 1px solid #f1eceb;
        color: var(--cwcu-muted);
        font-size: .88rem;
    }

    .pmb-schedule-empty i {
        color: #bd4b4e;
        font-size: 1.15rem;
    }

    .pmb-flow {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 1rem;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .pmb-flow-step {
        position: relative;
        min-width: 0;
        padding: 0rem .6rem;
        text-align: center;
    }

    .pmb-flow-step:not(:last-child)::after {
        position: absolute;
        z-index: 0;
        top: 30px;
        left: calc(50% + 31px);
        width: calc(100% - 46px);
        border-top: 2px dashed #e9b9b5;
        content: '';
    }

    .pmb-flow-number {
        position: relative;
        z-index: 1;
        display: grid;
        width: 60px;
        height: 60px;
        margin: 0 auto 1rem;
        place-items: center;
        border: 5px solid #fff1ef;
        border-radius: 50%;
        background: linear-gradient(135deg, #f0463b, var(--cwcu-deep));
        color: #fff;
        font-size: 1.3rem;
        box-shadow: 0 6px 16px rgba(194, 34, 38, .2);
    }

    .pmb-flow-step h3 {
        font-size: .94rem;
        font-weight: 750;
    }

    .pmb-flow-step p {
        margin: 0;
        color: var(--cwcu-muted);
        font-size: .8rem;
        line-height: 1.65;
    }

    .pmb-flow-caption {
        margin: 1.1rem 0 0;
        padding: .85rem 1rem;
        border-radius: .8rem;
        background: #fff4f1;
        color: #68585a;
        font-size: .84rem;
    }

    .pmb-help-card {
        padding: 1.35rem;
    }

    .pmb-help-card .icon {
        color: var(--cwcu-red);
        font-size: 1.5rem;
    }

    .pmb-help-card h3 {
        margin-top: .7rem;
        font-size: 1rem;
        font-weight: 750;
    }

    .pmb-help-card p {
        color: var(--cwcu-muted);
        font-size: .87rem;
    }

    .pmb-cta {
        position: relative;
        overflow: hidden;
        padding: clamp(1.5rem, 4vw, 2.7rem);
        border-radius: 1.3rem;
        background: linear-gradient(110deg, #94121a, #df2529);
        color: #fff;
    }

    .pmb-cta h2 {
        color: #fff;
        font-weight: 800;
    }

    .pmb-cta p {
        color: rgba(255, 255, 255, .8);
    }

    .pmb-public-footer {
        padding: 2rem 0;
        border-top: 1px solid #eee;
        color: var(--cwcu-muted);
        font-size: .82rem;
    }

    .pmb-footer-logo {
        width: 48px;
        height: 48px;
        object-fit: contain;
    }

    @media(max-width:767.98px) {
        .pmb-utility {
            display: none;
        }

        .pmb-public-nav .container {
            min-height: 66px;
        }

        .pmb-public-logo {
            width: 43px;
            height: 43px;
        }

        .pmb-public-brand {
            font-size: .83rem;
        }

        .pmb-public-brand small {
            font-size: .55rem;
        }

        .pmb-nav-links .nav-extra {
            display: none !important;
        }

        .pmb-hero {
            min-height: 420px;
        }

        .pmb-hero-content {
            padding-top: 3.8rem;
            padding-bottom: 3.8rem;
        }

        .pmb-hero-mark {
            right: -9%;
            width: 55vw;
            opacity: .12;
        }

        .pmb-section {
            padding: 3.3rem 0;
        }

        .pmb-welcome {
            padding-top: 3rem;
        }

        .pmb-welcome-art {
            min-height: 220px;
        }

        .pmb-welcome-art img {
            width: 120px;
            height: 120px;
        }

        .pmb-flow {
            display: block;
        }

        .pmb-flow-step {
            display: grid;
            grid-template-columns: 50px minmax(0, 1fr);
            gap: .1rem .8rem;
            padding: .55rem 0 1.1rem;
            text-align: left;
        }

        .pmb-flow-number {
            grid-row: span 2;
            width: 46px;
            height: 46px;
            margin: 0;
            border-width: 4px;
            font-size: 1.05rem;
        }

        .pmb-flow-step:not(:last-child)::after {
            top: 49px;
            left: 22px;
            width: 0;
            height: calc(100% - 55px);
            border-top: 0;
            border-left: 2px dashed #e9b9b5;
        }

        .pmb-flow-step h3 {
            margin: .12rem 0 .2rem;
        }

        .pmb-flow-step p {
            grid-column: 2;
        }
    }
</style>
@endpush

@section('content')
@php($startUrl = auth()->check() ? route('pmb.index') : route('login'))
<div class="pmb-utility">
    <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2 py-2">
        <span><i class="ti ti-map-pin me-1 text-danger"></i>Jl. Karel Satsuit Tubun No. 28A, Malang</span>
        <a href="{{ route('login') }}"><i class="ti ti-user-circle me-1"></i>Masuk / Daftar</a>
    </div>
</div>
<header class="pmb-public-nav">
    <div class="container d-flex align-items-center justify-content-between gap-3">
        <a class="d-flex align-items-center gap-2 text-decoration-none" href="{{ route('home') }}" aria-label="Beranda PMB">
            <img class="pmb-public-logo" src="{{ asset('logo.png') }}" alt="Logo Universitas Cipta Wacana">
            <span class="pmb-public-brand">Universitas Cipta Wacana<small>PENERIMAAN MAHASISWA BARU</small></span>
        </a>
        <nav class="pmb-nav-links d-flex align-items-center gap-3 gap-lg-4">
            <a class="nav-extra" href="#tentang">Tentang</a>
            <a class="nav-extra" href="{{ route('pmb.admissions') }}">Jalur PMB</a>
            <a class="nav-extra" href="#alur">Alur PMB</a>
            <a class="btn btn-sm btn-cwcu rounded-pill px-3" href="{{ $startUrl }}">{{ auth()->check() ? 'Lanjutkan' : 'Daftar' }}</a>
        </nav>
    </div>
</header>

<main>
    <section class="pmb-hero">
        <div class="container pmb-hero-content">
            <span class="pmb-hero-kicker"><i class="ti ti-sparkles"></i> Penerimaan Mahasiswa Baru</span>
            <h1>Siapkan masa depan,<br>mulai dari sini.</h1>
            <p>Jelajahi pilihan program studi Universitas Cipta Wacana dan ikuti proses pendaftaran mahasiswa baru secara online.</p>
            <div class="pmb-hero-actions d-flex flex-wrap align-items-center gap-2 mt-4">
                <a class="btn btn-light btn-lg fw-semibold rounded-pill px-4" href="{{ $startUrl }}"><i class="ti ti-arrow-right me-1"></i>{{ auth()->check() ? 'Buka pendaftaran' : 'Daftar sekarang' }}</a>
                <a class="btn btn-outline-light btn-lg rounded-pill px-4" href="#alur">Lihat alur PMB</a>
            </div>
        </div>
        <div class="pmb-hero-mark" aria-hidden="true"><img src="{{ asset('logo.png') }}" alt=""></div>
    </section>

    <section class="pmb-section pmb-welcome" id="tentang">
        <div class="container">
            <div class="row align-items-center g-4 g-lg-5">
                <div class="col-12 col-lg-5">
                    <div class="pmb-welcome-art"><img src="https://cwcu.ac.id/theme/assets/img/logo2.png" alt="Lambang Universitas Cipta Wacana"></div>
                </div>
                <div class="col-12 col-lg-7 pmb-welcome-copy">
                    <span class="pmb-section-heading eyebrow text-start d-block mb-2">SELAMAT DATANG</span>
                    <h2>Mulai perjalanan akademik Anda bersama Cipta Wacana</h2>
                    <p>Portal PMB membantu calon mahasiswa menjalani setiap tahap dalam satu tempat: melengkapi biodata, memilih periode dan jalur yang tersedia, mengirim dokumen, serta memantau proses seleksi.</p>
                    <a class="btn btn-cwcu rounded-pill px-4" href="{{ $startUrl }}">Jelajahi pendaftaran <i class="ti ti-arrow-up-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </section>

    <section class="pmb-section" id="alur">
        <div class="container">
            <div class="pmb-section-heading"><span class="eyebrow">LANGKAH PENDAFTARAN</span>
                <h2>Alur penerimaan mahasiswa baru</h2>
                <p>Ikuti tahapan pendaftaran dari awal hingga hasil seleksi. Ujian CBT hanya berlaku untuk jalur yang mewajibkannya.</p>
            </div>
            <div class="pmb-flow-card">
                <ol class="pmb-flow">
                    <li class="pmb-flow-step"><span class="pmb-flow-number">1</span>
                        <h3>Masuk atau klaim akun</h3>
                        <p>Masuk ke portal. Jika didaftarkan marketing, klaim pendaftaran dengan NIK dan tautan yang dibagikan.</p>
                    </li>
                    <li class="pmb-flow-step"><span class="pmb-flow-number">2</span>
                        <h3>Lengkapi biodata</h3>
                        <p>Isi data diri dan informasi keluarga dengan benar.</p>
                    </li>
                    <li class="pmb-flow-step"><span class="pmb-flow-number">3</span>
                        <h3>Pilih jalur &amp; program</h3>
                        <p>Pilih periode, jalur yang dibuka, dan program studi.</p>
                    </li>
                    <li class="pmb-flow-step"><span class="pmb-flow-number">4</span>
                        <h3>Kirim berkas &amp; pembayaran</h3>
                        <p>Unggah dokumen persyaratan dan bukti pembayaran untuk diverifikasi.</p>
                    </li>
                    <li class="pmb-flow-step"><span class="pmb-flow-number">5</span>
                        <h3>Ikuti proses seleksi</h3>
                        <p>Pantau status, ikuti ujian bila diwajibkan, dan lihat hasil seleksi.</p>
                    </li>
                </ol>
            </div>
        </div>
    </section>


    <section class="pb-5">
        <div class="container">
            <div class="pmb-section-heading"><span class="eyebrow">LAYANAN PMB</span>
                <h2>Informasi penting dalam satu portal</h2>
            </div>
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <article class="pmb-help-card"><i class="icon ti ti-file-description"></i>
                        <h3>Persyaratan berkas</h3>
                        <p>Periksa dokumen yang diminta dan status verifikasinya melalui akun pendaftaran.</p>
                    </article>
                </div>
                <div class="col-12 col-md-4">
                    <article class="pmb-help-card"><i class="icon ti ti-credit-card"></i>
                        <h3>Pembayaran</h3>
                        <p>Kirim bukti pembayaran dan tunggu verifikasi dari petugas PMB.</p>
                    </article>
                </div>
                <div class="col-12 col-md-4">
                    <article class="pmb-help-card"><i class="icon ti ti-bell-ringing"></i>
                        <h3>Status seleksi</h3>
                        <p>Pantau perkembangan pendaftaran dan informasi tahapan berikutnya.</p>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <section class="pb-5">
        <div class="container">
            <div class="pmb-cta d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h2 class="h3 mb-2">Siap memulai langkah Anda?</h2>
                    <p class="mb-0">Masuk ke portal dan lihat informasi penerimaan yang sedang dibuka.</p>
                </div><a class="btn btn-light btn-lg rounded-pill fw-semibold flex-shrink-0" href="{{ $startUrl }}">{{ auth()->check() ? 'Lanjutkan pendaftaran' : 'Masuk / Daftar' }} <i class="ti ti-arrow-up-right ms-1"></i></a>
            </div>
        </div>
    </section>
</main>

<footer class="pmb-public-footer">
    <div class="container d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2"><img class="pmb-footer-logo" src="{{ asset('logo.png') }}" alt=""><span>Portal Penerimaan Mahasiswa Baru<br><strong>Universitas Cipta Wacana</strong></span></div><span>Jl. Karel Satsuit Tubun No. 28A, Malang</span>
    </div>
</footer>
@endsection
