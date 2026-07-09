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
        Schema::create('tb_potongan', function (Blueprint $table) {
            $table->id('id_potongan');
            $table->unsignedBigInteger('id_karyawan')->nullable();
            $table->decimal('potongan_sedekah_rombongan', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_potongan');
    }
};
