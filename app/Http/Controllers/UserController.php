<?php

namespace App\Http\Controllers;

use App\Models\NguoiDung;
use App\Models\VaiTro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = NguoiDung::with(['vaiTro', 'rapPhim', 'quanLy'])->get();
            return response()->json($users);
        } catch (\Exception $e) {
            Log::error('Error fetching users: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi lấy danh sách người dùng'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ho_ten' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:nguoi_dung',
                'mat_khau' => 'required|string|min:6',
                'sdt' => 'required|string|max:20',
                'ma_vai_tro' => 'required|exists:vai_tro,ma_vai_tro',
                'ma_quan_ly' => 'nullable|exists:nguoi_dung,ma_nguoi_dung',
                'ma_rap' => 'nullable|exists:rap_phim,ma_rap',
                'ngay_sinh' => 'required|date',
                'anh_nguoi_dung' => 'nullable|string',
                'trang_thai' => 'required|in:hoat_dong,khoa'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $user = NguoiDung::create([
                'ho_ten' => $request->ho_ten,
                'email' => $request->email,
                'mat_khau' => Hash::make($request->mat_khau),
                'sdt' => $request->sdt,
                'ma_vai_tro' => $request->ma_vai_tro,
                'ma_quan_ly' => $request->ma_quan_ly,
                'ma_rap' => $request->ma_rap,
                'ngay_sinh' => $request->ngay_sinh,
                'anh_nguoi_dung' => $request->anh_nguoi_dung ?? 'default-avatar.jpg',
                'ngay_tao_nd' => now(),
                'trang_thai' => $request->trang_thai
            ]);

            return response()->json([
                'message' => 'Thêm người dùng thành công',
                'data' => $user
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi thêm người dùng'], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = NguoiDung::with(['vaiTro', 'rapPhim', 'quanLy'])->find($id);

            if (!$user) {
                return response()->json(['message' => 'Người dùng không tồn tại'], 404);
            }

            return response()->json($user);
        } catch (\Exception $e) {
            Log::error('Error fetching user: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi lấy thông tin người dùng'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = NguoiDung::find($id);

            if (!$user) {
                return response()->json(['message' => 'Người dùng không tồn tại'], 404);
            }

            $validator = Validator::make($request->all(), [
                'ho_ten' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email|max:255|unique:nguoi_dung,email,' . $id . ',ma_nguoi_dung',
                'mat_khau' => 'sometimes|string|min:6',
                'sdt' => 'sometimes|string|max:20',
                'ma_vai_tro' => 'sometimes|exists:vai_tro,ma_vai_tro',
                'ma_quan_ly' => 'nullable|exists:nguoi_dung,ma_nguoi_dung',
                'ma_rap' => 'nullable|exists:rap_phim,ma_rap',
                'ngay_sinh' => 'sometimes|date',
                'anh_nguoi_dung' => 'nullable|string',
                'trang_thai' => 'sometimes|in:hoat_dong,khoa'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $updateData = $request->all();
            if (isset($updateData['mat_khau'])) {
                $updateData['mat_khau'] = Hash::make($updateData['mat_khau']);
            }

            $user->update($updateData);

            return response()->json([
                'message' => 'Cập nhật người dùng thành công',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi cập nhật người dùng'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = NguoiDung::find($id);

            if (!$user) {
                return response()->json(['message' => 'Người dùng không tồn tại'], 404);
            }

            // Kiểm tra xem người dùng có phải là admin không
            if ($user->isAdmin()) {
                return response()->json(['message' => 'Không thể xóa tài khoản admin'], 403);
            }

            $user->delete();

            return response()->json(['message' => 'Xóa người dùng thành công']);
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi xóa người dùng'], 500);
        }
    }

    public function getNhanVien()
    {
        try {
            $currentUser = Auth::user();
            // Lấy id vai trò nhân viên từ bảng vai_tro
            $roleStaff = \App\Models\VaiTro::where('ten_vai_tro', 'nhan_vien')->value('ma_vai_tro');
            $nhanVien = NguoiDung::with(['vaiTro', 'rapPhim', 'quanLy'])
                ->where('ma_vai_tro', $roleStaff)
                ->where('ma_quan_ly', $currentUser->ma_nguoi_dung)
                ->get();
            return response()->json($nhanVien);
        } catch (\Exception $e) {
            Log::error('Error fetching staff: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi lấy danh sách nhân viên'], 500);
        }
    }

    public function getQuanLy()
    {
        try {
            // Lấy id vai trò quản lý từ bảng vai_tro
            $roleManager = \App\Models\VaiTro::where('ten_vai_tro', 'quan_ly')->value('ma_vai_tro');
            $quanLy = NguoiDung::with(['vaiTro', 'rapPhim'])
                ->where('ma_vai_tro', $roleManager)
                ->get();
            return response()->json($quanLy);
        } catch (\Exception $e) {
            Log::error('Error fetching managers: ' . $e->getMessage());
            return response()->json(['message' => 'Có lỗi xảy ra khi lấy danh sách quản lý'], 500);
        }
    }

    public function createNhanVien(Request $request)
    {
        $currentUser = Auth::user();

        $validator = Validator::make($request->all(), [
            'ho_ten' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:nguoi_dung',
            'mat_khau' => 'required|string|min:6',
            'sdt' => 'required|string|max:20',
            'ngay_sinh' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Lấy vai trò nhân viên
        $vaiTroNhanVien = VaiTro::where('ten_vai_tro', VaiTro::NHAN_VIEN)->first();
        if (!$vaiTroNhanVien) {
            return response()->json(['message' => 'Vai trò nhân viên không tồn tại'], 404);
        }

        $nhanVien = NguoiDung::create([
            'ho_ten' => $request->ho_ten,
            'email' => $request->email,
            'mat_khau' => Hash::make($request->mat_khau),
            'sdt' => $request->sdt,
            'ma_vai_tro' => $vaiTroNhanVien->ma_vai_tro,
            'ma_quan_ly' => $currentUser->ma_nguoi_dung,
            'ma_rap' => $currentUser->ma_rap, // Tự động thêm mã rạp của quản lý
            'ngay_sinh' => $request->ngay_sinh,
            'ngay_tao_nd' => now(),
            'anh_nguoi_dung' => 'default-avatar.jpg',
            'trang_thai' => 'hoat_dong'
        ]);

        return response()->json($nhanVien, 201);
    }

    public function updateNhanVien(Request $request, $id)
    {
        $currentUser = Auth::user();

        $nhanVien = NguoiDung::where('ma_nguoi_dung', $id)
            ->where('ma_quan_ly', $currentUser->ma_nguoi_dung)
            ->first();

        if (!$nhanVien) {
            return response()->json(['message' => 'Nhân viên không tồn tại hoặc không thuộc quyền quản lý của bạn'], 404);
        }

        $validator = Validator::make($request->all(), [
            'ho_ten' => 'string|max:255',
            'email' => 'string|email|max:255|unique:nguoi_dung,email,' . $id . ',ma_nguoi_dung',
            'mat_khau' => 'string|min:6|nullable',
            'sdt' => 'string|max:20',
            'ngay_sinh' => 'date'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $updateData = $request->only(['ho_ten', 'email', 'sdt', 'ngay_sinh']);
        if ($request->mat_khau) {
            $updateData['mat_khau'] = Hash::make($request->mat_khau);
        }

        // Đảm bảo mã rạp không bị thay đổi
        $updateData['ma_rap'] = $currentUser->ma_rap;
        $updateData['ma_quan_ly'] = $currentUser->ma_nguoi_dung;

        $nhanVien->update($updateData);
        return response()->json($nhanVien);
    }

    public function deleteNhanVien($id)
    {
        $currentUser = Auth::user();

        $nhanVien = NguoiDung::where('ma_nguoi_dung', $id)
            ->where('ma_quan_ly', $currentUser->ma_nguoi_dung)
            ->first();

        if (!$nhanVien) {
            return response()->json(['message' => 'Nhân viên không tồn tại hoặc không thuộc quyền quản lý của bạn'], 404);
        }

        $nhanVien->delete();
        return response()->json(['message' => 'Nhân viên đã được xóa']);
    }
}
