<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pmb\Candidate;
use App\Models\Pmb\CandidateClaim;
use App\Models\Pmb\CandidateDocument;
use App\Models\Pmb\DocumentRequirement;
use App\Models\Pmb\Fee;
use App\Models\Pmb\Payment;
use App\Models\Pmb\PeriodPathSchedule;
use App\Models\Pmb\Referral;
use App\Models\Pmb\ReferralSppRate;
use App\Models\Pmb\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PmbMarketingController extends Controller
{
    public function registrationDetail(Request $request, int $registrationId)
    {
        $registration = Registration::with([
            'candidate', 'period', 'path.documentRequirements', 'program1', 'documents.requirement', 'payments',
        ])->where('referral_by', $request->user()->id)->findOrFail($registrationId);

        return response()->json([
            'html' => view('admin.pmb.partials.marketing_registration_detail', compact('registration'))->render(),
        ]);
    }

    public function previewRegistrationDocument(Request $request, int $registrationId, int $documentId)
    {
        $registration = Registration::where('referral_by', $request->user()->id)->findOrFail($registrationId);
        $document = CandidateDocument::where('registration_id', $registration->id)->findOrFail($documentId);
        abort_unless(Storage::disk('local')->exists($document->file_path), 404);

        $mimeType = Storage::disk('local')->mimeType($document->file_path);
        abort_unless(in_array($mimeType, ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'], true), 415);

        return Storage::disk('local')->response(
            $document->file_path,
            basename($document->file_path),
            ['X-Content-Type-Options' => 'nosniff'],
            'inline'
        );
    }

    public function uploadRegistrationDocument(Request $request, int $registrationId, int $requirementId)
    {
        $registration = Registration::where('referral_by', $request->user()->id)->findOrFail($registrationId);
        $requirement = DocumentRequirement::whereKey($requirementId)
            ->where('path_id', $registration->path_id)
            ->firstOrFail();
        $allowed = collect(explode(',', strtolower($requirement->format_file)))
            ->map(fn ($format) => ltrim(trim($format), '.'))
            ->filter(fn ($format) => in_array($format, ['pdf', 'jpg', 'jpeg', 'png', 'webp'], true))->values();
        abort_if($allowed->isEmpty(), 422, 'Format unggahan persyaratan belum dikonfigurasi.');

        $data = $request->validate([
            'file' => ['required', 'file', 'max:10240', 'mimes:'.$allowed->implode(',')],
        ], [
            'file.max' => 'Ukuran berkas maksimal 10 MB.',
            'file.mimes' => 'Format berkas harus berupa: '.$allowed->implode(', ').'.',
        ]);

        $document = CandidateDocument::firstOrNew([
            'registration_id' => $registration->id,
            'requirement_id' => $requirement->id,
        ]);
        $oldPath = $document->file_path;
        $document->fill([
            'file_path' => $data['file']->store('pmb/candidate-documents', 'local'),
            'status_verifikasi' => 'pending',
            'catatan_admin' => null,
        ])->save();
        if ($oldPath) {
            Storage::disk('local')->delete($oldPath);
        }

        return response()->json(['message' => 'Berkas berhasil diunggah dan menunggu verifikasi.']);
    }

    public function previewRegistrationPaymentProof(Request $request, int $registrationId, int $paymentId)
    {
        $registration = Registration::where('referral_by', $request->user()->id)->findOrFail($registrationId);
        $payment = Payment::where('registration_id', $registration->id)->findOrFail($paymentId);
        abort_unless($payment->file_bukti_bayar, 404);
        abort_unless(Storage::disk('local')->exists($payment->file_bukti_bayar), 404);

        $mimeType = Storage::disk('local')->mimeType($payment->file_bukti_bayar);
        abort_unless(in_array($mimeType, ['application/pdf', 'image/jpeg', 'image/png'], true), 415);

        return Storage::disk('local')->response(
            $payment->file_bukti_bayar,
            basename($payment->file_bukti_bayar),
            ['X-Content-Type-Options' => 'nosniff'],
            'inline'
        );
    }

    public function uploadRegistrationPaymentProof(Request $request, int $registrationId, int $paymentId)
    {
        $registration = Registration::where('referral_by', $request->user()->id)->findOrFail($registrationId);
        $payment = Payment::where('registration_id', $registration->id)->findOrFail($paymentId);
        abort_unless(in_array($payment->status, ['pending', 'menunggu_verifikasi', 'ditolak'], true), 422, 'Bukti hanya dapat diunggah sebelum tagihan diverifikasi lunas.');

        $data = $request->validate([
            'bukti_bayar' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ], [
            'bukti_bayar.max' => 'Ukuran bukti bayar maksimal 5 MB.',
            'bukti_bayar.mimes' => 'Bukti bayar harus berupa PDF, JPG, JPEG, atau PNG.',
        ]);

        $path = $data['bukti_bayar']->store('pmb/payment-proofs', 'local');
        $oldPath = $payment->file_bukti_bayar;
        DB::transaction(function () use ($payment, $path, $registration) {
            $payment->update([
                'file_bukti_bayar' => $path,
                'status' => 'menunggu_verifikasi',
                'verified_by' => null,
            ]);
            $registration->update(['status' => 'berkas_masuk']);
        });
        if ($oldPath) {
            Storage::disk('local')->delete($oldPath);
        }

        return response()->json(['message' => 'Bukti bayar berhasil diunggah dan menunggu verifikasi.']);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_mahasiswa' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'handphone' => ['required', 'string', 'max:20'],
            'nik' => ['required', 'digits:16'],
            'nisn' => ['nullable', 'string', 'max:20'],
            'npwp' => ['nullable', 'string', 'max:25'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'tempat_lahir' => ['required', 'string', 'max:255'],
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'id_agama' => ['nullable', 'string', 'max:5'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'jalan' => ['required', 'string', 'max:255'],
            'dusun' => ['nullable', 'string', 'max:255'],
            'rt' => ['nullable', 'string', 'max:5'],
            'rw' => ['nullable', 'string', 'max:5'],
            'kelurahan' => ['required', 'string', 'max:255'],
            'kode_pos' => ['nullable', 'string', 'max:10'],
            'id_wilayah' => ['nullable', 'string', 'max:10'],
            'id_negara' => ['nullable', 'string', 'max:5'],
            'nomor_daftar_kip' => ['nullable', 'string', 'max:90'],
            'nomor_kip' => ['nullable', 'string', 'max:50'],
            'nomor_kks' => ['nullable', 'string', 'max:50'],
            'nomor_kps' => ['nullable', 'string', 'max:50'],
            'penerima_kps' => ['nullable', 'boolean'],
            'terdaftar_dtks' => ['nullable', 'boolean'],
            'nik_ayah' => ['nullable', 'string', 'max:20'],
            'nama_ayah' => ['nullable', 'string', 'max:255'],
            'tanggal_lahir_ayah' => ['nullable', 'date', 'before:today'],
            'id_pendidikan_ayah' => ['nullable', 'string', 'max:5'],
            'nama_pendidikan_ayah' => ['nullable', 'string', 'max:255'],
            'id_pekerjaan_ayah' => ['nullable', 'string', 'max:5'],
            'nama_pekerjaan_ayah' => ['nullable', 'string', 'max:255'],
            'id_penghasilan_ayah' => ['nullable', 'string', 'max:5'],
            'nama_penghasilan_ayah' => ['nullable', 'string', 'max:255'],
            'nama_ibu_kandung' => ['required', 'string', 'max:255'],
            'nik_ibu' => ['nullable', 'string', 'max:20'],
            'tanggal_lahir_ibu' => ['nullable', 'date', 'before:today'],
            'id_pendidikan_ibu' => ['nullable', 'string', 'max:5'],
            'nama_pendidikan_ibu' => ['nullable', 'string', 'max:255'],
            'id_pekerjaan_ibu' => ['nullable', 'string', 'max:5'],
            'nama_pekerjaan_ibu' => ['nullable', 'string', 'max:255'],
            'id_penghasilan_ibu' => ['nullable', 'string', 'max:5'],
            'nama_penghasilan_ibu' => ['nullable', 'string', 'max:255'],
            'nama_wali' => ['nullable', 'string', 'max:255'],
            'tanggal_lahir_wali' => ['nullable', 'date', 'before:today'],
            'id_pendidikan_wali' => ['nullable', 'string', 'max:5'],
            'nama_pendidikan_wali' => ['nullable', 'string', 'max:255'],
            'id_pekerjaan_wali' => ['nullable', 'string', 'max:5'],
            'nama_pekerjaan_wali' => ['nullable', 'string', 'max:255'],
            'id_penghasilan_wali' => ['nullable', 'string', 'max:5'],
            'nama_penghasilan_wali' => ['nullable', 'string', 'max:255'],
            'fee_id' => ['required', 'integer', 'exists:pmb_fees,id'],
        ], [
            'nik.digits' => 'NIK harus terdiri dari 16 angka.',
            'nama_ibu_kandung.required' => 'Nama ibu kandung wajib diisi.',
        ]);
        $data['email'] = filled($data['email'] ?? null) ? mb_strtolower(trim($data['email'])) : null;
        $data['id_negara'] = filled($data['id_negara'] ?? null) ? $data['id_negara'] : 'ID';

        if (Candidate::where('nik', $data['nik'])->exists()) {
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
                && PeriodPathSchedule::where('period_id', $fee->period_id)
                    ->where('path_id', $fee->path_id)
                    ->where('is_active', true)
                    ->whereDate('tanggal_mulai', '<=', today())
                    ->whereDate('tanggal_selesai', '>=', today())
                    ->exists()
                && $fee->program?->is_active,
            422,
            'Periode atau jadwal jalur pendaftaran sudah ditutup, atau program studi tidak aktif.'
        );

        $requirements = DocumentRequirement::where('path_id', $fee->path_id)->get()->keyBy('id');
        $uploadedFiles = $request->file('documents', []);
        if (! is_array($uploadedFiles)) {
            throw ValidationException::withMessages(['documents' => 'Format berkas tidak valid.']);
        }
        foreach ($uploadedFiles as $requirementId => $file) {
            $requirement = $requirements->get((int) $requirementId);
            if (! $requirement || ! $file) {
                throw ValidationException::withMessages(["documents.{$requirementId}" => 'Persyaratan berkas tidak sesuai dengan jalur pendaftaran.']);
            }
            $allowed = collect(explode(',', strtolower($requirement->format_file)))
                ->map(fn ($format) => ltrim(trim($format), '.'))
                ->filter(fn ($format) => in_array($format, ['pdf', 'jpg', 'jpeg', 'png', 'webp'], true))->values();
            if ($allowed->isEmpty()) {
                throw ValidationException::withMessages(["documents.{$requirementId}" => "Format untuk {$requirement->nama_dokumen} belum dikonfigurasi."]);
            }
            Validator::make(['file' => $file], [
                'file' => ['file', 'max:10240', 'mimes:'.$allowed->implode(',')],
            ], [
                'file.max' => 'Ukuran setiap berkas maksimal 10 MB.',
                'file.mimes' => "Format {$requirement->nama_dokumen} harus berupa: ".$allowed->implode(', ').'.',
            ])->validate();
        }

        $expiresAt = now()->addDays(14);
        $nominalSpp = ReferralSppRate::where('referral_id', $referral->id)
            ->where('fee_id', $fee->id)
            ->value('nominal_spp') ?? $fee->nominal_spp;

        [$registration, $token] = DB::transaction(function () use ($data, $fee, $referral, $request, $expiresAt, $uploadedFiles, $nominalSpp) {
            $candidateData = $data;
            unset($candidateData['fee_id']);
            $candidate = Candidate::create($candidateData);

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
                'nominal_spp' => $nominalSpp,
                'nomor_pendaftaran' => $registrationNumber,
                'status' => 'draft',
            ]);

            $registration->payments()->create([
                'jenis_tagihan' => 'pendaftaran',
                'nominal_tagihan' => $fee->nominal_pendaftaran,
                'status' => (float) $fee->nominal_pendaftaran === 0.0 ? 'lunas' : 'pending',
            ]);

            foreach ($uploadedFiles as $requirementId => $file) {
                CandidateDocument::create([
                    'registration_id' => $registration->id,
                    'requirement_id' => (int) $requirementId,
                    'file_path' => $file->store('pmb/candidate-documents', 'local'),
                    'status_verifikasi' => 'pending',
                ]);
            }

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
