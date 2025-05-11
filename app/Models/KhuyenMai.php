<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class KhuyenMai
 *
 * @property int $ma_khuyen_mai
 * @property string $ten_khuyen_mai
 * @property string $mo_ta
 * @property float $phan_tram_giam
 * @property Carbon $ngay_bat_dau
 * @property Carbon $ngay_ket_thuc
 * @property string $ma_code
 * @property int $so_luong
 * @property Carbon $ngay_tao_km
 *
 * @package App\Models
 */
class KhuyenMai extends Model
{
    protected $table = 'khuyen_mai';
    protected $primaryKey = 'ma_khuyen_mai';
    public $timestamps = false;

    protected $casts = [
        'phan_tram_giam' => 'float',
        'so_luong' => 'integer',
        'ngay_bat_dau' => 'date',
        'ngay_ket_thuc' => 'date',
        'ngay_tao_km' => 'datetime'
    ];

    protected $fillable = [
        'ten_khuyen_mai',
        'mo_ta',
        'phan_tram_giam',
        'ngay_bat_dau',
        'ngay_ket_thuc',
        'ma_code',
        'so_luong',
        'ngay_tao_km'
    ];

    public function isValid()
    {
        $now = Carbon::now();
        return $this->ngay_bat_dau <= $now
            && $this->ngay_ket_thuc >= $now
            && $this->so_luong > 0;
    }

    public function isExpired()
    {
        return Carbon::now() > $this->ngay_ket_thuc;
    }

    public function hasStock()
    {
        return $this->so_luong > 0;
    }
}
