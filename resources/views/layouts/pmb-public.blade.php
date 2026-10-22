<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#d71920">
    <title>@yield('title', 'Penerimaan Mahasiswa Baru | Universitas Cipta Wacana')</title>
    <meta name="description" content="@yield('meta_description', 'Informasi Penerimaan Mahasiswa Baru Universitas Cipta Wacana. Lihat jalur dan jadwal pendaftaran, persyaratan, serta daftar secara online.')">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <link rel="canonical" href="@yield('canonical_url', route('home'))">
    <link rel="shortcut icon" type="image/png" href="{{ asset('logo.png') }}">
    <meta property="og:locale" content="id_ID">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Universitas Cipta Wacana - PMB">
    <meta property="og:title" content="@yield('title', 'Penerimaan Mahasiswa Baru | Universitas Cipta Wacana')">
    <meta property="og:description" content="@yield('meta_description', 'Informasi Penerimaan Mahasiswa Baru Universitas Cipta Wacana. Lihat jalur dan jadwal pendaftaran, persyaratan, serta daftar secara online.')">
    <meta property="og:url" content="@yield('canonical_url', route('home'))">
    <meta property="og:image" content="{{ asset('logo.png') }}">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="@yield('title', 'Penerimaan Mahasiswa Baru | Universitas Cipta Wacana')">
    <meta name="twitter:description" content="@yield('meta_description', 'Informasi Penerimaan Mahasiswa Baru Universitas Cipta Wacana. Lihat jalur dan jadwal pendaftaran, persyaratan, serta daftar secara online.')">
    <meta name="twitter:image" content="{{ asset('logo.png') }}">
    @stack('meta')
    <link rel="stylesheet" href="{{ asset('dist/assets/css/styles.css') }}">
    @stack('styles')
</head>
<body>
    @yield('content')
</body>
</html>
