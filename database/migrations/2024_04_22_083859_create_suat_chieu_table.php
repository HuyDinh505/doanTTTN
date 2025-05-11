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
        Schema::create('suat_chieu', function (Blueprint $table) {
            $table->integer('ma_suat_chieu', true);
            $table->integer('ma_phim')->index('ma_phim');
            $table->integer('ma_phong')->index('ma_phong');
            $table->time('thoi_gian_bd');
            $table->date('ngay_chieu');
            $table->dateTime('ngay_tao_sc')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suat_chieu');
    }
};
