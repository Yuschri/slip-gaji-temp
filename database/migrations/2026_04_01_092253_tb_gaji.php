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
        Schema::create('tb_gaji', function (Blueprint $table) {
            $table->id('id_gaji');
            $table->unsignedBigInteger('id_karyawan')->nullable();
            $table->decimal('gaji_pokok', 15, 2);
            $table->decimal('t_pengalaman_kerja', 15, 2)->default(0);
            $table->decimal('t_jabatan', 15, 2)->default(0);
            $table->decimal('t_profesi', 15, 2)->default(0);
            $table->decimal('t_kehadiran', 15, 2)->default(0);
            $table->decimal('t_kinerja', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_gaji');
    }
};
