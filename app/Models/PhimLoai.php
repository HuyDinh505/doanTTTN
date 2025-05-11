<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PhimLoai
 *
 * @property int $ma_phim
 * @property int $ma_loai
 *
 * @property Phim $phim
 * @property LoaiPhim $loai_phim
 *
 * @package App\Models
 */
class PhimLoai extends Model
{
    protected $table = 'phim_loai';
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = [
        'ma_phim' => 'int',
        'ma_loai' => 'int'
    ];

    protected $fillable = [
        'ma_phim',
        'ma_loai',

    ];

    public function phim()
    {
        return $this->belongsTo(Phim::class, 'ma_phim');
    }

    public function loai_phim()
    {
        return $this->belongsTo(LoaiPhim::class, 'ma_loai');
    }
}
