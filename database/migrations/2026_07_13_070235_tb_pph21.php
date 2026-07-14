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
        //1. id
        //2. id_karyawan
        //3. identitas(npwp / ktp)
        //4. ptkp

        Schema::create('tb_pph21', function (Blueprint $table) {
            $table->id('id_pph21');
            $table->foreignId('id_karyawan')->constrained('tb_karyawan', 'id_karyawan')->onDelete('cascade');
            $table->string('identitas', 20)->nullable()->comment('npwp/ktp');
            $table->string('ptkp', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_pph21');
    }
};
