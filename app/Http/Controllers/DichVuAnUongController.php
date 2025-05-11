<?php

namespace App\Http\Controllers;

use App\Models\DvAnUong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class DichVuAnUongController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    /**
     * @OA\Get(
     *     path="/api/dichvuanuong",
     *     summary="Lấy danh sách các dịch vụ ăn uống",
     *     tags={"Dịch vụ ăn uống"},
     *     @OA\Response(
     *         response=200,
     *         description="Danh sách dịch vụ ăn uống",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="ten_dv", type="string", example="Combo bắp + nước"),
     *                 @OA\Property(property="gia_tien", type="number", format="float", example=45000)
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $dv = DvAnUong::all();
        return response()->json($dv);
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
            // Map frontend field names to backend field names
            $data = [
                'ten_dv_an_uong' => $request->input('ten_dv_an_uong'),
                'gia_tien' => $request->input('gia_tien'),
                'loai' => $request->input('loai'),
            ];

            // Validate data
            $validator = Validator::make($data, [
                'ten_dv_an_uong' => 'required|string|max:255',
                'gia_tien' => 'required|numeric|min:0',
                'loai' => 'nullable|string|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            // Handle image upload if present
            Log::info('All files:', $request->allFiles());
            if ($request->hasFile('anh_dv')) {
                $image = $request->file('anh_dv');
                $imageName = time() . '-' . $image->getClientOriginalName();
                $result = $image->storeAs('images', $imageName, 'public');
                Log::info('Kết quả lưu ảnh:', ['result' => $result, 'path' => storage_path('app/public/images/' . $imageName)]);
                $data['anh_dv'] = '/storage/images/' . $imageName;
            } else {
                Log::warning('Không nhận được file ảnh từ frontend!');
                $data['anh_dv'] = '/storage/images/default.jpg';
            }

            $data['ngay_tao_dv'] = now();
            $dv = DvAnUong::create($data);

            return response()->json([
                'message' => 'Thêm dịch vụ ăn uống thành công',
                'data' => $dv
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi thêm dịch vụ ăn uống',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $dv = DvAnUong::findOrFail($id);
        return response()->json($dv);
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
        try {
            Log::info('Bắt đầu cập nhật dịch vụ ăn uống', ['id' => $id, 'request_data' => $request->all()]);

            $dv = DvAnUong::find($id);
            if (!$dv) {
                Log::warning('Không tìm thấy dịch vụ ăn uống để cập nhật', ['id' => $id]);
                return response()->json(['message' => 'Dịch vụ ăn uống không tồn tại'], 404);
            }

            // Map frontend field names to backend field names
            $data = [
                'ten_dv_an_uong' => $request->input('ten_dv_an_uong'),
                'gia_tien' => $request->input('gia_tien'),
                'loai' => $request->input('loai'),
            ];

            // Validate data
            $validator = Validator::make($data, [
                'ten_dv_an_uong' => 'sometimes|string|max:255',
                'gia_tien' => 'sometimes|numeric|min:0',
                'loai' => 'sometimes|string|max:100',
            ]);

            if ($validator->fails()) {
                Log::warning('Validation thất bại', ['errors' => $validator->errors()]);
                return response()->json($validator->errors(), 422);
            }

            // Handle image upload if present
            if ($request->hasFile('anh_dv')) {
                $image = $request->file('anh_dv');
                $imageName = time() . '-' . $image->getClientOriginalName();
                $image->storeAs('images', $imageName, 'public');
                $data['anh_dv'] = '/storage/images/' . $imageName;

                // Delete old image if exists
                if ($dv->anh_dv && file_exists(public_path($dv->anh_dv))) {
                    unlink(public_path($dv->anh_dv));
                }
            }

            // Remove null values
            $data = array_filter($data, function($value) {
                return $value !== null;
            });

            // Update service data
            $dv->update($data);

            Log::info('Cập nhật dịch vụ ăn uống thành công', ['id' => $id]);

            return response()->json([
                'message' => 'Cập nhật dịch vụ ăn uống thành công',
                'data' => $dv
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật dịch vụ ăn uống', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Có lỗi xảy ra khi cập nhật dịch vụ ăn uống',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $dv = DvAnUong::findOrFail($id);
        $dv->delete();
        return response()->json(['message' => 'Đã xóa dịch vụ ăn uống']);
    }
}
