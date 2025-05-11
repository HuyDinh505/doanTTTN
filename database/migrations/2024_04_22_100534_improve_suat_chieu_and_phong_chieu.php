<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cải thiện bảng suat_chieu
        Schema::table('suat_chieu', function (Blueprint $table) {
            // Thêm unique constraint cho cặp ma_phong, thoi_gian_bd, ngay_chieu
            $table->unique(['ma_phong', 'thoi_gian_bd', 'ngay_chieu'], 'unique_suat_chieu_per_phong');

            // Thêm index cho thoi_gian_bd và ngay_chieu
            $table->index('thoi_gian_bd', 'idx_thoi_gian_bd');
            $table->index('ngay_chieu', 'idx_ngay_chieu');
        });

        // Cải thiện bảng phong_chieu
        Schema::table('phong_chieu', function (Blueprint $table) {
            // Thêm ràng buộc check cho so_cot và so_hang
            DB::statement('ALTER TABLE phong_chieu ADD CONSTRAINT check_so_cot CHECK (so_cot > 0)');
            DB::statement('ALTER TABLE phong_chieu ADD CONSTRAINT check_so_hang CHECK (so_hang > 0)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Xóa các cải thiện của bảng suat_chieu
        Schema::table('suat_chieu', function (Blueprint $table) {
            $table->dropUnique('unique_suat_chieu_per_phong');
            $table->dropIndex('idx_thoi_gian_bd');
            $table->dropIndex('idx_ngay_chieu');
        });

        // Xóa các cải thiện của bảng phong_chieu
        Schema::table('phong_chieu', function (Blueprint $table) {
            DB::statement('ALTER TABLE phong_chieu DROP CONSTRAINT check_so_cot');
            DB::statement('ALTER TABLE phong_chieu DROP CONSTRAINT check_so_hang');
        });
    }
};
