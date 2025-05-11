<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VaiTro extends Model
{
    use HasFactory;

    protected $table = 'vai_tro';
    protected $primaryKey = 'ma_vai_tro';
    public $timestamps = false;

    protected $fillable = [
        'ten_vai_tro',
        'mo_ta'
    ];

    public function nguoiDungs()
    {
        return $this->hasMany(NguoiDung::class, 'ma_vai_tro');
    }

    public function quyens()
    {
        return $this->belongsToMany(Quyen::class, 'vai_tro_quyen', 'ma_vai_tro', 'ma_quyen');
    }

    public function hasQuyen($quyen)
    {
        return $this->quyens()->where('ten_quyen', $quyen)->exists();
    }

    // Role constants
    const ADMIN = 'admin';
    const QUAN_LY = 'quan_ly';
    const NHAN_VIEN = 'nhan_vien';
    const KHACH_HANG = 'khach_hang';
}
