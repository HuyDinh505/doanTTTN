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
        Schema::create('phong_chieu', function (Blueprint $table) {
            $table->integer('ma_phong', true);
            $table->integer('ma_rap')->index('ma_rap');
            $table->string('ten_phong', 50);
            $table->string('loai_phong', 50);
            $table->integer('so_cot');
            $table->integer('so_hang');
            $table->dateTime('Ngay_tao_phong')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phong_chieu');
    }
};
