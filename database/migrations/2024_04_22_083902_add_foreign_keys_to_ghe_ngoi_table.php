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
        Schema::table('ghe_ngoi', function (Blueprint $table) {
            $table->foreign(['ma_phong'], 'ghe_ngoi_ibfk_1')->references(['ma_phong'])->on('phong_chieu')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ghe_ngoi', function (Blueprint $table) {
            $table->dropForeign('ghe_ngoi_ibfk_1');
        });
    }
};
