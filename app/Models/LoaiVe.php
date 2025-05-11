<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LoaiVe
 * 
 * @property int $ma_loai_ve
 * @property string $ten_loai_ve
 * @property float $gia_ve
 * 
 * @property Collection|VeDat[] $ve_dats
 *
 * @package App\Models
 */
class LoaiVe extends Model
{
	protected $table = 'loai_ve';
	protected $primaryKey = 'ma_loai_ve';
	public $timestamps = false;

	protected $casts = [
		'gia_ve' => 'float'
	];

	protected $fillable = [
		'ten_loai_ve',
		'gia_ve'
	];

	public function ve_dats()
	{
		return $this->hasMany(VeDat::class, 'ma_loai_ve');
	}
}
