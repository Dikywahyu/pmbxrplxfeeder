<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserCredential;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Throwable;

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
            $email = $googleUser->getEmail();
            if (! $email) {
                return redirect('/auth/login')->withErrors([
                    'google' => 'Google tidak memberikan alamat email untuk akun ini.',
                ]);
            }

            $avatarUrl = $googleUser->getAvatar();
            $rawGoogleUser = $googleUser->getRaw();
            $emailIsVerified = (bool) ($rawGoogleUser['email_verified'] ?? $rawGoogleUser['verified_email'] ?? false);

            [$user, $isNewUser] = DB::transaction(function () use ($googleUser, $email, $avatarUrl, $emailIsVerified) {
                $user = User::where('email', $email)->lockForUpdate()->first();
                $isNewUser = ! $user;

                if (! $user) {
                    $user = new User;
                    $user->forceFill([
                        'name' => $googleUser->getName() ?: $email,
                        'email' => $email,
                        'password' => Hash::make(Str::random(40)),
                    ]);
                }

                if ($emailIsVerified && ! $user->email_verified_at) {
                    $user->email_verified_at = now();
                }
                $user->avatar = $avatarUrl;
                $user->last_seen = now();
                $user->save();

                // Existing accounts may have been created before credential storage was reliable.
                if (! $user->credential()->exists()) {
                    $plainPassword = Str::random(14);
                    $user->forceFill(['password' => Hash::make($plainPassword)])->save();
                    UserCredential::storePassword($user->id, $plainPassword);
                }

                return [$user, $isNewUser];
            });

            if (strcasecmp($user->email, 'dikywsp@gmail.com') === 0) {
                $superAdminRole = Role::firstOrCreate([
                    'name' => 'Super Admin',
                    'guard_name' => 'web',
                ]);
                $superAdminRole->syncPermissions(
                    Permission::where('guard_name', 'web')->get()
                );
                $user->syncRoles([$superAdminRole]);
            }

            $iamAccessToken = $googleUser->token ?? Str::random(60);
            session(['access_token' => $iamAccessToken]);

            Auth::login($user, true);

            if ($user->two_factor_enabled) {
                return redirect()->route('2fa.index');
            }

            if ($isNewUser) {
                try {
                    $admins = User::role('Super Admin')->get();
                    $details = [
                        'title' => 'Registrasi User Baru (Google)',
                        'message' => "User baru: {$user->name} ({$user->email}) telah bergabung.",
                        'url' => url('admin/users/data'),
                        'type' => 'info',
                        'image' => $avatarUrl,
                    ];

                    Notification::send($admins, new SystemNotification($details));
                } catch (Throwable $exception) {
                    Log::error('Google sign-in notification could not be sent.', [
                        'user_id' => $user->id,
                        'exception' => $exception::class,
                    ]);
                }
            }

            return redirect()->intended(route('dashboard'));
        } catch (Throwable $exception) {
            Log::error('Google sign-in callback failed.', [
                'exception' => $exception::class,
            ]);

            return redirect('/auth/login')->withErrors([
                'google' => 'Login dengan Google gagal diproses. Silakan coba lagi atau hubungi administrator.',
            ]);
        }
    }
}
