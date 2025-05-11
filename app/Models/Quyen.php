<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quyen extends Model
{
    use HasFactory;

    protected $table = 'quyen';
    protected $primaryKey = 'ma_quyen';
    public $timestamps = false;

    protected $fillable = [
        'ten_quyen',
        'mo_ta'
    ];

    public function vaiTros()
    {
        return $this->belongsToMany(VaiTro::class, 'vai_tro_quyen', 'ma_quyen', 'ma_vai_tro');
    }

    // Permission constants
    const XEM_PHIM = 'xem_phim';
    const THEM_PHIM = 'them_phim';
    const SUA_PHIM = 'sua_phim';
    const XOA_PHIM = 'xoa_phim';
    const XEM_NGUOI_DUNG = 'xem_nguoi_dung';
    const THEM_NGUOI_DUNG = 'them_nguoi_dung';
    const SUA_NGUOI_DUNG = 'sua_nguoi_dung';
    const XOA_NGUOI_DUNG = 'xoa_nguoi_dung';
    const XEM_RAP = 'xem_rap';
    const THEM_RAP = 'them_rap';
    const SUA_RAP = 'sua_rap';
    const XOA_RAP = 'xoa_rap';
    const XEM_THONG_KE = 'xem_thong_ke';
}
