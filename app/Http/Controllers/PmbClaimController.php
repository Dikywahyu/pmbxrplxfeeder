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

        $state = blank($candidate->nik) ? 'nik_missing' : (auth()->check() ? 'ready' : 'create_account');

        return view('pmb.claim', compact('candidate', 'state'));
    }

    public function claim(Request $request, string $token)
    {
        $data = $request->validate([
            'nik' => ['required', 'digits:16'],
            'email' => [$request->user() ? 'nullable' : 'required', 'email', 'max:255'],
            'password' => [$request->user() ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
        ], ['nik.digits' => 'NIK harus terdiri dari 16 angka.']);

        $user = DB::transaction(function () use ($request, $token, $data) {
            $claim = CandidateClaim::where('token_hash', hash('sha256', $token))
                ->lockForUpdate()
                ->firstOrFail();
            abort_if($claim->claimed_at || $claim->expires_at->isPast(), 410, 'Link klaim sudah digunakan atau kedaluwarsa. Minta marketing mengirim link baru.');

            $candidate = Candidate::whereKey($claim->candidate_id)->lockForUpdate()->firstOrFail();
            abort_if($candidate->user_id, 410, 'Pendaftaran ini sudah terhubung ke akun mahasiswa.');
            abort_if(blank($candidate->nik), 422, 'NIK pendaftar belum tercatat. Hubungi marketing untuk memperbarui data.');
            if (! hash_equals((string) $candidate->nik, (string) $data['nik'])) {
                throw ValidationException::withMessages(['nik' => 'NIK tidak cocok dengan data pendaftaran.']);
            }

            if ($request->user()) {
                $user = $request->user();
                $candidate->email = $user->email;
            } else {
                $email = mb_strtolower(trim($data['email']));
                if (User::query()->whereRaw('LOWER(email) = ?', [$email])->exists()) {
                    throw ValidationException::withMessages([
                        'email' => 'Email ini sudah memiliki akun. Masuk menggunakan email tersebut, lalu buka kembali link klaim.',
                    ]);
                }

                $user = User::create([
                    'name' => $candidate->nama_mahasiswa,
                    'email' => $email,
                    'password' => $request->input('password'),
                    'phone' => $candidate->handphone,
                ]);
                $candidate->email = $email;
            }

            $candidate->user_id = $user->id;
            $candidate->save();
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
