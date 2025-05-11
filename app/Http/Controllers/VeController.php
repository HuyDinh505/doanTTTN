<?php

namespace App\Http\Controllers;

use App\Models\DatVe;
use App\Models\VeDat;
use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VeController extends Controller
{
    /**
     * Lấy danh sách vé
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = DatVe::join('nguoi_dung', 'dat_ve.ma_nguoi_dung', '=', 'nguoi_dung.ma_nguoi_dung')
            ->with(['suat_chieu.phim', 've_dats.ghe_ngoi', 'suat_chieu.phong_chieu'])
            ->select('dat_ve.*', 'nguoi_dung.ho_ten', 'nguoi_dung.email', 'nguoi_dung.sdt')
            ->orderBy('dat_ve.ngay_dat', 'desc');

        // Nếu là nhân viên, chỉ lấy vé thuộc rạp của nhân viên
        if ($user && isset($user->ma_vai_tro) && ($user->ma_vai_tro == 3 || $user->ma_vai_tro === 'nhan_vien')) {
            $ma_rap = $user->ma_rap;
            $query->whereHas('suat_chieu.phong_chieu', function($q) use ($ma_rap) {
                $q->where('ma_rap', $ma_rap);
            });
        }

        // Lọc theo trạng thái
        if ($request->has('trang_thai')) {
            $query->where('dat_ve.trang_thai', $request->trang_thai);
        }

        // Lọc theo ngày
        if ($request->has('ngay_dat')) {
            $query->whereDate('dat_ve.ngay_dat', $request->ngay_dat);
        }

        $ve = $query->get();

        // Transform the data to combine showtime date and time
        $ve = $ve->map(function ($item) {
            $item->thoi_gian_chieu = null;
            if ($item->suat_chieu) {
                if ($item->suat_chieu->ngay_chieu && $item->suat_chieu->thoi_gian_bd) {
                    $date = \Carbon\Carbon::parse($item->suat_chieu->ngay_chieu);
                    $time = \Carbon\Carbon::parse($item->suat_chieu->thoi_gian_bd);
                    $item->thoi_gian_chieu = $date->format('Y-m-d') . ' ' . $time->format('H:i:s');
                }
            }

            // Format customer information
            $item->khach_hang = [
                'ten' => $item->ho_ten,
                'email' => $item->email,
                'sdt' => $item->sdt
            ];

            return $item;
        });

        return response()->json($ve);
    }

    /**
     * Hiển thị chi tiết vé
     */
    public function show($id)
    {
        $ve = DatVe::join('nguoi_dung', 'dat_ve.ma_nguoi_dung', '=', 'nguoi_dung.ma_nguoi_dung')
            ->with(['suat_chieu.phim', 've_dats.ghe_ngoi'])
            ->select('dat_ve.*', 'nguoi_dung.ho_ten', 'nguoi_dung.email', 'nguoi_dung.sdt')
            ->where('dat_ve.ma_ve', $id)
            ->first();

        if (!$ve) {
            return response()->json(['message' => 'Không tìm thấy vé'], 404);
        }

        // Format customer information
        $ve->khach_hang = [
            'ten' => $ve->ho_ten,
            'email' => $ve->email,
            'sdt' => $ve->sdt
        ];

        return response()->json($ve);
    }

    /**
     * Cập nhật trạng thái vé
     */
    public function updateTrangThai(Request $request, $id)
    {
        $ve = DatVe::find($id);
        if (!$ve) {
            return response()->json(['message' => 'Không tìm thấy vé'], 404);
        }

        $request->validate([
            'trang_thai' => 'required|in:Đang chờ thanh toán,Đã thanh toán,Đã hủy'
        ]);

        $ve->trang_thai = $request->trang_thai;
        $ve->save();

        return response()->json($ve);
    }

    public function getStaffTickets(Request $request)
    {
        $user = $request->user();
        $maRap = $user->ma_rap;
        $tickets = DatVe::whereHas('suat_chieu', function($query) use ($maRap) {
            $query->where('ma_rap', $maRap);
        })->with(['suat_chieu.phim', 've_dats.ghe_ngoi', 'nguoi_dung'])->get();
        return response()->json($tickets);
    }

    public function updateTicketStatus(Request $request, $id)
    {
        $user = $request->user();
        $maRap = $user->ma_rap;
        $ve = DatVe::where('ma_ve', $id)
            ->whereHas('suat_chieu', function($query) use ($maRap) {
                $query->where('ma_rap', $maRap);
            })->first();
        if (!$ve) {
            return response()->json(['message' => 'Không có quyền thao tác vé này'], 403);
        }
        $ve->trang_thai = $request->input('trang_thai');
        $ve->save();
        return response()->json(['message' => 'Cập nhật trạng thái thành công']);
    }
}

