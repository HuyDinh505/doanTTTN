<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\VaiTro;

return new class extends Migration
{
    public function up()
    {
        DB::table('vai_tro')->insert([
            [
                'ten_vai_tro' => VaiTro::ADMIN,
                'mo_ta' => 'Quản trị viên hệ thống'
            ],
            [
                'ten_vai_tro' => VaiTro::QUAN_LY,
                'mo_ta' => 'Quản lý rạp phim'
            ],
            [
                'ten_vai_tro' => VaiTro::NHAN_VIEN,
                'mo_ta' => 'Nhân viên rạp phim'
            ],
            [
                'ten_vai_tro' => VaiTro::KHACH_HANG,
                'mo_ta' => 'Khách hàng'
            ]
        ]);
    }

    public function down()
    {
        DB::table('vai_tro')->whereIn('ten_vai_tro', [
            VaiTro::ADMIN,
            VaiTro::QUAN_LY,
            VaiTro::NHAN_VIEN,
            VaiTro::KHACH_HANG
        ])->delete();
    }
};
