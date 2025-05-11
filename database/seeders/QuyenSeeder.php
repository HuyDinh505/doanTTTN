<?php

namespace Database\Seeders;

use App\Models\Quyen;
use Illuminate\Database\Seeder;

class QuyenSeeder extends Seeder
{
    public function run(): void
    {
        $quyens = [
            ['ten_quyen' => 'quan_ly_nguoi_dung', 'mo_ta' => 'Quản lý người dùng'],
            ['ten_quyen' => 'quan_ly_phim', 'mo_ta' => 'Quản lý phim'],
            ['ten_quyen' => 'quan_ly_rap', 'mo_ta' => 'Quản lý rạp'],
            ['ten_quyen' => 'quan_ly_suat_chieu', 'mo_ta' => 'Quản lý suất chiếu'],
            ['ten_quyen' => 'quan_ly_ve', 'mo_ta' => 'Quản lý vé'],
            ['ten_quyen' => 'dat_ve', 'mo_ta' => 'Đặt vé xem phim'],
            ['ten_quyen' => 'xem_thong_ke', 'mo_ta' => 'Xem thống kê'],
        ];

        foreach ($quyens as $quyen) {
            Quyen::create($quyen);
        }
    }
}
