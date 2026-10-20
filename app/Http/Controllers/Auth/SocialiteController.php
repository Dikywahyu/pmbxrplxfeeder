<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserCredential;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SystemNotification;

class SocialiteController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
        // dd(Socialite::driver('google')->redirect()->getTargetUrl());
    }
 
    
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $plainPassword = Str::random(14);

            // Ambil avatar di awal agar konsisten
            $avatarUrl = $googleUser->getAvatar();

            // 1. Cari atau buat user
            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'username' => Str::slug($googleUser->getName()) . rand(10, 99),
                    'password' => Hash::make($plainPassword),
                    'is_active' => true,
                ]
            );

            // Simpan status wasRecentlyCreated karena update() bisa meresetnya pada beberapa versi Laravel
            $isNewUser = $user->wasRecentlyCreated;

            // 2. Update data profil terbaru
            $user->update([
                'avatar'    => $avatarUrl,
                'last_seen' => now(),
            ]);

            // --- NOTIFIKASI USER BARU ---
            if ($isNewUser) {
                $details = [
                    'title'   => 'Registrasi User Baru (Google)',
                    'message' => "User baru: {$user->name} ({$user->email}) telah bergabung.",
                    'url'     => url('admin/users/data'),
                    'type'    => 'info',
                    'image'   => $avatarUrl,
                ];

                $admins = User::role('Super Admin')->get();
                
                UserCredential::storePassword($user->id, $plainPassword);

                \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\SystemNotification($details));
            }

            // Assign Role khusus Admin
            if ($user->email === 'dikywsp@gmail.com') {
                $user->assignRole(['Super Admin']);
            } 

            // ==================== KUNCI PERBAIKAN: GENERATE ACCESS TOKEN IAM ====================
            // Buat token acak untuk dilempar ke SIAKAD (Bisa pakai Str::random atau token dari Google jika diinginkan)
            $iamAccessToken = $googleUser->token ?? Str::random(60);
            
            // Simpan token ini ke session IAM agar bisa dipanggil oleh card dashboard {{ session('access_token') }}
            session(['access_token' => $iamAccessToken]);
            // ====================================================================================
            
            if ($user->two_factor_enabled) {
                // Loginkan user secara dasar agar session Auth-nya terbentuk
                Auth::login($user);
                
                // Jika pakai 2FA, token tetap aman di session, tinggal diarahkan ke verifikasi OTP
                return redirect()->route('2fa.index');
            }

            Auth::login($user, true);
            return redirect()->intended('/dashboard');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Gagal login: ' . $e->getMessage());
        }
    }
}
