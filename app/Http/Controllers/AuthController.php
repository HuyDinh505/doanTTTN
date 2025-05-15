<?php

namespace App\Http\Controllers;

use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use App\Models\VaiTro;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                'hoTen' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:nguoi_dung,email'],
                'matKhau' => ['required', 'string', 'min:8'],
                'matKhau_confirmation' => ['required', 'same:matKhau'],
                'soDienThoai' => ['required', 'string', 'max:20'],
                'anhNguoiDung' => ['nullable', 'image', 'max:2048'], // 2MB max
            ]);

            // Handle avatar upload if provided
            $anhNguoiDung = 'default-avatar.jpg';
            if ($request->hasFile('anhNguoiDung')) {
                $file = $request->file('anhNguoiDung');
                $anhNguoiDung = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/avatars', $anhNguoiDung);
            }

            $nguoiDung = NguoiDung::create([
                'ho_ten' => $request->hoTen,
                'email' => $request->email,
                'mat_khau' => Hash::make($request->matKhau),
                'sdt' => $request->soDienThoai,
                // 'vai_tro' => VaiTro::KHACH_HANG,
                'ma_vai_tro' => 4,
                'anh_nguoi_dung' => $anhNguoiDung,
                'ngay_sinh' => '2000-01-01',
                'ngay_tao_nd' => now(),
            ]);

            $token = $nguoiDung->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'nguoiDung' => $nguoiDung,
                'token' => $token,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực dữ liệu',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi đăng ký',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateAvatar(Request $request)
    {
        try {
            $request->validate([
                'anhNguoiDung' => ['required', 'image', 'max:2048'], // 2MB max
            ]);

            $nguoiDung = $request->user();

            // Delete old avatar if it's not the default
            if ($nguoiDung->anh_nguoi_dung !== 'default-avatar.jpg') {
                Storage::delete('public/avatars/' . $nguoiDung->anh_nguoi_dung);
            }

            // Upload new avatar
            $file = $request->file('anhNguoiDung');
            $anhNguoiDung = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/avatars', $anhNguoiDung);

            // Update user avatar
            $nguoiDung->update([
                'anh_nguoi_dung' => $anhNguoiDung
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật ảnh đại diện thành công',
                'anh_nguoi_dung' => $anhNguoiDung
            ]);
        } catch (\Exception $e) {
            Log::error('Avatar update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi cập nhật ảnh đại diện',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'matKhau' => 'required'
            ]);

            $nguoiDung = NguoiDung::where('email', $request->email)->first();

            if (!$nguoiDung || !Hash::check($request->matKhau, $nguoiDung->mat_khau)) {
                throw ValidationException::withMessages([
                    'email' => ['Thông tin đăng nhập không chính xác'],
                ]);
            }

            $token = $nguoiDung->createToken('auth_token')->plainTextToken;

            return response()->json([
                'token' => $token,
                'nguoiDung' => $nguoiDung,
                'role' => $nguoiDung->vai_tro
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Đã xảy ra lỗi khi đăng nhập',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Đăng xuất thành công']);
    }

    public function user(Request $request)
    {
        return response()->json([
            'nguoi_dung' => $request->user(),
            'vai_tro' => $request->user()->vaiTro
        ]);
    }

    public function adminLogin(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'matKhau' => 'required'
            ]);

            $nguoiDung = NguoiDung::where('email', $request->email)
                                 ->where('vai_tro', VaiTro::ADMIN)
                                 ->first();

            if (!$nguoiDung || !Hash::check($request->matKhau, $nguoiDung->mat_khau)) {
                throw ValidationException::withMessages([
                    'email' => ['Thông tin đăng nhập không chính xác hoặc bạn không có quyền admin'],
                ]);
            }

            $token = $nguoiDung->createToken('admin_token')->plainTextToken;

            return response()->json([
                'token' => $token,
                'admin' => $nguoiDung,
                'role' => $nguoiDung->vai_tro
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Đã xảy ra lỗi khi đăng nhập',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
