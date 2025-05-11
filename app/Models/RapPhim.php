<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RapPhim
 *
 * @property int $ma_rap
 * @property string $ten_rap
 * @property string $dia_chi
 * @property string $dia_chi_map
 * @property Carbon $ngay_tao_rap
 *
 * @property Collection|PhongChieu[] $phong_chieus
 *
 * @package App\Models
 */
class RapPhim extends Model
{
    protected $table = 'rap_phim';
    protected $primaryKey = 'ma_rap';
    public $timestamps = false;

    protected $casts = [
        'ngay_tao_rap' => 'datetime'
    ];

    protected $fillable = [
        'ten_rap',
        'dia_chi',
        'dia_chi_map',
        'ngay_tao_rap'
    ];

    public function phong_chieus()
    {
        return $this->hasMany(PhongChieu::class, 'ma_rap');
    }

    public function suatChieu()
    {
        return $this->hasManyThrough(
            SuatChieu::class,
            PhongChieu::class,
            'ma_rap',
            'ma_phong',
            'ma_rap',
            'ma_phong'
        );
    }
}
