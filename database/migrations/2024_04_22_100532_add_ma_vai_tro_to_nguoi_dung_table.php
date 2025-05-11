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
            // Thêm cột ma_vai_tro nếu chưa có
            if (!Schema::hasColumn('nguoi_dung', 'ma_vai_tro')) {
                $table->integer('ma_vai_tro')->nullable()->after('sdt');
            }

            // Thêm foreign key constraint
            $table->foreign('ma_vai_tro')
                ->references('ma_vai_tro')
                ->on('vai_tro')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            // Xóa foreign key constraint
            $table->dropForeign(['ma_vai_tro']);

            // Xóa cột ma_vai_tro
            $table->dropColumn('ma_vai_tro');
        });
    }
};
