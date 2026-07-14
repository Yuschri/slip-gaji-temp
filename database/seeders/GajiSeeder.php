<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GajiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $karyawanIds = Karyawan::query()->pluck('id_karyawan');

        foreach ($karyawanIds as $idKaryawan) {
            DB::table('tb_gaji')->updateOrInsert(
                ['id_karyawan' => $idKaryawan],
                [
                    'gaji_pokok' => 0,
                    't_pengalaman_kerja' => 0,
                    't_jabatan' => 0,
                    't_profesi' => 0,
                    't_kehadiran' => 0,
                    't_kinerja' => 0,
                    't_operasional' => 0,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
