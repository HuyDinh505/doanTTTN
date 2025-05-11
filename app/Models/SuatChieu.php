<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SuatChieu
 *
 * @property int $ma_suat_chieu
 * @property int $ma_phim
 * @property int $ma_phong
 * @property string $thoi_gian_bd
 * @property Carbon $ngay_chieu
 * @property Carbon $ngay_tao_sc
 *
 * @property PhongChieu $phong_chieu
 * @property Phim $phim
 * @property Collection|DatVe[] $dat_ves
 *
 * @package App\Models
 */
class SuatChieu extends Model
{
	protected $table = 'suat_chieu';
	protected $primaryKey = 'ma_suat_chieu';
	public $timestamps = false;

	protected $casts = [
		'ma_phim' => 'int',
		'ma_phong' => 'int',
		'ngay_chieu' => 'datetime',
		'ngay_tao_sc' => 'datetime'
	];

	protected $fillable = [
		'ma_phim',
		'ma_phong',
		'thoi_gian_bd',
		'ngay_chieu',
		'ngay_tao_sc'
	];

	public function phong_chieu()
	{
		return $this->belongsTo(PhongChieu::class, 'ma_phong');
	}

	public function phim()
	{
		return $this->belongsTo(Phim::class, 'ma_phim');
	}

	public function dat_ves()
	{
		return $this->hasMany(DatVe::class, 'ma_suat_chieu');
	}
}
