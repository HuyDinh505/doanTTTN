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
        Schema::create('phim_loai', function (Blueprint $table) {
            $table->integer('ma_phim');
            $table->integer('ma_loai');

            // Thêm foreign key cho ma_phim
            $table->foreign('ma_phim')
                ->references('ma_phim')
                ->on('phim')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // Thêm foreign key cho ma_loai
            $table->foreign('ma_loai')
                ->references('ma_loai')
                ->on('loai_phim')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // Thêm primary key
            $table->primary(['ma_phim', 'ma_loai']);

            // Thêm index cho cả hai cột để tăng hiệu suất truy vấn
            $table->index('ma_phim');
            $table->index('ma_loai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phim_loai');
    }
};
