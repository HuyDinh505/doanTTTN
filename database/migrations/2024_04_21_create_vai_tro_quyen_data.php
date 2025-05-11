<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Thêm quyền cho vai trò Admin
        $adminRoleId = DB::table('vai_tro')->where('ten_vai_tro', 'admin')->value('ma_vai_tro');
        $quyenIds = DB::table('quyen')->pluck('ma_quyen')->toArray();

        if ($adminRoleId && !empty($quyenIds)) {
            $data = [];
            foreach ($quyenIds as $quyenId) {
                $data[] = [
                    'ma_vai_tro' => $adminRoleId,
                    'ma_quyen' => $quyenId,
                ];
            }

            DB::table('vai_tro_quyen')->insert($data);
        }

        // Thêm quyền cho vai trò Quản lý
        $managerRoleId = DB::table('vai_tro')->where('ten_vai_tro', 'quan_ly')->value('ma_vai_tro');
        $managerPermissions = [
            'quan_ly_phim',
            'quan_ly_suat_chieu',
            'quan_ly_phong_chieu',
            'quan_ly_nhan_vien',
            'quan_ly_khach_hang',
            'quan_ly_ve',
            'quan_ly_dich_vu'
        ];

        if ($managerRoleId) {
            $managerQuyenIds = DB::table('quyen')
                ->whereIn('ten_quyen', $managerPermissions)
                ->pluck('ma_quyen')
                ->toArray();

            $data = [];
            foreach ($managerQuyenIds as $quyenId) {
                $data[] = [
                    'ma_vai_tro' => $managerRoleId,
                    'ma_quyen' => $quyenId,
                ];
            }

            if (!empty($data)) {
                DB::table('vai_tro_quyen')->insert($data);
            }
        }

        // Thêm quyền cho vai trò Nhân viên
        $staffRoleId = DB::table('vai_tro')->where('ten_vai_tro', 'nhan_vien')->value('ma_vai_tro');
        $staffPermissions = [
            'quan_ly_ve',
            'quan_ly_dich_vu'
        ];

        if ($staffRoleId) {
            $staffQuyenIds = DB::table('quyen')
                ->whereIn('ten_quyen', $staffPermissions)
                ->pluck('ma_quyen')
                ->toArray();

            $data = [];
            foreach ($staffQuyenIds as $quyenId) {
                $data[] = [
                    'ma_vai_tro' => $staffRoleId,
                    'ma_quyen' => $quyenId,
                ];
            }

            if (!empty($data)) {
                DB::table('vai_tro_quyen')->insert($data);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('vai_tro_quyen')->truncate();
    }
};
