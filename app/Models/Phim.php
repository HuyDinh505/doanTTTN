<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Phim",
 *     type="object",
 *     title="Phim",
 *     description="Thông tin về phim",
 *     @OA\Property(property="ma_phim", type="integer", example=1),
 *     @OA\Property(property="ten_phim", type="string", example="Avengers: Endgame"),
 *     @OA\Property(property="mo_ta", type="string", example="Phim siêu anh hùng"),
 *     @OA\Property(property="thoi_luong", type="integer", example=180),
 *     @OA\Property(property="ngay_phat_hanh", type="string", format="date", example="2019-04-26"),
 *     @OA\Property(property="anh", type="string", example="https://example.com/image.jpg"),
 *     @OA\Property(property="hinh_thuc_chieu", type="string", example="2D, 3D, IMAX"),
 *     @OA\Property(property="dao_dien", type="string", example="Anthony Russo, Joe Russo"),
 *     @OA\Property(property="dien_vien", type="string", example="Robert Downey Jr., Chris Evans"),
 *     @OA\Property(property="trang_thai", type="string", example="Đang chiếu"),
 *     @OA\Property(property="do_tuoi", type="string", example="13+"),
 *     @OA\Property(property="quoc_gia", type="string", example="Mỹ"),
 *     @OA\Property(property="ngay_tao_phim", type="string", format="date-time", example="2024-01-01T00:00:00Z")
 * )
 */

class Phim extends Model
{
    protected $table = 'phim';
    protected $primaryKey = 'ma_phim';
    public $timestamps = false;

    protected $casts = [
        'thoi_luong' => 'int',
        'ngay_phat_hanh' => 'datetime',
        'ngay_tao_phim' => 'datetime'
    ];

    protected $fillable = [
        'ten_phim',
        'mo_ta',
        'thoi_luong',
        'ngay_phat_hanh',
        'anh',
        'hinh_thuc_chieu',
        'dao_dien',
        'dien_vien',
        'trang_thai',
        'do_tuoi',
        'quoc_gia',
        'ngay_tao_phim'
    ];

    public function phim_loais()
    {
        return $this->hasMany(PhimLoai::class, 'ma_phim');
    }

    public function suat_chieus()
    {
        return $this->hasMany(SuatChieu::class, 'ma_phim');
    }

    public function rapChieu()
    {
        return $this->hasManyThrough(
            RapPhim::class,
            SuatChieu::class,
            'ma_phim',
            'ma_rap',
            'ma_phim',
            'ma_rap'
        );
    }

    public function loaiPhim()
    {
        return $this->belongsToMany(LoaiPhim::class, 'phim_loai', 'ma_phim', 'ma_loai');
    }
}
