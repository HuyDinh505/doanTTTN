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
        Schema::table('chi_tiet_dv', function (Blueprint $table) {
            $table->foreign(['ma_dv_an_uong'], 'chi_tiet_dv_ibfk_1')->references(['ma_dv_an_uong'])->on('dv_an_uong')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ma_ve'], 'chi_tiet_dv_ibfk_2')->references(['ma_ve'])->on('dat_ve')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chi_tiet_dv', function (Blueprint $table) {
            $table->dropForeign('chi_tiet_dv_ibfk_1');
            $table->dropForeign('chi_tiet_dv_ibfk_2');
        });
    }
};
