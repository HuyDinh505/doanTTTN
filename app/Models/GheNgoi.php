<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class GheNgoi
 *
 * @property int $ma_ghe
 * @property int $ma_phong
 * @property string $so_ghe
 * @property Carbon $ngay_tao_ghe
 *
 * @property PhongChieu $phong_chieu
 * @property Collection|ChiTietVe[] $chi_tiet_ves
 *
 * @package App\Models
 */
class GheNgoi extends Model
{
    protected $table = 'ghe_ngoi';
    protected $primaryKey = 'ma_ghe';
    public $timestamps = false;

    protected $casts = [
        'ma_phong' => 'int',
        'ngay_tao_ghe' => 'datetime'
    ];

    protected $fillable = [
        'ma_phong',
        'so_ghe',
        'ngay_tao_ghe'
    ];

    public function phong_chieu()
    {
        return $this->belongsTo(PhongChieu::class, 'ma_phong');
    }

    public function ve_dat()
    {
        return $this->hasMany(VeDat::class, 'ma_ghe');
    }
}
