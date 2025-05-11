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
        // Thêm unique constraint và index cho so_ghe trong bảng ghe_ngoi
        Schema::table('ghe_ngoi', function (Blueprint $table) {
            $table->unique(['ma_phong', 'so_ghe'], 'unique_so_ghe_per_phong');
            $table->index('so_ghe', 'idx_so_ghe');
        });

        // Điều chỉnh foreign key constraints cho phong_chieu
        Schema::table('phong_chieu', function (Blueprint $table) {
            // Kiểm tra và xóa foreign key cũ nếu tồn tại
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.TABLE_CONSTRAINTS
                WHERE TABLE_NAME = 'phong_chieu'
                AND CONSTRAINT_TYPE = 'FOREIGN KEY'
                AND CONSTRAINT_NAME LIKE '%ma_rap%'
            ");

            if (!empty($foreignKeys)) {
                $table->dropForeign($foreignKeys[0]->CONSTRAINT_NAME);
            }

            $table->foreign('ma_rap')
                ->references('ma_rap')
                ->on('rap_phim')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        // Điều chỉnh foreign key constraints cho ghe_ngoi
        Schema::table('ghe_ngoi', function (Blueprint $table) {
            // Kiểm tra và xóa foreign key cũ nếu tồn tại
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.TABLE_CONSTRAINTS
                WHERE TABLE_NAME = 'ghe_ngoi'
                AND CONSTRAINT_TYPE = 'FOREIGN KEY'
                AND CONSTRAINT_NAME LIKE '%ma_phong%'
            ");

            if (!empty($foreignKeys)) {
                $table->dropForeign($foreignKeys[0]->CONSTRAINT_NAME);
            }

            $table->foreign('ma_phong')
                ->references('ma_phong')
                ->on('phong_chieu')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        // Điều chỉnh foreign key constraints cho suat_chieu
        Schema::table('suat_chieu', function (Blueprint $table) {
            // Kiểm tra và xóa foreign key cũ nếu tồn tại
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.TABLE_CONSTRAINTS
                WHERE TABLE_NAME = 'suat_chieu'
                AND CONSTRAINT_TYPE = 'FOREIGN KEY'
                AND (CONSTRAINT_NAME LIKE '%ma_phong%' OR CONSTRAINT_NAME LIKE '%ma_phim%')
            ");

            foreach ($foreignKeys as $foreignKey) {
                $table->dropForeign($foreignKey->CONSTRAINT_NAME);
            }

            $table->foreign('ma_phong')
                ->references('ma_phong')
                ->on('phong_chieu')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('ma_phim')
                ->references('ma_phim')
                ->on('phim')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Xóa unique constraint và index cho so_ghe trong bảng ghe_ngoi
        Schema::table('ghe_ngoi', function (Blueprint $table) {
            $table->dropUnique('unique_so_ghe_per_phong');
            $table->dropIndex('idx_so_ghe');
        });

        // Khôi phục foreign key constraints cho phong_chieu
        Schema::table('phong_chieu', function (Blueprint $table) {
            $table->dropForeign(['ma_rap']);
            $table->foreign('ma_rap')
                ->references('ma_rap')
                ->on('rap_phim')
                ->onUpdate('no action')
                ->onDelete('no action');
        });

        // Khôi phục foreign key constraints cho ghe_ngoi
        Schema::table('ghe_ngoi', function (Blueprint $table) {
            $table->dropForeign(['ma_phong']);
            $table->foreign('ma_phong')
                ->references('ma_phong')
                ->on('phong_chieu')
                ->onUpdate('no action')
                ->onDelete('no action');
        });

        // Khôi phục foreign key constraints cho suat_chieu
        Schema::table('suat_chieu', function (Blueprint $table) {
            $table->dropForeign(['ma_phong']);
            $table->dropForeign(['ma_phim']);
            $table->foreign('ma_phong')
                ->references('ma_phong')
                ->on('phong_chieu')
                ->onUpdate('no action')
                ->onDelete('no action');
            $table->foreign('ma_phim')
                ->references('ma_phim')
                ->on('phim')
                ->onUpdate('no action')
                ->onDelete('no action');
        });
    }
};
