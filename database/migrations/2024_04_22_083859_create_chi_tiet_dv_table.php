<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chi_tiet_dv', function (Blueprint $table) {
            $table->string('ma_ve', 50)->index('ma_ve');
            $table->integer('ma_dv_an_uong')->index('ma_dv_an_uong');
            $table->integer('so_luong');
            $table->decimal('tong_gia_tien', 10);

            $table->primary(['ma_ve', 'ma_dv_an_uong']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_dv');
    }
};
