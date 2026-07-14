<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\SkemaBPJSK;

class SkemaBPJSKSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'premi' => 0.05,
                'tanggungan_perusahaan' => 0.04,
                'tanggungan_karyawan' => 0.01,
            ],
        ];

        foreach ($data as $item) {
            SkemaBPJSK::create($item);
        }
    }
}
