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
            $table->foreignId('id_karyawan')->constrained('tb_karyawan', 'id_karyawan')->onDelete('cascade');
            $table->decimal('thp', 15, 2)->default(0);
            $table->decimal('gaji_pokok', 15, 2)->default(0);
            $table->decimal('t_pengalaman_kerja', 15, 2)->default(0);
            $table->decimal('t_jabatan', 15, 2)->default(0);
            $table->decimal('t_profesi', 15, 2)->default(0);
            $table->decimal('t_operasional', 15, 2)->default(0);
            $table->decimal('t_kehadiran', 15, 2)->default(0);
            $table->decimal('t_kinerja', 15, 2)->default(0);
            $table->decimal('t_hari_raya', 15, 2)->default(0);
            $table->decimal('prosentase_gaji', 15, 2)->default(0);
            $table->integer('jumlah_hari_gabung')->default(0);
            $table->decimal('nominal_lembur', 15, 2)->default(0);
            $table->decimal('fee_beautician', 15, 2)->default(0);
            $table->decimal('lain_lain', 15, 2)->default(0);
            $table->decimal('punishment', 15, 2)->default(0);
            $table->decimal('bpjs_tk', 15, 2)->default(0);
            $table->decimal('bpjs_kesehatan', 15, 2)->default(0);
            $table->decimal('pph_21', 15, 2)->default(0);
            $table->decimal('potongan_bpjs_tk', 15, 2)->default(0);
            $table->decimal('potongan_bpjs_kesehatan', 15, 2)->default(0);
            $table->decimal('potongan_pph_21', 15, 2)->default(0);
            $table->decimal('sedekah_rombongan', 15, 2)->default(0);
            $table->decimal('potongan_lainnya', 15, 2)->default(0);
            $table->decimal('nominal_transfer', 15, 2)->default(0);
            $table->foreignId('id_kehadiran')->constrained('tb_kehadiran', 'id_kehadiran')->onDelete('cascade');
            $table->integer('kehadiran_lainnya')->default(0);
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
