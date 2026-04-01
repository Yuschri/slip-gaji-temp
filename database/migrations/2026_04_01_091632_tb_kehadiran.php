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
        Schema::create('tb_kehadiran', function (Blueprint $table) {
            $table->id('id_kehadiran');
            $table->foreignId('id_karyawan')->constrained('tb_karyawan', 'id_karyawan')->onDelete('cascade');
            $table->integer('cuti')->default(0); //satuan kali
            $table->integer('lembur')->default(0); //satuan kali
            $table->integer('lembur_jam')->default(0); //satuan jam
            $table->integer('terlambat')->default(0); //satuan kali
            $table->integer('terlambat_menit')->default(0); //satuan menit
            $table->integer('ijin_pulang_cepat')->default(0); //satuan kali
            $table->integer('ijin_tidak_masuk')->default(0); //contoh 1 (x)
            $table->integer('no_check_in_or_out')->default(0); //contoh 1 (x)
            $table->integer('no_check_in_and_out')->default(0); //contoh 1 (x)
            $table->integer('bulan');
            $table->year('tahun');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_kehadiran');
    }
};
