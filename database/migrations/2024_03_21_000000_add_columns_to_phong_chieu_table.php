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
            if (!Schema::hasColumn('phong_chieu', 'so_hang')) {
                $table->integer('so_hang')->after('loai_phong');
            }
            if (!Schema::hasColumn('phong_chieu', 'so_cot')) {
                $table->integer('so_cot')->after('so_hang');
            }
            if (!Schema::hasColumn('phong_chieu', 'so_ghe')) {
                $table->integer('so_ghe')->after('so_cot')->default(0);
            }
            if (!Schema::hasColumn('phong_chieu', 'trang_thai')) {
                $table->boolean('trang_thai')->after('so_ghe')->default(true);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phong_chieu', function (Blueprint $table) {
            $table->dropColumn(['so_hang', 'so_cot', 'so_ghe', 'trang_thai']);
        });
    }
};