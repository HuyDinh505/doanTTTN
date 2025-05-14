<?php

namespace App\Http\Controllers;

use App\Models\GheNgoi;
use App\Models\PhongChieu;
use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PhongChieuController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/phong/dsghe/{ma_phong}",
     *     summary="Lấy danh sách ghế theo phòng chiếu",
     *     description="API này dùng để lấy danh sách các ghế trong một phòng chiếu cụ thể.",
     *     tags={"Phòng Chiếu"},
     *     @OA\Parameter(
     *         name="ma_phong",
     *         in="path",
     *         description="Mã phòng chiếu cần lấy ghế",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Danh sách ghế trong phòng chiếu",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="ma_ghe", type="string", example="G01"),
     *                 @OA\Property(property="so_ghe", type="string", example="A1")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Không tìm thấy ghế nào trong phòng chiếu",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Không tìm thấy ghế nào")
     *         )
     *     )
     * )
     */


     public function getGhebyPhong($ma_phong)
{
    $ghes = GheNgoi::where('ma_phong', $ma_phong)
        ->select('ma_ghe', 'so_ghe', 'ngay_tao_ghe')
        ->get();
    // Luôn trả về mảng (có thể rỗng)
    return response()->json($ghes, 200);
}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = Auth::user();
            $query = PhongChieu::with(['rap_phim', 'ghe_ngois']);

            // Nếu là quản lý, chỉ lấy phòng chiếu của rạp mà họ quản lý
            if ($user->ma_vai_tro === 2) { // 2 là mã vai trò quản lý
                $query->where('ma_rap', $user->ma_rap);
            }

            $phongChieu = $query->get();
            return response()->json($phongChieu);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Có lỗi xảy ra khi lấy danh sách phòng chiếu'], 500);
        }
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
        try {
            $user = Auth::user();

            // Nếu là quản lý, chỉ cho phép tạo phòng chiếu cho rạp của họ
            if ($user->ma_vai_tro === 2) {
                $request->merge(['ma_rap' => $user->ma_rap]);
            }

            $validator = Validator::make($request->all(), [
                'ten_phong' => 'required|string|max:100',
                'so_hang' => 'required|integer|min:1',
                'so_cot' => 'required|integer|min:1',
                'loai_phong' => 'required|in:2D,3D,4DX,IMAX',
                'ma_rap' => 'required|exists:rap_phim,ma_rap',
                'trang_thai' => 'required|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $request->all();
            $data['so_ghe'] = $data['so_hang'] * $data['so_cot'];
            $data['ngay_tao_phong'] = now();

            $phongChieu = PhongChieu::create($data);
            return response()->json($phongChieu, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Có lỗi xảy ra khi tạo phòng chiếu'], 500);
        }
    }

    /**
     * Display the specified resource.
     */



    /**
     * @OA\Get(
     *     path="/api/phong/{ma_phong}",
     *     summary="Lấy thông tin phòng chiếu theo mã",
     *     description="API này dùng để lấy thông tin chi tiết một phòng chiếu theo mã phòng.",
     *     tags={"Phòng Chiếu"},
     *     @OA\Parameter(
     *         name="ma_phong",
     *         in="path",
     *         description="Mã phòng chiếu cần tìm",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Thông tin phòng chiếu",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="ma_phong", type="string", example="PC001"),
     *             @OA\Property(property="ten_phong", type="string", example="Phòng Chiếu 1"),
     *             @OA\Property(property="so_ghe", type="integer", example=50),
     *             @OA\Property(property="mo_ta", type="string", example="Phòng chiếu hiện đại với ghế ngồi thoải mái")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Phòng chiếu không tồn tại",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Phòng chiếu không tồn tại")
     *         )
     *     )
     * )
     */
    public function show($ma_phong)
    {
        try {
            $user = Auth::user();
            $query = PhongChieu::with(['rap_phim', 'ghe_ngois']);

            // Chỉ giới hạn quyền truy cập cho quản lý
            if ($user && $user->ma_vai_tro === 2) {
                $query->where('ma_rap', $user->ma_rap);
            }

            $phongChieu = $query->findOrFail($ma_phong);

            // Kiểm tra trạng thái phòng chiếu
            if (!$phongChieu->isActive()) {
                return response()->json(['message' => 'Phòng chiếu đang bảo trì'], 403);
            }

            return response()->json($phongChieu);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Phòng chiếu không tồn tại'], 404);
        }
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
    public function update(Request $request, $ma_phong)
    {
        try {
            $user = Auth::user();
            $phongChieu = PhongChieu::findOrFail($ma_phong);

            // Kiểm tra quyền truy cập
            if ($user->ma_vai_tro === 2 && $phongChieu->ma_rap !== $user->ma_rap) {
                return response()->json(['message' => 'Bạn không có quyền cập nhật phòng chiếu này'], 403);
            }

            $validator = Validator::make($request->all(), [
                'ten_phong' => 'string|max:100',
                'so_hang' => 'integer|min:1',
                'so_cot' => 'integer|min:1',
                'loai_phong' => 'in:2D,3D,4DX,IMAX',
                'trang_thai' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $request->all();
            if (isset($data['so_hang']) || isset($data['so_cot'])) {
                $data['so_ghe'] = ($data['so_hang'] ?? $phongChieu->so_hang) *
                                ($data['so_cot'] ?? $phongChieu->so_cot);
            }

            $phongChieu->update($data);
            return response()->json($phongChieu);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Có lỗi xảy ra khi cập nhật phòng chiếu'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($ma_phong)
    {
        try {
            $user = Auth::user();
            $phongChieu = PhongChieu::findOrFail($ma_phong);

            // Kiểm tra quyền truy cập
            if ($user->ma_vai_tro === 2 && $phongChieu->ma_rap !== $user->ma_rap) {
                return response()->json(['message' => 'Bạn không có quyền xóa phòng chiếu này'], 403);
            }

            $phongChieu->delete();
            return response()->json(['message' => 'Xóa phòng chiếu thành công']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Có lỗi xảy ra khi xóa phòng chiếu'], 500);
        }
    }
}
