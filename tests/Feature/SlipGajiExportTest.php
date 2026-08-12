<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlipGajiExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_export_filtered_slip_gaji_to_excel(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('slip-gaji.export-excel', [
            'klinik' => 'Pusat',
            'bulan' => 8,
            'tahun' => 2026,
        ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}
