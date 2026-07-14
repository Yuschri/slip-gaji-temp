<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_skema_pph21', function (Blueprint $table) {
            $table->id('id_skema_pph21');
            $table->char('golongan', 1); // Menyimpan 'A', 'B', atau 'C'
            $table->unsignedBigInteger('batas_uang'); // Batas minimal uang
            $table->decimal('persen', 5, 4); // Persentase desimal (contoh: 0.3400 atau 0.0225)
            $table->timestamps();

            // Indexing agar pencarian query super cepat karena datanya akan ada ratusan baris
            $table->index(['golongan', 'batas_uang']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_skema_pph21');
    }
};
