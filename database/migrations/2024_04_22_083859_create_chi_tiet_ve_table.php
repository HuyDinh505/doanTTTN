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
        Schema::create('chi_tiet_ve', function (Blueprint $table) {
            $table->string('ma_ve', 50)->index('ma_ve');
            $table->integer('ma_ghe')->index('ma_ghe');

            $table->primary(['ma_ve', 'ma_ghe']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_ve');
    }
};
