<?php

namespace App\Http\Controllers;

use App\Models\RapPhim;
use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RapPhimController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $query = RapPhim::with('phong_chieus');

        // Nếu đã đăng nhập và là quản lý (ma_vai_tro = 2), chỉ lấy thông tin rạp mà họ quản lý
        if ($user && $user->ma_vai_tro === 2 && $user->ma_rap) {
            $query->where('ma_rap', $user->ma_rap);
        } else if ($user && $user->ma_vai_tro === 2) {
            return response()->json(['message' => 'Bạn chưa được phân công quản lý rạp nào'], 403);
        }

        $rapPhim = $query->get();
        return response()->json($rapPhim);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_rap' => 'required|string|max:255',
            'dia_chi' => 'required|string|max:255',
            'dia_chi_map' => 'nullable|string|max:255',
        ]);
        $rap = RapPhim::create($validated);
        return response()->json(['message' => 'Thêm rạp thành công', 'data' => $rap], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/rap/{ma_rap}",
     *     summary="Lấy thông tin rạp chiếu phim",
     *     description="API này dùng để lấy thông tin chi tiết của một rạp chiếu phim theo ID.",
     *     tags={"Rạp chiếu phim"},
     *     @OA\Parameter(
     *         name="ma_rap",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="RP001"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lấy thông tin rạp chiếu phim thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="string", example="RP001"),
     *             @OA\Property(property="ten", type="string", example="Rạp Phim ABC"),
     *             @OA\Property(property="dia_chi", type="string", example="123 Đường ABC, Quận 1, TP.HCM"),
     *             @OA\Property(property="so_phong_chieu", type="integer", example=5)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rạp chiếu phim không tồn tại",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Không tìm thấy rạp chiếu phim!")
     *         )
     *     )
     * )
     */
    public function show(string $id)
    {
        $user = Auth::user();
        $rapPhim = RapPhim::find($id);

        if (!$rapPhim) {
            return response()->json(['message' => 'Rạp phim không tồn tại'], 404);
        }

        // Nếu đã đăng nhập và là quản lý, chỉ cho phép xem thông tin rạp họ quản lý
        if ($user && $user->ma_vai_tro === 2) {
            $manager = NguoiDung::where('ma_nguoi_dung', $user->ma_nguoi_dung)
                                ->where('ma_vai_tro', 2)
                                ->first();

            if (!$manager || !$manager->ma_rap) {
                return response()->json(['message' => 'Bạn chưa được phân công quản lý rạp nào'], 403);
            }

            if ($rapPhim->ma_rap !== $manager->ma_rap) {
                return response()->json(['message' => 'Bạn không có quyền xem thông tin rạp này'], 403);
            }
        }

        return response()->json($rapPhim);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $rap = RapPhim::find($id);
        if (!$rap) {
            return response()->json(['message' => 'Rạp phim không tồn tại'], 404);
        }
        $validated = $request->validate([
            'ten_rap' => 'sometimes|string|max:255',
            'dia_chi' => 'sometimes|string|max:255',
            'dia_chi_map' => 'nullable|string|max:255',
        ]);
        $rap->update($validated);
        return response()->json(['message' => 'Cập nhật rạp thành công', 'data' => $rap]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $rap = RapPhim::find($id);
        if (!$rap) {
            return response()->json(['message' => 'Rạp phim không tồn tại'], 404);
        }
        $rap->delete();
        return response()->json(['message' => 'Xóa rạp thành công']);
    }
}
