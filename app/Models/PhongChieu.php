<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PhongChieu
 *
 * @property int $ma_phong
 * @property int $ma_rap
 * @property string $ten_phong
 * @property string $loai_phong
 * @property int $so_cot
 * @property int $so_hang
 * @property int $so_ghe
 * @property bool $trang_thai
 * @property Carbon $ngay_tao_phong
 *
 * @property RapPhim $rap_phim
 * @property Collection|GheNgoi[] $ghe_ngois
 * @property Collection|SuatChieu[] $suat_chieus
 *
 * @package App\Models
 */
class PhongChieu extends Model
{
	protected $table = 'phong_chieu';
	protected $primaryKey = 'ma_phong';
	public $timestamps = false;

	protected $casts = [
		'ma_rap' => 'int',
		'so_cot' => 'int',
		'so_hang' => 'int',
		'so_ghe' => 'int',
		'trang_thai' => 'boolean',
		'ngay_tao_phong' => 'datetime'
	];

	protected $fillable = [
		'ma_rap',
		'ten_phong',
		'loai_phong',
		'so_cot',
		'so_hang',
		'so_ghe',
		'trang_thai',
		'ngay_tao_phong'
	];

	public function rap_phim()
	{
		return $this->belongsTo(RapPhim::class, 'ma_rap');
	}

	public function ghe_ngois()
	{
		return $this->hasMany(GheNgoi::class, 'ma_phong');
	}

	public function suat_chieus()
	{
		return $this->hasMany(SuatChieu::class, 'ma_phong');
	}

	public function isActive()
	{
		return $this->trang_thai === true;
	}

	public function calculateTotalSeats()
	{
		return $this->so_hang * $this->so_cot;
	}
}
