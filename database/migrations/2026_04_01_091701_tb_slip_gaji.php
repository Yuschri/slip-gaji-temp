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
        Schema::create('tb_slip_gaji', function (Blueprint $table) {
            $table->id('id_slip');
            $table->unsignedBigInteger('id_karyawan')->nullable();
            $table->string('nama_karyawan');
            $table->date('tanggal_masuk')->nullable();
            $table->string('divisi');
            $table->string('klinik')->nullable();
            $table->string('no_wa');
            $table->string('nomor_rekening');
            $table->decimal('thp', 15, 2)->default(0);
            $table->decimal('gaji_pokok', 15, 2)->default(0);
            $table->decimal('t_jabatan', 15, 2)->default(0);
            $table->decimal('t_profesi', 15, 2)->default(0);
            $table->decimal('t_kehadiran', 15, 2)->default(0);
            $table->decimal('t_kinerja', 15, 2)->default(0);
            $table->decimal('t_hari_raya', 15, 2)->default(0);
            $table->integer('cuti')->default(0);
            $table->decimal('punishment', 15, 2)->default(0);
            $table->decimal('bpjstk', 15, 2)->default(0);
            $table->decimal('bpjs_kesehatan', 15, 2)->default(0);
            $table->decimal('pph_21', 15, 2)->default(0);
            $table->decimal('lembur', 15, 2)->default(0);
            $table->decimal('sedekah_rombongan', 15, 2)->default(0);
            $table->decimal('nominal_transfer', 15, 2)->default(0);
            $table->integer('terlambat')->default(0);
            $table->integer('ijin_pulang_cepat')->default(0);
            $table->integer('ijin_tidak_masuk')->default(0);
            $table->integer('no_check_in_or_out')->default(0);
            $table->integer('no_check_in_and_out')->default(0);
            $table->decimal('lain_lain', 15, 2)->default(0);
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
        Schema::dropIfExists('tb_slip_gaji');
    }
};
