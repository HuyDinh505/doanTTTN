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
        Schema::table('suat_chieu', function (Blueprint $table) {
            $table->foreign(['ma_phong'], 'suat_chieu_ibfk_1')->references(['ma_phong'])->on('phong_chieu')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['ma_phim'], 'suat_chieu_ibfk_2')->references(['ma_phim'])->on('phim')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suat_chieu', function (Blueprint $table) {
            $table->dropForeign('suat_chieu_ibfk_1');
            $table->dropForeign('suat_chieu_ibfk_2');
        });
    }
};
