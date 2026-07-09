<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DivisiJabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisi = [
            ['nama_divisi' => 'Medis'],
            ['nama_divisi' => 'Non-Medis'],
            ['nama_divisi' => 'Administrasi'],
            ['nama_divisi' => 'Umum'],
        ];

        foreach ($divisi as $d) {
            DB::table('tb_divisi')->updateOrInsert(
                ['nama_divisi' => $d['nama_divisi']],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        $jabatan = [
            ['nama_jabatan' => 'Dokter'],
            ['nama_jabatan' => 'Perawat'],
            ['nama_jabatan' => 'Apoteker'],
            ['nama_jabatan' => 'Administrasi'],
            ['nama_jabatan' => 'Kasir'],
            ['nama_jabatan' => 'Staf Umum'],
        ];

        foreach ($jabatan as $j) {
            DB::table('tb_jabatan')->updateOrInsert(
                ['nama_jabatan' => $j['nama_jabatan']],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
