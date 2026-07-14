<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SkemaPPHSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kosongkan tabel terlebih dahulu agar tidak duplikat saat dijalankan ulang
        Schema::disableForeignKeyConstraints();
        DB::table('tb_skema_pph21')->truncate();
        Schema::enableForeignKeyConstraints();

        // 1. DATA TARIF A
        $tarifA = [
            ['golongan' => 'A', 'batas_uang' => 11400000000, 'persen' => 0.34],
            ['golongan' => 'A', 'batas_uang' => 910000000, 'persen' => 0.33],
            ['golongan' => 'A', 'batas_uang' => 695000000, 'persen' => 0.32],
            ['golongan' => 'A', 'batas_uang' => 550000000, 'persen' => 0.31],
            ['golongan' => 'A', 'batas_uang' => 454000000, 'persen' => 0.30],
            ['golongan' => 'A', 'batas_uang' => 337000000, 'persen' => 0.29],
            ['golongan' => 'A', 'batas_uang' => 206000000, 'persen' => 0.28],
            ['golongan' => 'A', 'batas_uang' => 157000000, 'persen' => 0.27],
            ['golongan' => 'A', 'batas_uang' => 125000000, 'persen' => 0.26],
            ['golongan' => 'A', 'batas_uang' => 103000000, 'persen' => 0.25],
            ['golongan' => 'A', 'batas_uang' => 89000000, 'persen' => 0.24],
            ['golongan' => 'A', 'batas_uang' => 77500000, 'persen' => 0.23],
            ['golongan' => 'A', 'batas_uang' => 68600000, 'persen' => 0.22],
            ['golongan' => 'A', 'batas_uang' => 62200000, 'persen' => 0.21],
            ['golongan' => 'A', 'batas_uang' => 56300000, 'persen' => 0.20],
            ['golongan' => 'A', 'batas_uang' => 51400000, 'persen' => 0.19],
            ['golongan' => 'A', 'batas_uang' => 47800000, 'persen' => 0.18],
            ['golongan' => 'A', 'batas_uang' => 43850000, 'persen' => 0.17],
            ['golongan' => 'A', 'batas_uang' => 39100000, 'persen' => 0.16],
            ['golongan' => 'A', 'batas_uang' => 35400000, 'persen' => 0.15],
            ['golongan' => 'A', 'batas_uang' => 32400000, 'persen' => 0.14],
            ['golongan' => 'A', 'batas_uang' => 30050000, 'persen' => 0.13],
            ['golongan' => 'A', 'batas_uang' => 28000000, 'persen' => 0.12],
            ['golongan' => 'A', 'batas_uang' => 26450000, 'persen' => 0.11],
            ['golongan' => 'A', 'batas_uang' => 24150000, 'persen' => 0.10],
            ['golongan' => 'A', 'batas_uang' => 19750000, 'persen' => 0.09],
            ['golongan' => 'A', 'batas_uang' => 16950000, 'persen' => 0.08],
            ['golongan' => 'A', 'batas_uang' => 15100000, 'persen' => 0.07],
            ['golongan' => 'A', 'batas_uang' => 13750000, 'persen' => 0.06],
            ['golongan' => 'A', 'batas_uang' => 12500000, 'persen' => 0.05],
            ['golongan' => 'A', 'batas_uang' => 11600000, 'persen' => 0.04],
            ['golongan' => 'A', 'batas_uang' => 11050000, 'persen' => 0.035],
            ['golongan' => 'A', 'batas_uang' => 10700000, 'persen' => 0.03],
            ['golongan' => 'A', 'batas_uang' => 10350000, 'persen' => 0.025],
            ['golongan' => 'A', 'batas_uang' => 10050000, 'persen' => 0.0225],
            ['golongan' => 'A', 'batas_uang' => 9650000, 'persen' => 0.02],
            ['golongan' => 'A', 'batas_uang' => 8550000, 'persen' => 0.0175],
            ['golongan' => 'A', 'batas_uang' => 7500000, 'persen' => 0.015],
            ['golongan' => 'A', 'batas_uang' => 6750000, 'persen' => 0.0125],
            ['golongan' => 'A', 'batas_uang' => 6300000, 'persen' => 0.01],
            ['golongan' => 'A', 'batas_uang' => 5950000, 'persen' => 0.0075],
            ['golongan' => 'A', 'batas_uang' => 5650000, 'persen' => 0.005],
            ['golongan' => 'A', 'batas_uang' => 5400000, 'persen' => 0.0025],
        ];

        // 2. DATA TARIF B
        $tarifB = [
            ['golongan' => 'B', 'batas_uang' => 1405000000, 'persen' => 0.34],
            ['golongan' => 'B', 'batas_uang' => 957000000, 'persen' => 0.33],
            ['golongan' => 'B', 'batas_uang' => 704000000, 'persen' => 0.32],
            ['golongan' => 'B', 'batas_uang' => 555000000, 'persen' => 0.31],
            ['golongan' => 'B', 'batas_uang' => 459000000, 'persen' => 0.30],
            ['golongan' => 'B', 'batas_uang' => 374000000, 'persen' => 0.29],
            ['golongan' => 'B', 'batas_uang' => 211000000, 'persen' => 0.28],
            ['golongan' => 'B', 'batas_uang' => 163000000, 'persen' => 0.27],
            ['golongan' => 'B', 'batas_uang' => 129000000, 'persen' => 0.26],
            ['golongan' => 'B', 'batas_uang' => 109000000, 'persen' => 0.25],
            ['golongan' => 'B', 'batas_uang' => 93000000, 'persen' => 0.24],
            ['golongan' => 'B', 'batas_uang' => 80000000, 'persen' => 0.23],
            ['golongan' => 'B', 'batas_uang' => 71000000, 'persen' => 0.22],
            ['golongan' => 'B', 'batas_uang' => 64000000, 'persen' => 0.21],
            ['golongan' => 'B', 'batas_uang' => 58500000, 'persen' => 0.20],
            ['golongan' => 'B', 'batas_uang' => 53800000, 'persen' => 0.19],
            ['golongan' => 'B', 'batas_uang' => 49500000, 'persen' => 0.18],
            ['golongan' => 'B', 'batas_uang' => 45800000, 'persen' => 0.17],
            ['golongan' => 'B', 'batas_uang' => 41100000, 'persen' => 0.16],
            ['golongan' => 'B', 'batas_uang' => 37100000, 'persen' => 0.15],
            ['golongan' => 'B', 'batas_uang' => 33950000, 'persen' => 0.14],
            ['golongan' => 'B', 'batas_uang' => 31450000, 'persen' => 0.13],
            ['golongan' => 'B', 'batas_uang' => 29350000, 'persen' => 0.12],
            ['golongan' => 'B', 'batas_uang' => 27700000, 'persen' => 0.11],
            ['golongan' => 'B', 'batas_uang' => 26000000, 'persen' => 0.10],
            ['golongan' => 'B', 'batas_uang' => 21850000, 'persen' => 0.09],
            ['golongan' => 'B', 'batas_uang' => 18450000, 'persen' => 0.08],
            ['golongan' => 'B', 'batas_uang' => 16400000, 'persen' => 0.07],
            ['golongan' => 'B', 'batas_uang' => 14950000, 'persen' => 0.06],
            ['golongan' => 'B', 'batas_uang' => 13600000, 'persen' => 0.05],
            ['golongan' => 'B', 'batas_uang' => 12600000, 'persen' => 0.04],
            ['golongan' => 'B', 'batas_uang' => 11600000, 'persen' => 0.03],
            ['golongan' => 'B', 'batas_uang' => 11250000, 'persen' => 0.025],
            ['golongan' => 'B', 'batas_uang' => 10750000, 'persen' => 0.02],
            ['golongan' => 'B', 'batas_uang' => 9200000, 'persen' => 0.015],
            ['golongan' => 'B', 'batas_uang' => 7300000, 'persen' => 0.01],
            ['golongan' => 'B', 'batas_uang' => 6850000, 'persen' => 0.0075],
            ['golongan' => 'B', 'batas_uang' => 6500000, 'persen' => 0.005],
            ['golongan' => 'B', 'batas_uang' => 6200000, 'persen' => 0.0025],
        ];

        // 3. DATA TARIF C
        $tarifC = [
            ['golongan' => 'C', 'batas_uang' => 1419000000, 'persen' => 0.34],
            ['golongan' => 'C', 'batas_uang' => 965000000, 'persen' => 0.33],
            ['golongan' => 'C', 'batas_uang' => 709000000, 'persen' => 0.32],
            ['golongan' => 'C', 'batas_uang' => 561000000, 'persen' => 0.31],
            ['golongan' => 'C', 'batas_uang' => 463000000, 'persen' => 0.30],
            ['golongan' => 'C', 'batas_uang' => 390000000, 'persen' => 0.29],
            ['golongan' => 'C', 'batas_uang' => 221000000, 'persen' => 0.28],
            ['golongan' => 'C', 'batas_uang' => 169000000, 'persen' => 0.27],
            ['golongan' => 'C', 'batas_uang' => 134000000, 'persen' => 0.26],
            ['golongan' => 'C', 'batas_uang' => 110000000, 'persen' => 0.25],
            ['golongan' => 'C', 'batas_uang' => 95600000, 'persen' => 0.24],
            ['golongan' => 'C', 'batas_uang' => 83200000, 'persen' => 0.23],
            ['golongan' => 'C', 'batas_uang' => 74500000, 'persen' => 0.22],
            ['golongan' => 'C', 'batas_uang' => 66700000, 'persen' => 0.21],
            ['golongan' => 'C', 'batas_uang' => 60400000, 'persen' => 0.20],
            ['golongan' => 'C', 'batas_uang' => 55800000, 'persen' => 0.19],
            ['golongan' => 'C', 'batas_uang' => 51200000, 'persen' => 0.18],
            ['golongan' => 'C', 'batas_uang' => 47400000, 'persen' => 0.17],
            ['golongan' => 'C', 'batas_uang' => 43000000, 'persen' => 0.16],
            ['golongan' => 'C', 'batas_uang' => 38900000, 'persen' => 0.15],
            ['golongan' => 'C', 'batas_uang' => 35400000, 'persen' => 0.14],
            ['golongan' => 'C', 'batas_uang' => 32600000, 'persen' => 0.13],
            ['golongan' => 'C', 'batas_uang' => 30100000, 'persen' => 0.12],
            ['golongan' => 'C', 'batas_uang' => 28100000, 'persen' => 0.11],
            ['golongan' => 'C', 'batas_uang' => 26600000, 'persen' => 0.10],
            ['golongan' => 'C', 'batas_uang' => 22700000, 'persen' => 0.09],
            ['golongan' => 'C', 'batas_uang' => 19500000, 'persen' => 0.08],
            ['golongan' => 'C', 'batas_uang' => 17050000, 'persen' => 0.07],
            ['golongan' => 'C', 'batas_uang' => 15550000, 'persen' => 0.06],
            ['golongan' => 'C', 'batas_uang' => 14150000, 'persen' => 0.05],
            ['golongan' => 'C', 'batas_uang' => 12950000, 'persen' => 0.04],
            ['golongan' => 'C', 'batas_uang' => 12050000, 'persen' => 0.03],
            ['golongan' => 'C', 'batas_uang' => 11200000, 'persen' => 0.02],
            ['golongan' => 'C', 'batas_uang' => 10950000, 'persen' => 0.0175],
            ['golongan' => 'C', 'batas_uang' => 9800000, 'persen' => 0.015],
            ['golongan' => 'C', 'batas_uang' => 8850000, 'persen' => 0.0125],
            ['golongan' => 'C', 'batas_uang' => 7800000, 'persen' => 0.01],
            ['golongan' => 'C', 'batas_uang' => 7350000, 'persen' => 0.0075],
            ['golongan' => 'C', 'batas_uang' => 6950000, 'persen' => 0.005],
            ['golongan' => 'C', 'batas_uang' => 6600000, 'persen' => 0.0025],
        ];

        // Menggabungkan semua data skema tarif
        $semuaTarif = array_merge($tarifA, $tarifB, $tarifC);

        // Membagi array menjadi bagian-bagian lebih kecil (chunk) 
        // agar proses insert database lebih ringan dan cepat
        foreach (array_chunk($semuaTarif, 50) as $chunk) {
            DB::table('tb_skema_pph21')->insert($chunk);
        }
    }
}
