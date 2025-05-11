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
            // Drop existing primary key
            $table->dropPrimary(['ma_ve', 'ma_loai_ve']);

            // Add auto-incrementing id column
            $table->id()->first();

            // Add unique constraint for ma_ve and ma_loai_ve combination
            $table->unique(['ma_ve', 'ma_loai_ve']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ve_dat', function (Blueprint $table) {
            // Drop the id column
            $table->dropColumn('id');

            // Drop the unique constraint
            $table->dropUnique(['ma_ve', 'ma_loai_ve']);

            // Restore the original primary key
            $table->primary(['ma_ve', 'ma_loai_ve']);
        });
    }
};
