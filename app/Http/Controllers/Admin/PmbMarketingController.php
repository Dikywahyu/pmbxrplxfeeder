<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pmb\Candidate;
use App\Models\Pmb\CandidateClaim;
use App\Models\Pmb\Fee;
use App\Models\Pmb\Referral;
use App\Models\Pmb\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PmbMarketingController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_mahasiswa' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'handphone' => ['required', 'string', 'max:20'],
            'nik' => ['nullable', 'digits:16'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'fee_id' => ['required', 'integer', 'exists:pmb_fees,id'],
        ]);
        $data['email'] = mb_strtolower(trim($data['email']));

        if (Candidate::query()->whereRaw('LOWER(email) = ?', [$data['email']])->exists()) {
            throw ValidationException::withMessages([
                'email' => 'Email ini sudah tercatat sebagai calon mahasiswa. Cari pendaftar tersebut atau minta mahasiswa melanjutkan melalui akunnya.',
            ]);
        }

        if (filled($data['nik']) && Candidate::where('nik', $data['nik'])->exists()) {
            throw ValidationException::withMessages([
                'nik' => 'NIK ini sudah tercatat sebagai calon mahasiswa.',
            ]);
        }

        $referral = Referral::where('owner_id', $request->user()->id)
            ->where('is_active', true)
            ->firstOrFail();
        $fee = Fee::with(['period', 'path', 'program'])->findOrFail($data['fee_id']);
        $period = $fee->period;
        abort_unless(
            $period && $period->is_active
                && $period->tanggal_mulai->toDateString() <= today()->toDateString()
                && $period->tanggal_selesai->toDateString() >= today()->toDateString()
                && $fee->program?->is_active,
            422,
            'Periode atau program studi pada pilihan ini sudah tidak aktif.'
        );

        $expiresAt = now()->addDays(14);
        [$registration, $token] = DB::transaction(function () use ($data, $fee, $referral, $request, $expiresAt) {
            $candidate = Candidate::create([
                'nama_mahasiswa' => $data['nama_mahasiswa'],
                'email' => $data['email'],
                'handphone' => $data['handphone'],
                'nik' => $data['nik'] ?? null,
                'jenis_kelamin' => $data['jenis_kelamin'] ?? null,
            ]);

            do {
                $registrationNumber = 'PMB-'.now()->format('Y').'-'.Str::upper(Str::random(8));
            } while (Registration::where('nomor_pendaftaran', $registrationNumber)->exists());

            $registration = $candidate->registrations()->create([
                'period_id' => $fee->period_id,
                'input_by' => $request->user()->id,
                'referral_by' => $request->user()->id,
                'referral_id' => $referral->id,
                'path_id' => $fee->path_id,
                'prodi_id_1' => $fee->program_id,
                'nomor_pendaftaran' => $registrationNumber,
                'status' => 'draft',
            ]);

            $registration->payments()->create([
                'jenis_tagihan' => 'pendaftaran',
                'nominal_tagihan' => $fee->nominal_pendaftaran,
                'status' => (float) $fee->nominal_pendaftaran === 0.0 ? 'lunas' : 'pending',
            ]);

            $token = Str::random(64);
            CandidateClaim::create([
                'candidate_id' => $candidate->id,
                'token_hash' => hash('sha256', $token),
                'created_by' => $request->user()->id,
                'expires_at' => $expiresAt,
            ]);

            return [$registration, $token];
        });

        return response()->json([
            'message' => 'Pendaftar berhasil dibuat. Kirim link klaim pribadi ini kepada mahasiswa.',
            'registration_number' => $registration->nomor_pendaftaran,
            'claim_url' => route('pmb.claim.show', ['token' => $token]),
            'expires_at' => $expiresAt->toIso8601String(),
        ], 201);
    }

    public function renewClaimLink(Request $request, int $registrationId)
    {
        $registration = Registration::with('candidate.claim')
            ->where('referral_by', $request->user()->id)
            ->findOrFail($registrationId);
        $candidate = $registration->candidate;
        $claim = $candidate?->claim;

        abort_unless($claim && ! $claim->claimed_at && ! $candidate->user_id, 409, 'Pendaftaran ini sudah diklaim atau tidak memiliki link klaim.');

        $token = Str::random(64);
        $claim->update([
            'token_hash' => hash('sha256', $token),
            'created_by' => $request->user()->id,
            'expires_at' => now()->addDays(14),
        ]);

        return response()->json([
            'message' => 'Link klaim baru berhasil dibuat. Link sebelumnya sudah tidak berlaku.',
            'registration_number' => $registration->nomor_pendaftaran,
            'claim_url' => route('pmb.claim.show', ['token' => $token]),
            'expires_at' => $claim->expires_at->toIso8601String(),
        ]);
    }
}
