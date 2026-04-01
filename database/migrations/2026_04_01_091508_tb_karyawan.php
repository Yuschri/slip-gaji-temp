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
        Schema::create('tb_karyawan', function (Blueprint $table) {
            $table->id('id_karyawan');
            $table->string('nip', 20)->unique();
            $table->string('nama_karyawan');
            $table->date('tanggal_masuk')->nullable();
            $table->foreignId('id_divisi')->constrained('tb_divisi', 'id_divisi')->onDelete('cascade');
            $table->foreignId('id_jabatan')->constrained('tb_jabatan', 'id_jabatan')->onDelete('cascade');
            $table->string('no_wa', 20)->nullable();
            $table->string('nomor_rekening', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_karyawan');
    }
};
