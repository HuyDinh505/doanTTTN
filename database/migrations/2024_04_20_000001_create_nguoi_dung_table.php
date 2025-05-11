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
        Schema::create('nguoi_dung', function (Blueprint $table) {
            $table->integer('ma_nguoi_dung', true);
            $table->string('ho_ten', 100);
            $table->string('email', 100);
            $table->string('mat_khau');
            $table->string('sdt', 15);
            $table->string('vai_tro', 10)->default('User');
            $table->date('ngay_sinh')->nullable();
            $table->dateTime('ngay_tao_nd')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nguoi_dung');
    }
};
