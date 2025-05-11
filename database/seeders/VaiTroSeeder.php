<?php

namespace Database\Seeders;

use App\Models\VaiTro;
use App\Models\Quyen;
use Illuminate\Database\Seeder;

class VaiTroSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo vai trò
        $admin = VaiTro::create([
            'ten_vai_tro' => VaiTro::ADMIN,
            'mo_ta' => 'Quản trị viên hệ thống'
        ]);

        $quanLy = VaiTro::create([
            'ten_vai_tro' => VaiTro::QUAN_LY,
            'mo_ta' => 'Quản lý rạp phim'
        ]);

        $nhanVien = VaiTro::create([
            'ten_vai_tro' => VaiTro::NHAN_VIEN,
            'mo_ta' => 'Nhân viên rạp phim'
        ]);

        $khachHang = VaiTro::create([
            'ten_vai_tro' => VaiTro::KHACH_HANG,
            'mo_ta' => 'Khách hàng'
        ]);

        // Gán quyền cho vai trò
        $admin->quyens()->attach(Quyen::all());

        $quanLy->quyens()->attach(
            Quyen::whereIn('ten_quyen', [
                'quan_ly_phim',
                'quan_ly_rap',
                'quan_ly_suat_chieu',
                'quan_ly_ve',
                'xem_thong_ke'
            ])->get()
        );

        $nhanVien->quyens()->attach(
            Quyen::whereIn('ten_quyen', [
                'quan_ly_ve',
                'xem_thong_ke'
            ])->get()
        );

        $khachHang->quyens()->attach(
            Quyen::whereIn('ten_quyen', [
                'dat_ve'
            ])->get()
        );
    }
}
