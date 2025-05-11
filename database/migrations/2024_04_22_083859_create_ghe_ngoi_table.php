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
        Schema::create('ghe_ngoi', function (Blueprint $table) {
            $table->integer('ma_ghe', true);
            $table->integer('ma_phong')->index('ma_phong');
            $table->string('so_ghe', 10);
            $table->dateTime('ngay_tao_ghe')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ghe_ngoi');
    }
};
