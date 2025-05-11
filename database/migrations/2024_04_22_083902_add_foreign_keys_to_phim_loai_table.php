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
        Schema::table('phim_loai', function (Blueprint $table) {
            $table->foreign(['ma_phim'], 'phim_loai_ibfk_1')->references(['ma_phim'])->on('phim')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['ma_loai'], 'phim_loai_ibfk_2')->references(['ma_loai'])->on('loai_phim')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phim_loai', function (Blueprint $table) {
            $table->dropForeign('phim_loai_ibfk_1');
            $table->dropForeign('phim_loai_ibfk_2');
        });
    }
};
