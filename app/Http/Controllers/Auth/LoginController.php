<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard';

    /**
     * Tampilkan form login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * 🟢 FUNGSI BARU: Memproses input form login manual Email & Password
     */
    public function login(Request $request)
    {
        // 1. Validasi input form
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        // 2. Ambil kredensial email & password
        $credentials = $request->only('email', 'password');
        $remember    = $request->filled('remember');

        // 3. Lakukan percobaan login (Attempt)
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // SINKRONISASI AKTIVITAS LOG
            activity()
                ->causedBy($user)
                ->log('User logged in via manual form');

            // 🟢 JIKA USER MENGAKTIFKAN 2FA: 
            // Buat intended mengarah ke dashboard, lalu biarkan Google2FAMiddleware yang mencegatnya!
            if ($user->two_factor_enabled) {
                return redirect()->intended('/dashboard');
            }

            // Jika tidak aktif 2FA, langsung lolos ke dashboard utama
            return redirect()->intended($this->redirectPath());
        }

        // 4. Jika password/email salah, kembalikan ke halaman login dengan pesan eror bawaan
        return redirect()->back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => 'Kredensial yang Anda masukkan tidak cocok dengan data kami.',
            ]);
    }

    /**
     * Logika setelah user berhasil login manual (Fallback).
     */
    protected function authenticated(Request $request, $user)
    {
        // Tetap jaga fungsi log aktivitas lama Anda jika dibutuhkan
    }
}