<?php

namespace App\Http\Controllers;

use App\Models\Phim as PhimModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class PhimController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/phim",
     *     summary="Lấy danh sách phim",
     *     description="API này dùng để lấy danh sách tất cả các phim có sẵn.",
     *     tags={"Phim"},
     *     @OA\Response(
     *         response=200,
     *         description="Danh sách phim",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="ma_phim", type="string", example="PH001"),
     *                 @OA\Property(property="ten_phim", type="string", example="Phim 1"),
     *                 @OA\Property(property="mo_ta", type="string", example="Mô tả phim 1"),
     *                 @OA\Property(property="ngay_cong_chieu", type="string", format="date", example="2025-04-12"),
     *                 @OA\Property(property="the_loai", type="array", @OA\Items(type="string", example="Hành động"))
     *             )
     *         )
     *     )
     * )
     */

    public function index()
    {
        $phim = PhimModel::all();
        return response()->json($phim);
    }

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
            // Map frontend field names to backend field names
            $data = [
                'ten_phim' => $request->input('ten_phim'),
                'mo_ta' => $request->input('mo_ta'),
                'thoi_luong' => $request->input('thoi_luong'),
                'ngay_phat_hanh' => $request->input('ngay_phat_hanh'),
                'hinh_thuc_chieu' => $request->input('hinh_thuc_chieu'),
                'dao_dien' => $request->input('dao_dien'),
                'dien_vien' => $request->input('dien_vien'),
                'trang_thai' => $request->input('trang_thai'),
                'do_tuoi' => $request->input('do_tuoi'),
                'quoc_gia' => $request->input('quoc_gia')
            ];

            // Validate data
            $validator = Validator::make($data, [
                'ten_phim' => 'required|string|max:255',
                'mo_ta' => 'required|string',
                'thoi_luong' => 'required|integer',
                'ngay_phat_hanh' => 'required|date',
                'hinh_thuc_chieu' => 'required|string|max:255',
                'dao_dien' => 'required|string|max:255',
                'dien_vien' => 'required|string|max:255',
                'trang_thai' => 'required|string|max:255',
                'do_tuoi' => 'required|string|max:10',
                'quoc_gia' => 'required|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            // Handle image upload if present
            Log::info('All files:', $request->allFiles());
            if ($request->hasFile('hinhAnh')) {
                $image = $request->file('hinhAnh');
                $imageName = time() . '-' . $image->getClientOriginalName();
                $result = $image->storeAs('images', $imageName, 'public');
                Log::info('Kết quả lưu ảnh:', ['result' => $result, 'path' => storage_path('app/public/images/' . $imageName)]);
                $data['anh'] = '/storage/images/' . $imageName;
            } else {
                Log::warning('Không nhận được file ảnh từ frontend!');
                $data['anh'] = '/storage/images/default.jpg';
            }

            $phim = PhimModel::create($data);

            return response()->json([
                'message' => 'Thêm phim thành công',
                'data' => $phim
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi thêm phim',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/phim/{ma_phim}",
     *     summary="Lấy thông tin phim theo mã",
     *     description="API này dùng để lấy thông tin chi tiết của một phim dựa trên mã phim.",
     *     tags={"Phim"},
     *     @OA\Parameter(
     *         name="ma_phim",
     *         in="path",
     *         description="Mã phim cần tìm",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Thông tin phim chi tiết",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="ma_phim", type="string", example="PH001"),
     *             @OA\Property(property="ten_phim", type="string", example="Phim 1"),
     *             @OA\Property(property="mo_ta", type="string", example="Mô tả phim 1"),
     *             @OA\Property(property="ngay_cong_chieu", type="string", format="date", example="2025-04-12"),
     *             @OA\Property(property="the_loai", type="array", @OA\Items(type="string", example="Hành động"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Phim không tồn tại",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Phim không tồn tại")
     *         )
     *     )
     * )
     */
    public function show(string $ma_phim)
    {
        $phim = PhimModel::with('phim_loais.loai_phim')->where('ma_phim', $ma_phim)->first();
        if (!$phim) {
            return response()->json(['message' => 'Phim không tồn tại'], 404);
        }

        $phimArr = $phim->toArray();
        $phimArr['the_loai'] = $phim->phim_loais->map(function ($s1) {
            return $s1->loai_phim->ten_loai;
        });
        unset($phimArr['phim_loais']);
        return response()->json($phimArr);
    }


    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *     path="/api/phim/{id}",
     *     summary="Cập nhật thông tin phim",
     *     description="API này dùng để cập nhật thông tin của một phim.",
     *     tags={"Phim"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID của phim cần cập nhật",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="tenPhim", type="string"),
     *             @OA\Property(property="moTa", type="string"),
     *             @OA\Property(property="thoiLuong", type="integer"),
     *             @OA\Property(property="ngayKhoiChieu", type="string", format="date"),
     *             @OA\Property(property="hinhThucChieu", type="string"),
     *             @OA\Property(property="daoDien", type="string"),
     *             @OA\Property(property="dienVien", type="string"),
     *             @OA\Property(property="trangThai", type="string"),
     *             @OA\Property(property="doTuoi", type="string"),
     *             @OA\Property(property="quocGia", type="string"),
     *             @OA\Property(property="theLoai", type="array", @OA\Items(type="integer"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cập nhật phim thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Phim không tồn tại"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dữ liệu không hợp lệ"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            Log::info('Bắt đầu cập nhật phim', ['id' => $id, 'request_data' => $request->all()]);

            $phim = PhimModel::find($id);
            if (!$phim) {
                Log::warning('Không tìm thấy phim để cập nhật', ['id' => $id]);
                return response()->json(['message' => 'Phim không tồn tại'], 404);
            }

            // Map frontend field names to backend field names
            $data = [
                'ten_phim' => $request->input('ten_phim'),
                'mo_ta' => $request->input('mo_ta'),
                'thoi_luong' => $request->input('thoi_luong'),
                'ngay_phat_hanh' => $request->input('ngay_phat_hanh'),
                'hinh_thuc_chieu' => $request->input('hinh_thuc_chieu'),
                'dao_dien' => $request->input('dao_dien'),
                'dien_vien' => $request->input('dien_vien'),
                'trang_thai' => $request->input('trang_thai'),
                'do_tuoi' => $request->input('do_tuoi'),
                'quoc_gia' => $request->input('quoc_gia')
            ];

            // Validate data
            $validator = Validator::make($data, [
                'ten_phim' => 'sometimes|string|max:255',
                'mo_ta' => 'sometimes|string',
                'thoi_luong' => 'sometimes|integer|min:1',
                'ngay_phat_hanh' => 'sometimes|date',
                'hinh_thuc_chieu' => 'sometimes|string|max:255',
                'dao_dien' => 'sometimes|string|max:255',
                'dien_vien' => 'sometimes|string|max:255',
                'trang_thai' => 'sometimes|string|in:dang_chieu,sap_chieu,ngung_chieu',
                'do_tuoi' => 'sometimes|string|max:10',
                'quoc_gia' => 'sometimes|string|max:255'
            ]);

            if ($validator->fails()) {
                Log::warning('Validation thất bại', ['errors' => $validator->errors()]);
                return response()->json($validator->errors(), 422);
            }

            // Handle image upload if present
            if ($request->hasFile('hinhAnh')) {
                $image = $request->file('hinhAnh');
                $imageName = time() . '-' . $image->getClientOriginalName();
                $image->storeAs('images', $imageName, 'public');
                $data['anh'] = '/storage/images/' . $imageName;

                // Delete old image if exists
                if ($phim->anh && file_exists(public_path($phim->anh))) {
                    unlink(public_path($phim->anh));
                }
            }

            // Remove null values
            $data = array_filter($data, function($value) {
                return $value !== null;
            });

            // Update movie data
            $phim->update($data);

            // Handle movie genres update if provided
            if ($request->has('theLoai')) {
                $theLoaiIds = $request->input('theLoai');
                $phim->phim_loais()->delete(); // Remove existing genres
                foreach ($theLoaiIds as $theLoaiId) {
                    $phim->phim_loais()->create(['ma_loai' => $theLoaiId]);
                }
            }

            Log::info('Cập nhật phim thành công', ['id' => $id]);

            return response()->json([
                'message' => 'Cập nhật phim thành công',
                'data' => $phim->load('phim_loais.loai_phim')
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật phim', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Có lỗi xảy ra khi cập nhật phim',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $phim = PhimModel::find($id);
            if (!$phim) {
                return response()->json(['message' => 'Phim không tồn tại'], 404);
            }
            $phim->delete();
            return response()->json(['message' => 'Xóa phim thành công']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi xóa phim',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getPhimDangChieu()
    {
        $phims = PhimModel::where('trang_thai', 'dang_chieu')
            // ->orderBy('ngay_khoi_chieu', 'desc')
            ->get();

        return response()->json($phims);
    }

    public function getPhimSapChieu()
    {
        $phims = PhimModel::where('trang_thai', 'sap_chieu')
            // ->orderBy('ngay_khoi_chieu', 'asc')
            ->get();

        return response()->json($phims);
    }

}
