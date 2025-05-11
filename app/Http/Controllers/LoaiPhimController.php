<?php

namespace App\Http\Controllers;

use App\Models\LoaiPhim;
use App\Models\Phim;
use Illuminate\Http\Request;

class LoaiPhimController extends Controller
{
    public function index()
    {
        $loaiPhim = LoaiPhim::all();
        return response()->json($loaiPhim);
    }

    public function addMovieGenres($movieId, Request $request)
    {
        try {
            $phim = Phim::findOrFail($movieId);
            $phim->loaiPhim()->sync($request->loai_phim_ids);

            return response()->json([
                'message' => 'Cập nhật thể loại thành công',
                'data' => $phim->load('loaiPhim')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi cập nhật thể loại',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function removeMovieGenres($movieId, Request $request)
    {
        try {
            $phim = Phim::findOrFail($movieId);
            $phim->loaiPhim()->detach($request->loai_phim_ids);

            return response()->json([
                'message' => 'Xóa thể loại thành công',
                'data' => $phim->load('loaiPhim')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi xóa thể loại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
