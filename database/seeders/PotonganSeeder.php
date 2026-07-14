<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PotonganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $karyawanIds = Karyawan::query()->pluck('id_karyawan');

        foreach ($karyawanIds as $idKaryawan) {
            DB::table('tb_potongan')->updateOrInsert(
                ['id_karyawan' => $idKaryawan],
                [
                    'potongan_sedekah_rombongan' => 0,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
