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
        Schema::create('tb_bpjstk', function (Blueprint $table) {
            $table->id('id_bpjstk');
            $table->foreignId('id_karyawan')->constrained('tb_karyawan', 'id_karyawan')->onDelete('cascade');
            $table->string('no_referensi', 20)->nullable();
            $table->date('tanggal_kepesertaan')->nullable();
            $table->double('upah_didaftarkan')->nullable();
            $table->double('iuran_jkk')->nullable();
            $table->double('iuran_jkm')->nullable();
            $table->double('pemberi_kerja')->nullable();
            $table->double('tenaga_kerja')->nullable();
            $table->double('total_iuran')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_bpjstk');
    }
};
