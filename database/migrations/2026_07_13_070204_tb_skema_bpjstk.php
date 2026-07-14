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
        Schema::create('tb_skema_bpjstk', function (Blueprint $table) {
            $table->id('id_skema_bpjstk');
            $table->double('iuran_jkk')->nullable();
            $table->double('iuran_jkm')->nullable();
            $table->double('pemberi_kerja')->nullable();
            $table->double('tenaga_kerja')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_skema_bpjstk');
    }
};
