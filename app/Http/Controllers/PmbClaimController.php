<?php

namespace App\Http\Controllers;

use App\Models\Pmb\Candidate;
use App\Models\Pmb\CandidateClaim;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PmbClaimController extends Controller
{
    public function show(string $token)
    {
        $claim = CandidateClaim::with('candidate')
            ->where('token_hash', hash('sha256', $token))
            ->firstOrFail();

        if ($claim->claimed_at || $claim->candidate?->user_id) {
            if (auth()->check() && auth()->id() === $claim->candidate?->user_id) {
                return redirect()->route('pmb.index')->with('success', 'Akun Anda sudah terhubung dengan pendaftaran ini.');
            }
            return view('pmb.claim', ['state' => 'claimed', 'candidate' => null]);
        }

        $candidate = $claim->candidate;
        abort_unless($candidate, 404);
        if ($claim->expires_at->isPast()) {
            return view('pmb.claim', ['state' => 'expired', 'candidate' => $candidate]);
        }

        $existingUser = User::query()->whereRaw('LOWER(email) = ?', [mb_strtolower($candidate->email)])->exists();
        if (! auth()->check() && $existingUser) {
            return redirect()->guest(route('login'));
        }

        $state = auth()->check()
            ? (mb_strtolower(auth()->user()->email) === mb_strtolower($candidate->email) ? 'ready' : 'email_mismatch')
            : 'create_account';

        return view('pmb.claim', compact('candidate', 'state'));
    }

    public function claim(Request $request, string $token)
    {
        if (! $request->user()) {
            $request->validate([
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);
        }

        $user = DB::transaction(function () use ($request, $token) {
            $claim = CandidateClaim::where('token_hash', hash('sha256', $token))
                ->lockForUpdate()
                ->firstOrFail();
            abort_if($claim->claimed_at || $claim->expires_at->isPast(), 410, 'Link klaim sudah digunakan atau kedaluwarsa. Minta marketing mengirim link baru.');

            $candidate = Candidate::whereKey($claim->candidate_id)->lockForUpdate()->firstOrFail();
            abort_if($candidate->user_id, 410, 'Pendaftaran ini sudah terhubung ke akun mahasiswa.');

            if ($request->user()) {
                $user = $request->user();
                if (mb_strtolower($user->email) !== mb_strtolower($candidate->email)) {
                    throw ValidationException::withMessages([
                        'email' => 'Link ini ditujukan untuk akun '.$candidate->email.'. Keluar dan masuk menggunakan email tersebut.',
                    ]);
                }
            } else {
                $email = mb_strtolower($candidate->email);
                if (User::query()->whereRaw('LOWER(email) = ?', [$email])->exists()) {
                    throw ValidationException::withMessages([
                        'email' => 'Akun dengan email ini sudah ada. Silakan masuk, lalu buka kembali link klaim.',
                    ]);
                }

                $user = User::create([
                    'name' => $candidate->nama_mahasiswa,
                    'email' => $email,
                    'password' => $request->input('password'),
                    'phone' => $candidate->handphone,
                ]);
            }

            $candidate->update(['user_id' => $user->id]);
            $claim->update(['claimed_at' => now()]);

            return $user;
        });

        if (! $request->user()) {
            Auth::login($user);
            $request->session()->regenerate();
        }

        return redirect()->route('pmb.index')->with('success', 'Akun berhasil diklaim. Silakan lengkapi biodata dan berkas pendaftaran Anda.');
    }
}
