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
        Schema::create('dich_vu_an_uong', function (Blueprint $table) {
            $table->integer('ma_dv_an_uong', true);
            $table->string('ten_dv_an_uong', 50);
            $table->decimal('gia_tien', 10);
            $table->string('loai', 50);
            $table->string('anh_dv')->nullable();
            $table->dateTime('ngay_tao_dv')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dich_vu_an_uong');
    }
};
