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
        Schema::table('chi_tiet_ve', function (Blueprint $table) {
            $table->foreign(['ma_ve'], 'chi_tiet_ve_ibfk_1')->references(['ma_ve'])->on('dat_ve')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['ma_ghe'], 'chi_tiet_ve_ibfk_2')->references(['ma_ghe'])->on('ghe_ngoi')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chi_tiet_ve', function (Blueprint $table) {
            $table->dropForeign('chi_tiet_ve_ibfk_1');
            $table->dropForeign('chi_tiet_ve_ibfk_2');
        });
    }
};
