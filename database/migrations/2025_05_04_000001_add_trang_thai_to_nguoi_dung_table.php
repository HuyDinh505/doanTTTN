<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->string('trang_thai')->default('hoat_dong')->after('ngay_tao_nd');
        });
    }

    public function down()
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->dropColumn('trang_thai');
        });
    }
};
