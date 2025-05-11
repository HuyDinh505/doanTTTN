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
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->integer('ma_quan_ly')->nullable()->after('ma_vai_tro');
            $table->integer('ma_rap')->nullable()->after('ma_quan_ly');

            // Thêm foreign key constraints
            $table->foreign('ma_quan_ly')
                ->references('ma_nguoi_dung')
                ->on('nguoi_dung')
                ->onDelete('set null');

            $table->foreign('ma_rap')
                ->references('ma_rap')
                ->on('rap_phim')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            // Xóa foreign key constraints trước
            $table->dropForeign(['ma_quan_ly']);
            $table->dropForeign(['ma_rap']);

            // Xóa cột
            $table->dropColumn(['ma_quan_ly', 'ma_rap']);
        });
    }
};
