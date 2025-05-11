<?php

namespace App\Http\Controllers;

use App\Models\ChiTietDv;
use App\Models\ChiTietVe;
use App\Models\DatVe;
use App\Models\DvAnUong;
use App\Models\LoaiVe;
use App\Models\VeDat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * @OA\Post(
     *     path="/api/ve",
     *     summary="Đặt vé xem phim",
     *     description="API này dùng để đặt vé bao gồm: ghế, loại vé, dịch vụ ăn uống và tính tổng tiền.",
     *     tags={"Đặt vé"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"ma_nguoi_dung", "ma_sc", "tong_tien", "loai_ve", "ghe", "ngay_dat"},
     *             @OA\Property(property="ma_nguoi_dung", type="string", example="ND001"),
     *             @OA\Property(property="ma_sc", type="string", example="SC002"),
     *             @OA\Property(property="tong_tien", type="string", example="150000"),
     *             @OA\Property(property="loai_ve", type="string", example="LV01:1,LV02:2"),
     *             @OA\Property(property="ghe", type="string", example="G01,G02,G03"),
     *             @OA\Property(property="ngay_dat", type="string", format="date", example="2025-04-12"),
     *             @OA\Property(property="bap_nuoc", type="string", example="DV01:1,DV02:2", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Đặt vé thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="ma_ve", type="string", example="VE1712900153123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Lỗi xử lý",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Lỗi khi đặt vé!"),
     *             @OA\Property(property="error", type="string", example="Chi tiết lỗi từ server")
     *         )
     *     )
     * )
     */

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $datve = new DatVe();
            $datve->ma_ve = 'VE' . time() . rand(100, 999);
            $datve->ma_nguoi_dung = $request->input('ma_nguoi_dung');
            $datve->ma_suat_chieu = $request->input('ma_sc');

            $number = str_replace(['.', ' VNĐ'], '', (string) $request->tong_tien);
            $number1 = str_replace(',', '', $number);
            $datve->tong_gia_tien = $number1;

            $tong_so_ve = 0;
            $loaive = explode(',', $request->input('loai_ve'));
            foreach ($loaive as $loai) {
                $ve = explode(':', $loai);
                if (isset($ve[1])) {
                    $tong_so_ve += (int) $ve[1];
                }
            }

            $datve->tong_so_ve = $tong_so_ve;
            $datve->trang_thai = 'Đang chờ thanh toán';
            $datve->ngay_dat_ve = Carbon::parse($request->ngay_dat_ve);
            $datve->save();

            $gheId = explode(',', $request->ghe);
            $index = 0;

            // Group tickets by type
            $ticketsByType = [];
            foreach ($loaive as $loaiveItem) {
                [$maloaive, $soluong] = explode(':', $loaiveItem);
                $loai = LoaiVe::find($maloaive);
                if (!$loai) {
                    throw new \Exception("Không tìm thấy loại vé: $maloaive");
                }

                // Create one VeDat record per ticket type
                $gheForThisType = array_slice($gheId, $index, $soluong);
                $index += $soluong;

                VeDat::create([
                    'ma_ve' => $datve->ma_ve,
                    'ma_loai_ve' => $maloaive,
                    'so_luong' => $soluong,
                    'gia_tien' => $soluong * $loai->gia_ve,
                    'ma_ghe' => implode(',', $gheForThisType)
                ]);
            }

            if (!empty($request->bap_nuoc)) {
                $dichVuData = explode(',', $request->bap_nuoc);
                foreach ($dichVuData as $dvItem) {
                    [$madv, $soLuongDv] = explode(':', $dvItem);
                    $loaDv = DvAnUong::find($madv);
                    if (!$loaDv) {
                        throw new \Exception("Không tìm thấy dịch vụ ăn uống: $madv");
                    }
                    ChiTietDv::create([
                        'ma_ve' => $datve->ma_ve,
                        'ma_dv_an_uong' => $madv,
                        'so_luong' => $soLuongDv,
                        'tong_gia_tien' => $soLuongDv * $loaDv->gia_tien
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                "success" => true,
                "ma_ve" => $datve->ma_ve
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi đặt vé!',
                'error' => $e->getMessage()
            ], 500);
        }
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
