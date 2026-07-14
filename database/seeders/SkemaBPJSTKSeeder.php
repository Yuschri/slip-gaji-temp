<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SkemaBPJSTK;

class SkemaBPJSTKSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'iuran_jkk' => 0.0024,
                'iuran_jkm' => 0.003,
                'pemberi_kerja' => 0.037,
                'tenaga_kerja' => 0.02,
                'is_active' => true,
            ],
        ];

        foreach ($data as $item) {
            SkemaBPJSTK::create($item);
        }
    }
}
