@extends('layouts.auth')

@section('title', 'Login - Integrated Academic Management')

@section('content')
<div class="forny-form">
    <div class="mb-8 text-center forny-logo">
        <img src="https://cwcu.ac.id/theme/assets/img/logo.png">
    </div>
    
    <div class="text-center">
        <h4>Sign In SSO</h4>
        <p class="mb-10">Gunakan akun Google instansi untuk akses cepat.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger py-2" data-app-toast>
            <small>{{ $errors->first() }}</small>
        </div>
    @endif

    <div class="text-center mb-6">
        <a href="{{ route('auth.google') }}" class="btn btn-outline-danger btn-block py-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 32 32" class="mr-2">
                <path fill="#EA4335" d="M16 12.85V19.15H24.85C24.1 22.35 21.3 25.1 16 25.1C10.95 25.1 6.85 21.05 6.85 16C6.85 10.95 10.95 6.9 16 6.9C18.3 6.9 20.35 7.7 21.95 9.1L26.45 4.6C23.65 1.95 19.95 0.5 16 0.5C7.45 0.5 0.5 7.45 0.5 16C0.5 24.55 7.45 31.5 16 31.5C24.55 31.5 31.5 24.55 31.5 16C31.5 14.55 31.35 13.15 31.05 11.85H16V12.85Z"/>
            </svg>
            Masuk dengan Google
        </a>
    </div>

    <div class="text-center mb-6">
        <a href="javascript:void(0)" id="show-manual-link" class="text-muted"><small>Atau masuk dengan Email & Password</small></a>
    </div>

    <form action="{{ url('/auth/login') }}" method="POST" id="manual-form" style="display: none;">
        @csrf
        <div class="form-group">
            <div class="input-group">
                <input required class="form-control" name="email" type="email" placeholder="Email Address">
            </div>
        </div>

        <div class="form-group password-field mb-3">
            <label class="form-label small text-muted">Password</label>
            <div class="input-group">
                <!-- Input Password diberikan id="password-field" -->
                <input required class="form-control" name="password" type="password" id="password-field" placeholder="Password">
                
                <!-- Tombol Mata (Toggle Show/Hide) -->
                <button class="btn btn-outline-secondary" type="button" id="toggle-password">
                    <span id="eye-icon">👁️</span>
                </button>
            </div>
        </div>

        <div class="row mt-6 mb-6">
            <div class="col-6 d-flex align-items-center">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="remember" class="custom-control-input" id="cb1">
                    <label class="custom-control-label" for="cb1">Remember me</label>
                </div>
            </div>
            <div class="col-6 text-right">
                <a href="{{ route('password.request') }}">Forgot password?</a>
            </div>
        </div>

        <div>
            <button class="btn btn-primary btn-block">Login Manual</button>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        // --- SKRIP TOGGLE SHOW/HIDE PASSWORD ---
        $('#toggle-password').on('click', function() {
            const passwordField = $('#password-field');
            const eyeIcon = $('#eye-icon');
            
            if (passwordField.attr('type') === 'password') {
                passwordField.attr('type', 'text');
                eyeIcon.text('🙈');
            } else {
                passwordField.attr('type', 'password');
                eyeIcon.text('👁️');
            }
        });

        // --- SKRIP TOGGLE FORM MANUAL ---
        $('#show-manual-link').on('click', function(e) {
            e.preventDefault();
            $('#manual-toggle-container').fadeOut(200, function() {
                $('#manual-form').slideDown(400);
            });
        });

        $('#hide-manual-link').on('click', function(e) {
            e.preventDefault();
            $('#manual-form').slideUp(400, function() {
                $('#manual-toggle-container').fadeIn(200);
            });
        });
    });
    document.getElementById('show-manual-link').addEventListener('click', function() {
        document.getElementById('manual-form').style.display = 'block';
        this.style.display = 'none';
    });
</script>
@endsection
