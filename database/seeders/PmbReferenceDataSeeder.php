<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PmbReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        $timestamp = now();

        DB::table('agamas')->upsert([
            ['id_agama' => '1', 'nama_agama' => 'Islam', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_agama' => '2', 'nama_agama' => 'Kristen', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_agama' => '3', 'nama_agama' => 'Katolik', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_agama' => '4', 'nama_agama' => 'Hindu', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_agama' => '5', 'nama_agama' => 'Budha', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_agama' => '6', 'nama_agama' => 'Khonghucu', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_agama' => '7', 'nama_agama' => 'Penghayat Kepercayaan Terhadap Tuhan YME', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_agama' => '99', 'nama_agama' => 'Lainnya', 'created_at' => $timestamp, 'updated_at' => $timestamp],
        ], ['id_agama'], ['nama_agama', 'updated_at']);

        DB::table('alat_transportasis')->upsert([
            ['id_alat_transportasi' => '1', 'nama_alat_transportasi' => 'Jalan kaki', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat_transportasi' => '3', 'nama_alat_transportasi' => 'Angkutan umum/bus/pete-pete', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat_transportasi' => '4', 'nama_alat_transportasi' => 'Mobil/bus antar jemput', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat_transportasi' => '5', 'nama_alat_transportasi' => 'Kereta api', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat_transportasi' => '6', 'nama_alat_transportasi' => 'Ojek', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat_transportasi' => '7', 'nama_alat_transportasi' => 'Andong/bendi/sado/dokar/delman/becak', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat_transportasi' => '8', 'nama_alat_transportasi' => 'Perahu penyeberangan/rakit/getek', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat_transportasi' => '11', 'nama_alat_transportasi' => 'Kuda', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat_transportasi' => '12', 'nama_alat_transportasi' => 'Sepeda', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat_transportasi' => '13', 'nama_alat_transportasi' => 'Sepeda motor', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat_transportasi' => '14', 'nama_alat_transportasi' => 'Mobil pribadi', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_alat_transportasi' => '99', 'nama_alat_transportasi' => 'Lainnya', 'created_at' => $timestamp, 'updated_at' => $timestamp],
        ], ['id_alat_transportasi'], ['nama_alat_transportasi', 'updated_at']);

        DB::table('penghasilans')->upsert([
            ['id_penghasilan' => '0', 'nama_penghasilan' => 'Tidak disebutkan', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_penghasilan' => '11', 'nama_penghasilan' => 'Kurang dari Rp. 500,000', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_penghasilan' => '12', 'nama_penghasilan' => 'Rp. 500,000 - Rp. 999,999', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_penghasilan' => '13', 'nama_penghasilan' => 'Rp. 1,000,000 - Rp. 1,999,999', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_penghasilan' => '14', 'nama_penghasilan' => 'Rp. 2,000,000 - Rp. 4,999,999', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_penghasilan' => '15', 'nama_penghasilan' => 'Rp. 5,000,000 - Rp. 20,000,000', 'created_at' => $timestamp, 'updated_at' => $timestamp],
            ['id_penghasilan' => '16', 'nama_penghasilan' => 'Lebih dari Rp. 20,000,000', 'created_at' => $timestamp, 'updated_at' => $timestamp],
        ], ['id_penghasilan'], ['nama_penghasilan', 'updated_at']);

        $wilayahPath = database_path('seeders/data/wilayahs.json');
        $wilayahRows = json_decode(file_get_contents($wilayahPath), true, 512, JSON_THROW_ON_ERROR);

        foreach (array_chunk($wilayahRows, 500) as $chunk) {
            $rows = array_map(fn (array $row) => $row + [
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ], $chunk);

            DB::table('wilayahs')->upsert(
                $rows,
                ['id_wilayah'],
                ['nama_wilayah', 'id_level_wilayah', 'id_negara', 'id_induk_wilayah', 'updated_at']
            );
        }
    }
}
