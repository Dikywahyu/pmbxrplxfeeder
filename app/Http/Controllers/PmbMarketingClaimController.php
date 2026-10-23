<?php

namespace App\Http\Controllers;

use App\Models\Pmb\Referral;
use App\Models\Pmb\ReferralSppRate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PmbMarketingClaimController extends Controller
{
    public function show(string $token)
    {
        $referral = $this->findInvite($token);
        $state = $referral->owner_id || $referral->claimed_at
            ? 'claimed'
            : (($referral->claim_expires_at?->isPast() ?? true) ? 'expired' : 'ready');

        return view('pmb.marketing-claim', compact('referral', 'state'));
    }

    public function claim(Request $request, string $token)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = DB::transaction(function () use ($request, $token, $data) {
            $referral = Referral::where('claim_token_hash', hash('sha256', $token))
                ->lockForUpdate()
                ->firstOrFail();

            abort_if($referral->owner_id || $referral->claimed_at, 410, 'Link klaim sudah digunakan.');
            abort_if(! $referral->claim_expires_at || $referral->claim_expires_at->isPast(), 410, 'Link klaim sudah kedaluwarsa. Hubungi admin PMB untuk meminta link baru.');

            $email = mb_strtolower(trim($data['email']));
            if (User::query()->whereRaw('LOWER(email) = ?', [$email])->exists()) {
                throw ValidationException::withMessages([
                    'email' => 'Email ini sudah memiliki akun. Hubungi admin PMB agar referral ditautkan ke akun Anda.',
                ]);
            }

            $user = User::create([
                'name' => $referral->marketing_name,
                'email' => $email,
                'password' => Hash::make($data['password']),
                'phone' => $referral->whatsapp,
                // A one-time invitation delivered to the registered WhatsApp number is the
                // verification step for this marketing account onboarding flow.
                'email_verified_at' => now(),
            ]);

            $user->givePermissionTo('pmb-marketing');
            $referral->update([
                'owner_id' => $user->id,
                'contact_email' => $email,
                'claimed_at' => now(),
            ]);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('admin.pmb.section', ['section' => 'marketing'])->with('success', 'Akun marketing berhasil diklaim. Silakan lanjutkan ke dashboard PMB.');
    }

    private function findInvite(string $token): Referral
    {
        return Referral::query()
            ->whereNotNull('claim_token_hash')
            ->where('claim_token_hash', hash('sha256', $token))
            ->firstOrFail();
    }
}
