<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;


class DatVe extends Model
{
    protected $table = 'dat_ve';
    protected $primaryKey = 'ma_ve';
    public $incrementing = false;
    public $timestamps = false;

    protected $casts = [
        'ma_nguoi_dung' => 'int',
        'ma_suat_chieu' => 'int',
        'tong_gia_tien' => 'decimal:2',
        'tong_so_ve' => 'int',
        'ngay_dat_ve' => 'datetime'
    ];

    protected $fillable = [
        'ma_nguoi_dung',
        'ma_suat_chieu',
        'tong_gia_tien',
        'tong_so_ve',
        'trang_thai',
        'ngay_dat_ve'
    ];

    public function suat_chieu()
    {
        return $this->belongsTo(SuatChieu::class, 'ma_suat_chieu');
    }

    public function nguoi_dung()
    {
        return $this->belongsTo(NguoiDung::class, 'ma_nguoi_dung');
    }

    public function chi_tiet_dvs()
    {
        return $this->hasMany(ChiTietDv::class, 'ma_ve');
    }



    public function ve_dats()
    {
        return $this->hasMany(VeDat::class, 'ma_ve');
    }
}
