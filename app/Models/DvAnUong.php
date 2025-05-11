<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DvAnUong
 *
 * @property int $ma_dv_an_uong
 * @property string $ten_dv_an_uong
 * @property float $gia_tien
 * @property string $loai
 * @property string $anh_dv
 * @property Carbon $ngay_tao_dv
 *
 * @property Collection|ChiTietDv[] $chi_tiet_dvs
 *
 * @package App\Models
 */
class DvAnUong extends Model
{
    protected $table = 'dich_vu_an_uong';
    protected $primaryKey = 'ma_dv_an_uong';
    public $timestamps = false;

    protected $casts = [
        'gia_tien' => 'float',
        'ngay_tao_dv' => 'datetime'
    ];

    protected $fillable = [
        'ten_dv_an_uong',
        'gia_tien',
        'loai',
        'anh_dv',
        'ngay_tao_dv'
    ];

    public function chi_tiet_dvs()
    {
        return $this->hasMany(ChiTietDv::class, 'ma_dv_an_uong');
    }
}
