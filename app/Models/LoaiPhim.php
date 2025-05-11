<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LoaiPhim
 * 
 * @property int $ma_loai
 * @property string $ten_loai
 * 
 * @property Collection|PhimLoai[] $phim_loais
 *
 * @package App\Models
 */
class LoaiPhim extends Model
{
	protected $table = 'loai_phim';
	protected $primaryKey = 'ma_loai';
	public $timestamps = false;

	protected $fillable = [
		'ten_loai'
	];

	public function phim_loais()
	{
		return $this->hasMany(PhimLoai::class, 'ma_loai');
	}
}
