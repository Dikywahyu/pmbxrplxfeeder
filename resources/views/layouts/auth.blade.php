<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title') - SSO Server</title> 
    <link href="{{ asset('templates/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('templates/css/common.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,600,700&display=swap" rel="stylesheet">
    <link href="{{ asset('templates/css/theme-03.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('dist/assets/libs/sweetalert2/dist/sweetalert2.min.css') }}">
    
    <!-- Favicons -->
    <link
        rel="apple-touch-icon"
        sizes="57x57"
        href="https://cwcu.ac.id/theme/assets/img/logo2.png" />
    <link
        rel="apple-touch-icon"
        sizes="60x60"
        href="https://cwcu.ac.id/theme/assets/img/logo2.png" />
    <link
        rel="apple-touch-icon"
        sizes="72x72"
        href="https://cwcu.ac.id/theme/assets/img/logo2.png" />
    <link
        rel="apple-touch-icon"
        sizes="76x76"
        href="https://cwcu.ac.id/theme/assets/img/logo2.png" />
    <link
        rel="apple-touch-icon"
        sizes="114x114"
        href="https://cwcu.ac.id/theme/assets/img/logo2.png" />
    <link
        rel="apple-touch-icon"
        sizes="120x120"
        href="https://cwcu.ac.id/theme/assets/img/logo2.png" />
    <link
        rel="apple-touch-icon"
        sizes="144x144"
        href="https://cwcu.ac.id/theme/assets/img/logo2.png" />
    <link
        rel="apple-touch-icon"
        sizes="152x152"
        href="https://cwcu.ac.id/theme/assets/img/logo2.png" />
    <link
        rel="apple-touch-icon"
        sizes="180x180"
        href="https://cwcu.ac.id/theme/assets/img/logo2.png" />
    <link
        rel="icon"
        type="image/png"
        sizes="192x192"
        href="https://cwcu.ac.id/theme/assets/img/logo2.png" />
    <link
        rel="icon"
        type="image/png"
        sizes="32x32"
        href="https://cwcu.ac.id/theme/assets/img/logo2.png" />
    <link
        rel="icon"
        type="image/png"
        sizes="96x96"
        href="https://cwcu.ac.id/theme/assets/img/logo2.png" />
    <link
        rel="icon"
        type="image/png"
        sizes="16x16"
        href="https://cwcu.ac.id/theme/assets/img/logo2.png" />
    
    <meta name="msapplication-TileImage" content="https://cwcu.ac.id/theme/assets/img/logo2.png" />
    
    @stack('styles')
    
    <style>
        /* 1. Bebaskan Scroll di level HTML, Body, dan seluruh kontainer */
        html, body {
            height: auto !important;
            min-height: 100% !important;
            overflow-x: hidden !important;
            overflow-y: auto !important;
            position: relative !important;
        }

        /* 2. Paksa semua pembungkus Forny tidak memotong konten yang melimpah */
        .forny-container, 
        .forny-inner, 
        .forny-form {
            height: auto !important;
            min-height: auto !important;
            max-height: none !important;
            overflow: visible !important;
            position: relative !important;
        }

        .forny-container {
            display: block !important;
            padding: 30px 15px 120px 15px !important; /* Jarak bawah 120px */
        }

        .forny-inner {
            margin: 0 auto !important;
            max-width: 520px !important;
        }

        .forny-form {
            padding-bottom: 40px !important;
        }
    </style>
</head>

<body>
    <div class="forny-container">
        <div class="forny-inner">
            @yield('content')
        </div>
    </div>

    <script src="{{ asset('templates/js/jquery.min.js') }}"></script>
    <script src="{{ asset('templates/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('templates/js/main.js') }}"></script>
    <script src="{{ asset('dist/assets/libs/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    @include('layouts.partials.toast-notifications')
</body>
</html>
