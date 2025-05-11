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
        Schema::table('dat_ve', function (Blueprint $table) {
            $table->foreign(['ma_suat_chieu'], 'dat_ve_ibfk_1')->references(['ma_suat_chieu'])->on('suat_chieu')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['ma_nguoi_dung'], 'dat_ve_ibfk_2')->references(['ma_nguoi_dung'])->on('nguoi_dung')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dat_ve', function (Blueprint $table) {
            $table->dropForeign('dat_ve_ibfk_1');
            $table->dropForeign('dat_ve_ibfk_2');
        });
    }
};
