<?php

namespace Database\Seeders;

use App\Models\Quyen;
use App\Models\VaiTro;
use Illuminate\Database\Seeder;

class VaiTroQuyenSeeder extends Seeder
{
    public function run()
    {
        // Tạo các vai trò
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

        // Tạo các quyền
        $quyens = [
            Quyen::XEM_PHIM => 'Xem danh sách phim',
            Quyen::THEM_PHIM => 'Thêm phim mới',
            Quyen::SUA_PHIM => 'Sửa thông tin phim',
            Quyen::XOA_PHIM => 'Xóa phim',
            Quyen::XEM_NGUOI_DUNG => 'Xem danh sách người dùng',
            Quyen::THEM_NGUOI_DUNG => 'Thêm người dùng mới',
            Quyen::SUA_NGUOI_DUNG => 'Sửa thông tin người dùng',
            Quyen::XOA_NGUOI_DUNG => 'Xóa người dùng',
            Quyen::XEM_RAP => 'Xem danh sách rạp',
            Quyen::THEM_RAP => 'Thêm rạp mới',
            Quyen::SUA_RAP => 'Sửa thông tin rạp',
            Quyen::XOA_RAP => 'Xóa rạp',
            Quyen::XEM_THONG_KE => 'Xem thống kê',
            'xem_phong_chieu' => 'Xem thông tin phòng chiếu'
        ];

        foreach ($quyens as $tenQuyen => $moTa) {
            Quyen::create([
                'ten_quyen' => $tenQuyen,
                'mo_ta' => $moTa
            ]);
        }

        // Gán quyền cho vai trò Admin
        $admin->quyens()->attach(Quyen::all()->pluck('ma_quyen'));

        // Gán quyền cho vai trò Quản lý
        $quanLy->quyens()->attach([
            Quyen::where('ten_quyen', Quyen::XEM_PHIM)->first()->ma_quyen,
            Quyen::where('ten_quyen', Quyen::THEM_PHIM)->first()->ma_quyen,
            Quyen::where('ten_quyen', Quyen::SUA_PHIM)->first()->ma_quyen,
            Quyen::where('ten_quyen', Quyen::XEM_NGUOI_DUNG)->first()->ma_quyen,
            Quyen::where('ten_quyen', Quyen::XEM_RAP)->first()->ma_quyen,
            Quyen::where('ten_quyen', Quyen::THEM_RAP)->first()->ma_quyen,
            Quyen::where('ten_quyen', Quyen::SUA_RAP)->first()->ma_quyen,
            Quyen::where('ten_quyen', Quyen::XEM_THONG_KE)->first()->ma_quyen
        ]);

        // Gán quyền cho vai trò Nhân viên
        $nhanVien->quyens()->attach([
            Quyen::where('ten_quyen', Quyen::XEM_PHIM)->first()->ma_quyen,
            Quyen::where('ten_quyen', Quyen::XEM_NGUOI_DUNG)->first()->ma_quyen,
            Quyen::where('ten_quyen', Quyen::XEM_RAP)->first()->ma_quyen
        ]);
    }
}
