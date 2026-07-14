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
        Schema::create('tb_skema_bpjsk', function (Blueprint $table) {
            $table->id('id_skema_bpjsk');
            $table->double('premi')->nullable()->comment('persentase');
            $table->double('tanggungan_perusahaan')->nullable()->comment('persentase');
            $table->double('tanggungan_karyawan')->nullable()->comment('persentase');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_skema_bpjsk');
    }
};
