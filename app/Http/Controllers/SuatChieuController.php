<?php

namespace App\Http\Controllers;

use App\Models\SuatChieu;
use App\Models\NguoiDung;
use App\Models\PhongChieu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use function PHPUnit\Framework\isEmpty;


class SuatChieuController extends Controller
{

    /**
     * Lấy danh sách suất chiếu theo mã phim
     * @OA\Get(
     *     path="/api/suatchieu/phim/{ma_phim}",
     *     summary="Lấy danh sách suất chiếu theo mã phim",
     *     description="API này dùng để lấy tất cả suất chiếu cho một phim.",
     *     tags={"Suất Chiếu"},
     *     @OA\Parameter(
     *         name="ma_phim",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="PH001"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Danh sách suất chiếu của phim",
     *         @OA\JsonContent(
     *             @OA\Property(property="ma_rap", type="string", example="RP001"),
     *             @OA\Property(property="ten_rap", type="string", example="Rạp Phim ABC"),
     *             @OA\Property(property="dia_chi", type="string", example="123 Đường ABC, Quận 1, TP.HCM"),
     *             @OA\Property(
     *                 property="suat_chieu",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="ma_suat_chieu", type="string", example="SC001"),
     *                     @OA\Property(property="ma_phong", type="string", example="P001"),
     *                     @OA\Property(property="phong", type="string", example="Phòng 1"),
     *                     @OA\Property(property="thoi_gian_bd", type="string", example="10:00"),
     *                     @OA\Property(property="ngay_chieu", type="string", example="12-04-2025")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không có suất chiếu cho phim này",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Không có suất chiếu cho phim này")
     *         )
     *     )
     * )
     */

    public function getByPhim($ma_phim)
    {
        $suatchieu = SuatChieu::with(['phong_chieu.rap_phim'])
            ->where('ma_phim', $ma_phim)
            ->get();

        if ($suatchieu->isEmpty()) {
            return response()->json(['message' => 'không có suất chiếu cho phim này'], 404);
        }

        $data = [];
        foreach ($suatchieu as $suat) {
            $phongchieu = $suat->phong_chieu;
            $rapphim = $phongchieu?->rap_phim;

            if (!$phongchieu || !$rapphim) {
                continue;
            }

            $tenrap = $rapphim->ten_rap;
            if (!isset($data[$tenrap])) {
                $data[$tenrap] = [
                    'ma_rap' => $rapphim->ma_rap,
                    'ten_rap' => $tenrap,
                    'dia_chi' => $rapphim->dia_chi,
                    'suat_chieu' => [],
                ];
            }

            $data[$tenrap]['suat_chieu'][] = [
                'ma_suat_chieu' => $suat->ma_suat_chieu,
                'ma_phong' => $phongchieu->ma_phong,
                'phong' => $phongchieu->ten_phong,
                'thoi_gian_bd' => is_string($suat->thoi_gian_bd) ? substr($suat->thoi_gian_bd, 0, 5) : ($suat->thoi_gian_bd?->format('H:i')),
                'ngay_chieu' => $suat->ngay_chieu?->format('d-m-Y'),
            ];
        }

        return response()->json(array_values($data));
    }


    /**
     * Display a listing of the resource.
     * @OA\Get(
     *     path="/api/suatchieu",
     *     summary="Lấy danh sách tất cả suất chiếu",
     *     description="API này dùng để lấy tất cả suất chiếu cùng thông tin phim và phòng chiếu.",
     *     tags={"Suất Chiếu"},
     *     @OA\Response(
     *         response=200,
     *         description="Danh sách tất cả suất chiếu",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="ma_suat_chieu", type="integer", example=1),
     *                 @OA\Property(property="ma_phim", type="integer", example=1),
     *                 @OA\Property(property="ma_phong", type="integer", example=1),
     *                 @OA\Property(property="thoi_gian_bd", type="string", example="10:00"),
     *                 @OA\Property(property="ngay_chieu", type="string", example="2024-04-22"),
     *                 @OA\Property(
     *                     property="phim",
     *                     type="object",
     *                     @OA\Property(property="ten_phim", type="string", example="Tên phim"),
     *                     @OA\Property(property="thoi_luong", type="integer", example=120)
     *                 ),
     *                 @OA\Property(
     *                     property="phong_chieu",
     *                     type="object",
     *                     @OA\Property(property="ten_phong", type="string", example="Phòng 1"),
     *                     @OA\Property(property="loai_phong", type="string", example="2D")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $user = Auth::user();
        $query = SuatChieu::with(['phim:ma_phim,ten_phim,thoi_luong', 'phong_chieu:ma_phong,ten_phong,loai_phong,ma_rap']);

        // Nếu là quản lý, chỉ lấy suất chiếu của rạp mà họ quản lý
        if ($user->ma_vai_tro === 2) { // 2 là mã vai trò quản lý
            $manager = NguoiDung::where('ma_nguoi_dung', $user->ma_nguoi_dung)
                               ->where('ma_vai_tro', 2)
                               ->first();

            if (!$manager || !$manager->ma_rap) {
                return response()->json(['message' => 'Bạn chưa được phân công quản lý rạp nào'], 403);
            }

            $query->whereHas('phong_chieu', function($q) use ($manager) {
                $q->where('ma_rap', $manager->ma_rap);
            });
        }

        $suatChieu = $query->orderBy('ngay_chieu', 'asc')
                          ->orderBy('thoi_gian_bd', 'asc')
                          ->get();

        // Đảm bảo trả về đúng định dạng HH:mm cho thoi_gian_bd
        $suatChieu = $suatChieu->map(function($item) {
            $item->thoi_gian_bd = is_string($item->thoi_gian_bd) ? substr($item->thoi_gian_bd, 0, 5) : ($item->thoi_gian_bd?->format('H:i'));
            return $item;
        });

        return response()->json($suatChieu);
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
        $user = Auth::user();

        // Kiểm tra quyền quản lý
        if ($user->ma_vai_tro === 2) {
            $manager = NguoiDung::where('ma_nguoi_dung', $user->ma_nguoi_dung)
                               ->where('ma_vai_tro', 2)
                               ->first();

            if (!$manager || !$manager->ma_rap) {
                return response()->json(['message' => 'Bạn chưa được phân công quản lý rạp nào'], 403);
            }

            // Kiểm tra xem phòng chiếu có thuộc rạp của quản lý không
            $phongChieu = PhongChieu::find($request->ma_phong);
            if (!$phongChieu || $phongChieu->ma_rap !== $manager->ma_rap) {
                return response()->json(['message' => 'Bạn không có quyền tạo suất chiếu cho phòng này'], 403);
            }
        }

        $validator = Validator::make($request->all(), [
            'ma_phim' => 'required|exists:phim,ma_phim',
            'ma_phong' => 'required|exists:phong_chieu,ma_phong',
            'ngay_chieu' => 'required|date',
            'thoi_gian_bd' => 'required|date_format:H:i'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $suatChieu = SuatChieu::create($request->all());
        return response()->json($suatChieu, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $suatChieu = SuatChieu::find($id);

        if (!$suatChieu) {
            return response()->json(['message' => 'Suất chiếu không tồn tại'], 404);
        }

        // Kiểm tra quyền quản lý
        if ($user->ma_vai_tro === 2) {
            $manager = NguoiDung::where('ma_nguoi_dung', $user->ma_nguoi_dung)
                               ->where('ma_vai_tro', 2)
                               ->first();

            if (!$manager || !$manager->ma_rap) {
                return response()->json(['message' => 'Bạn chưa được phân công quản lý rạp nào'], 403);
            }

            // Kiểm tra xem suất chiếu có thuộc rạp của quản lý không
            $phongChieu = $suatChieu->phong_chieu;
            if (!$phongChieu || $phongChieu->ma_rap !== $manager->ma_rap) {
                return response()->json(['message' => 'Bạn không có quyền cập nhật suất chiếu này'], 403);
            }
        }

        $validator = Validator::make($request->all(), [
            'ma_phim' => 'exists:phim,ma_phim',
            'ma_phong' => 'exists:phong_chieu,ma_phong',
            'ngay_chieu' => 'date',
            'thoi_gian_bd' => 'date_format:H:i'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $suatChieu->update($request->all());
        return response()->json($suatChieu);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $suatChieu = SuatChieu::find($id);

        if (!$suatChieu) {
            return response()->json(['message' => 'Suất chiếu không tồn tại'], 404);
        }

        // Kiểm tra quyền quản lý
        if ($user->ma_vai_tro === 2) {
            $manager = NguoiDung::where('ma_nguoi_dung', $user->ma_nguoi_dung)
                               ->where('ma_vai_tro', 2)
                               ->first();

            if (!$manager || !$manager->ma_rap) {
                return response()->json(['message' => 'Bạn chưa được phân công quản lý rạp nào'], 403);
            }

            // Kiểm tra xem suất chiếu có thuộc rạp của quản lý không
            $phongChieu = $suatChieu->phong_chieu;
            if (!$phongChieu || $phongChieu->ma_rap !== $manager->ma_rap) {
                return response()->json(['message' => 'Bạn không có quyền xóa suất chiếu này'], 403);
            }
        }

        $suatChieu->delete();
        return response()->json(['message' => 'Suất chiếu đã được xóa']);
    }
}
