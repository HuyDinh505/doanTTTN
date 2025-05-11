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
        Schema::table('ve_dat', function (Blueprint $table) {
            $table->foreign(['ma_ve'], 've_dat_ibfk_1')->references(['ma_ve'])->on('dat_ve')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ma_loai_ve'], 've_dat_ibfk_2')->references(['ma_loai_ve'])->on('loai_ve')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ve_dat', function (Blueprint $table) {
            $table->dropForeign('ve_dat_ibfk_1');
            $table->dropForeign('ve_dat_ibfk_2');
        });
    }
};
