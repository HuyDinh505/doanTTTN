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
        Schema::create('loai_ve', function (Blueprint $table) {
            $table->integer('ma_loai_ve', true);
            $table->string('ten_loai_ve', 50);
            $table->decimal('gia_ve', 10);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loai_ve');
    }
};
