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
        Schema::create('rap_phim', function (Blueprint $table) {
            $table->integer('ma_rap', true);
            $table->string('ten_rap', 50);
            $table->string('dia_chi');
            $table->text('dia_chi_map');
            $table->dateTime('ngay_tao_rap')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rap_phim');
    }
};
