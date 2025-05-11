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
        Schema::table('phong_chieu', function (Blueprint $table) {
            $table->foreign(['ma_rap'], 'phong_chieu_ibfk_1')->references(['ma_rap'])->on('rap_phim')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phong_chieu', function (Blueprint $table) {
            $table->dropForeign('phong_chieu_ibfk_1');
        });
    }
};
