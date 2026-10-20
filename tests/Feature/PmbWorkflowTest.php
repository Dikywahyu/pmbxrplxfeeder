<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PmbWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_applicant_can_create_a_registration_and_its_payment_record(): void
    {
        $user = User::factory()->create();
        $periodId = $this->createPmbOptions();

        $response = $this->actingAs($user)->post(route('pmb.registrations.store'), [
            'nama_mahasiswa' => 'Calon Mahasiswa',
            'nik' => '3201010101010001',
            'jenis_kelamin' => 'L',
            'handphone' => '081234567890',
            'period_id' => $periodId,
            'path_id' => 1,
            'prodi_id' => 1,
        ]);

        $response->assertRedirect(route('pmb.index'));
        $this->assertDatabaseHas('pmb_candidates', [
            'user_id' => $user->id,
            'nama_mahasiswa' => 'Calon Mahasiswa',
        ]);
        $this->assertDatabaseHas('pmb_registrations', [
            'candidate_id' => DB::table('pmb_candidates')->where('user_id', $user->id)->value('id'),
            'period_id' => $periodId,
            'status' => 'draft',
        ]);
        $this->assertDatabaseHas('pmb_payments', [
            'jenis_tagihan' => 'pendaftaran',
            'nominal_tagihan' => 250000,
            'status' => 'pending',
        ]);
    }

    public function test_applicant_can_submit_payment_proof_and_another_applicant_cannot_download_it(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $periodId = $this->createPmbOptions();
        $candidateId = DB::table('pmb_candidates')->insertGetId([
            'user_id' => $user->id,
            'nama_mahasiswa' => 'Calon Mahasiswa',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $registrationId = DB::table('pmb_registrations')->insertGetId([
            'candidate_id' => $candidateId,
            'period_id' => $periodId,
            'nomor_pendaftaran' => 'PMB-TEST-0001',
            'status' => 'draft',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $paymentId = DB::table('pmb_payments')->insertGetId([
            'registration_id' => $registrationId,
            'jenis_tagihan' => 'pendaftaran',
            'nominal_tagihan' => 250000,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($user)
            ->post(route('pmb.payments.proof', [$registrationId, $paymentId]), [
                'bukti_bayar' => UploadedFile::fake()->create('bukti.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect(route('pmb.index'));

        $payment = DB::table('pmb_payments')->where('id', $paymentId)->first();
        $this->assertSame('menunggu_verifikasi', $payment->status);
        $this->assertSame('berkas_masuk', DB::table('pmb_registrations')->where('id', $registrationId)->value('status'));
        Storage::disk('local')->assertExists($payment->file_bukti_bayar);

        $this->actingAs($otherUser)
            ->get(route('pmb.payments.download', $paymentId))
            ->assertNotFound();
    }

    public function test_authorized_finance_user_can_approve_a_payment(): void
    {
        $financeUser = User::factory()->create();
        Permission::firstOrCreate(['name' => 'pmb-payment-review', 'guard_name' => 'web']);
        $financeUser->givePermissionTo('pmb-payment-review');

        $user = User::factory()->create();
        $periodId = $this->createPmbOptions();
        $candidateId = DB::table('pmb_candidates')->insertGetId([
            'user_id' => $user->id,
            'nama_mahasiswa' => 'Calon Mahasiswa',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $registrationId = DB::table('pmb_registrations')->insertGetId([
            'candidate_id' => $candidateId,
            'period_id' => $periodId,
            'nomor_pendaftaran' => 'PMB-TEST-0002',
            'status' => 'berkas_masuk',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $paymentId = DB::table('pmb_payments')->insertGetId([
            'registration_id' => $registrationId,
            'jenis_tagihan' => 'pendaftaran',
            'nominal_tagihan' => 250000,
            'file_bukti_bayar' => 'pmb/payment-proofs/proof.pdf',
            'status' => 'menunggu_verifikasi',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($financeUser)
            ->patch(route('admin.pmb.payments.verify', $paymentId), ['status' => 'lunas'])
            ->assertRedirect();

        $this->assertDatabaseHas('pmb_payments', [
            'id' => $paymentId,
            'status' => 'lunas',
            'verified_by' => $financeUser->id,
        ]);
    }

    private function createPmbOptions(): int
    {
        $periodId = DB::table('pmb_periods')->insertGetId([
            'nama_periode' => 'PMB Aktif',
            'tanggal_mulai' => today()->subDay()->toDateString(),
            'tanggal_selesai' => today()->addMonth()->toDateString(),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('pmb_paths')->insert([
            'id' => 1,
            'nama_jalur' => 'Reguler',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('pmb_study_programs')->insert([
            'id' => 1,
            'kode_prodi' => 'TEST01',
            'nama_prodi' => 'Program Uji',
            'jenjang' => 'S1',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('pmb_fees')->insert([
            'period_id' => $periodId,
            'path_id' => 1,
            'program_id' => 1,
            'nominal_pendaftaran' => 250000,
            'nominal_spp' => 5000000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $periodId;
    }
}
