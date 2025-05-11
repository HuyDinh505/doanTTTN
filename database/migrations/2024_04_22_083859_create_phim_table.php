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
        Schema::create('phim', function (Blueprint $table) {
            $table->integer('ma_phim', true);
            $table->string('ten_phim', 100);
            $table->text('mo_ta');
            $table->integer('thoi_luong');
            $table->date('ngay_phat_hanh');
            $table->string('anh');
            $table->string('hinh_thuc_chieu', 50);
            $table->string('dao_dien');
            $table->string('dien_vien');
            $table->string('trang_thai', 50);
            $table->string('do_tuoi', 50);
            $table->string('quoc_gia', 100);
            $table->dateTime('ngay_tao_phim')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phim');
    }
};
