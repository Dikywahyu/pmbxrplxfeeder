<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#d71920">
    <title>@yield('title', 'Penerimaan Mahasiswa Baru') · PMB</title>
    <link rel="shortcut icon" type="image/png" href="https://cwcu.ac.id/theme/assets/img/logo2.png">
    <link rel="stylesheet" href="{{ asset('dist/assets/css/styles.css') }}">
    @stack('styles')
</head>
<body>
    @yield('content')
</body>
</html>
