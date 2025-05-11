<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ChiTietDv
 *
 * @property int $ma_ve
 * @property int $ma_dv_an_uong
 * @property int $so_luong
 * @property float $tong_gia_tien
 *
 * @property DvAnUong $dv_an_uong
 * @property DatVe $dat_ve
 *
 * @package App\Models
 */
class ChiTietDv extends Model
{
    protected $table = 'chi_tiet_dv';
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = [
        'ma_ve' => 'string',
        'ma_dv_an_uong' => 'int',
        'so_luong' => 'int',
        'tong_gia_tien' => 'float'
    ];

    protected $fillable = [
        'ma_ve',
        'ma_dv_an_uong',
        'so_luong',
        'tong_gia_tien'
    ];

    public function dv_an_uong()
    {
        return $this->belongsTo(DvAnUong::class, 'ma_dv_an_uong');
    }

    public function dat_ve()
    {
        return $this->belongsTo(DatVe::class, 'ma_ve');
    }
}
