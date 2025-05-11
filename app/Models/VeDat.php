<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VeDat
 *
 * @property int $ma_ve
 * @property int $ma_loai_ve
 * @property int $so_luong
 * @property float $gia_tien
 *
 * @property DatVe $dat_ve
 * @property LoaiVe $loai_ve
 *
 * @package App\Models
 */
class VeDat extends Model
{
    protected $table = 've_dat';
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = [
        'ma_ve' => 'string',
        'ma_loai_ve' => 'int',
        'so_luong' => 'int',
        'gia_tien' => 'float',
        ''
    ];

    protected $fillable = [
        'ma_ve',
        'ma_loai_ve',
        'so_luong',
        'gia_tien',
        'ma_ghe'
    ];

    public function dat_ve()
    {
        return $this->belongsTo(DatVe::class, 'ma_ve');
    }

    public function loai_ve()
    {
        return $this->belongsTo(LoaiVe::class, 'ma_loai_ve');
    }

    public function ghe_ngoi()
    {
        return $this->belongsTo(GheNgoi::class, 'ma_ghe');
    }

}
