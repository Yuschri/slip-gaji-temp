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
            $table->string('bulan');
            $table->string('tahun');
            $table->foreignId('id_karyawan')->constrained('tb_karyawan', 'id_karyawan')->onDelete('cascade');
            $table->decimal('gaji_pokok', 15, 2)->default(0);
            $table->decimal('t_pengalaman_kerja', 15, 2)->default(0);
            $table->decimal('t_jabatan', 15, 2)->default(0);
            $table->decimal('t_profesi', 15, 2)->default(0);
            $table->decimal('t_operasional', 15, 2)->default(0);
            $table->decimal('t_kehadiran', 15, 2)->default(0);
            $table->decimal('t_kinerja', 15, 2)->default(0);
            $table->decimal('t_hari_raya', 15, 2)->default(0);
            $table->decimal('fee_beautician', 15, 2)->default(0);
            $table->decimal('nominal_lembur', 15, 2)->default(0);
            $table->decimal('pendapatan_lainnya', 15, 2)->default(0);
            $table->decimal('penyesuaian_gaji_lalu', 15, 2)->default(0);
            $table->decimal('subtotal_penerimaan', 15, 2)->default(0);
            $table->decimal('bpjstk_perusahaan', 15, 2)->default(0);
            $table->decimal('bpjsk_perusahaan', 15, 2)->default(0);
            $table->decimal('punishment', 15, 2)->default(0);
            $table->decimal('sedekah_rombongan', 15, 2)->default(0);
            $table->decimal('potongan_lainnya', 15, 2)->default(0);
            $table->decimal('bpjstk_karyawan', 15, 2)->default(0);
            $table->decimal('bpjsk_karyawan', 15, 2)->default(0);
            $table->decimal('pph21', 15, 2)->default(0);
            $table->integer('lembur_kali')->default(0);
            $table->integer('lembur_menit')->default(0);
            $table->integer('terlambat_kali')->default(0);
            $table->integer('terlambat_menit')->default(0);
            $table->integer('ijin_pulang_awal')->default(0);
            $table->integer('ijin_tidak_masuk')->default(0);
            $table->integer('no_checkin_or_checkout')->default(0);
            $table->integer('no_checkin_and_checkout')->default(0);
            $table->integer('cuti')->default(0);
            $table->integer('kehadiran_lainnya')->default(0);
            $table->decimal('total_diterima', 15, 2)->default(0);
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
