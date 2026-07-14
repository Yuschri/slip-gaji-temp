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
        Schema::create('tb_bpjsk', function (Blueprint $table) {
            $table->id('id_bpjsk');
            $table->foreignId('id_karyawan')->constrained('tb_karyawan', 'id_karyawan')->onDelete('cascade');
            $table->integer('beban_bpjsk')->nullable()->comment('jumlah orang');
            $table->string('no_jkn_pekerja', 20)->nullable();
            $table->string('no_jkn_peserta', 20)->nullable();
            $table->string('npp', 20)->nullable();
            $table->double('upah_didaftarkan')->nullable();
            $table->double('premi')->nullable()->comment('nominal uang');
            $table->double('tanggungan_perusahaan')->nullable()->comment('nominal uang');
            $table->double('tanggungan_karyawan')->nullable()->comment('nominal uang');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_bpjsk');
    }
};
