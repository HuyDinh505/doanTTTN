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
        Schema::create('ve_dat', function (Blueprint $table) {
            $table->string('ma_ve', 50)->index('ma_ve');
            $table->integer('ma_loai_ve')->index('ma_loai_ve');
            $table->integer('so_luong');
            $table->decimal('gia_tien', 10);

            $table->primary(['ma_ve', 'ma_loai_ve']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ve_dat');
    }
};
