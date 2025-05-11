<?php

namespace App\Http\Controllers;

use App\Models\KhuyenMai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KhuyenMaiController extends Controller
{
    public function index()
    {
        $khuyenMai = KhuyenMai::all();
        return response()->json($khuyenMai);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ten_khuyen_mai' => 'required|string|max:100',
            'mo_ta' => 'required|string',
            'phan_tram_giam' => 'required|numeric|min:0|max:100',
            'ngay_bat_dau' => 'required|date',
            'ngay_ket_thuc' => 'required|date|after:ngay_bat_dau',
            'ma_code' => 'required|string|unique:khuyen_mai,ma_code',
            'so_luong' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $khuyenMai = KhuyenMai::create($request->all());
        return response()->json($khuyenMai, 201);
    }

    public function update(Request $request, $id)
    {
        $khuyenMai = KhuyenMai::find($id);
        if (!$khuyenMai) {
            return response()->json(['message' => 'Không tìm thấy khuyến mãi'], 404);
        }

        $validator = Validator::make($request->all(), [
            'ten_khuyen_mai' => 'string|max:100',
            'mo_ta' => 'string',
            'phan_tram_giam' => 'numeric|min:0|max:100',
            'ngay_bat_dau' => 'date',
            'ngay_ket_thuc' => 'date|after:ngay_bat_dau',
            'ma_code' => 'string|unique:khuyen_mai,ma_code,'.$id.',ma_khuyen_mai',
            'so_luong' => 'integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $khuyenMai->update($request->all());
        return response()->json($khuyenMai);
    }

    public function destroy($id)
    {
        $khuyenMai = KhuyenMai::find($id);
        if (!$khuyenMai) {
            return response()->json(['message' => 'Không tìm thấy khuyến mãi'], 404);
        }

        $khuyenMai->delete();
        return response()->json(['message' => 'Đã xóa khuyến mãi thành công']);
    }
}
