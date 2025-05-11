<?php

namespace Database\Seeders;

use App\Models\NguoiDung;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles first
        $this->call([
            QuyenSeeder::class,
            VaiTroSeeder::class,
        ]);

        // Create test users with different roles
        NguoiDung::create([
            'ho_ten' => 'Admin User',
            'email' => 'admin@example.com',
            'mat_khau' => Hash::make('password'),
            'sdt' => '0123456789',
            'ma_vai_tro' => 1, // Admin
            'ngay_sinh' => now(),
            'ngay_tao_nd' => now()
        ]);

        NguoiDung::create([
            'ho_ten' => 'Quản lý',
            'email' => 'quanly@example.com',
            'mat_khau' => Hash::make('password'),
            'sdt' => '0123456788',
            'ma_vai_tro' => 2, // Quản lý
            'ngay_sinh' => now(),
            'ngay_tao_nd' => now()
        ]);

        NguoiDung::create([
            'ho_ten' => 'Nhân viên',
            'email' => 'nhanvien@example.com',
            'mat_khau' => Hash::make('password'),
            'sdt' => '0123456787',
            'ma_vai_tro' => 3, // Nhân viên
            'ngay_sinh' => now(),
            'ngay_tao_nd' => now()
        ]);

        NguoiDung::create([
            'ho_ten' => 'Khách hàng',
            'email' => 'khachhang@example.com',
            'mat_khau' => Hash::make('password'),
            'sdt' => '0123456786',
            'ma_vai_tro' => 4, // Khách hàng
            'ngay_sinh' => now(),
            'ngay_tao_nd' => now()
        ]);
    }
}
