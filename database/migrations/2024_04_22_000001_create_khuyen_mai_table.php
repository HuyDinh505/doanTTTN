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
        Schema::create('khuyen_mai', function (Blueprint $table) {
            $table->integer('ma_khuyen_mai', true);
            $table->string('ten_khuyen_mai', 100);
            $table->text('mo_ta');
            $table->decimal('phan_tram_giam', 5, 2);
            $table->date('ngay_bat_dau');
            $table->date('ngay_ket_thuc');
            $table->string('ma_code', 20)->unique();
            $table->integer('so_luong');
            $table->dateTime('ngay_tao_km')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('khuyen_mai');
    }
};
