<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class NguoiDung
 *
 * @property int $ma_nguoi_dung
 * @property string $ho_ten
 * @property string $email
 * @property string $mat_khau
 * @property string $sdt
 * @property string $ma_vai_tro
 * @property string $ma_quan_ly
 * @property string $ma_rap
 * @property string $anh_nguoi_dung
 * @property Carbon $ngay_sinh
 * @property Carbon $ngay_tao_nd
 *
 * @property Collection|DatVe[] $dat_ves
 *
 * @package App\Models
 */
class NguoiDung extends Authenticatable
{
	use HasApiTokens, HasFactory, Notifiable;

	protected $table = 'nguoi_dung';
	protected $primaryKey = 'ma_nguoi_dung';
	public $timestamps = false;

	protected $casts = [
		'ngay_sinh' => 'datetime',
		'ngay_tao_nd' => 'datetime',
		'ma_vai_tro' => 'integer',
		'ma_quan_ly' => 'integer',
		'ma_rap' => 'integer',
	];

	protected $fillable = [
		'ho_ten',
		'email',
		'mat_khau',
		'sdt',
		'ma_vai_tro',
		'ma_quan_ly',
		'ma_rap',
		'anh_nguoi_dung',
		'ngay_sinh',
		'ngay_tao_nd',
		'trang_thai'
	];

	protected $hidden = [
		'mat_khau',
		'remember_token',
	];

	public function getAuthPassword()
	{
		return $this->mat_khau;
	}

	public function dat_ves()
	{
		return $this->hasMany(DatVe::class, 'ma_nguoi_dung');
	}

	public function vaiTro()
	{
		return $this->belongsTo(VaiTro::class, 'ma_vai_tro', 'ma_vai_tro');
	}

	public function rapPhim()
	{
		return $this->belongsTo(RapPhim::class, 'ma_rap', 'ma_rap');
	}

	public function quanLy()
	{
		return $this->belongsTo(NguoiDung::class, 'ma_quan_ly', 'ma_nguoi_dung');
	}

	public function nhanViens()
	{
		return $this->hasMany(NguoiDung::class, 'ma_quan_ly', 'ma_nguoi_dung');
	}

	public function isAdmin()
	{
		return $this->ma_vai_tro === 'admin';
	}

	public function isManager()
	{
		return $this->ma_vai_tro === 'quan_ly';
	}

	public function isStaff()
	{
		return $this->ma_vai_tro === 'nhan_vien';
	}

	public function isCustomer()
	{
		return $this->ma_vai_tro === 'khach_hang';
	}

	public function isActive()
	{
		return $this->trang_thai === 'hoat_dong';
	}

	public function isInactive()
	{
		return $this->trang_thai === 'khoa';
	}

	public function canManageUsers()
	{
		return $this->isAdmin() || $this->isManager();
	}

	public function canManageMovies()
	{
		return $this->isAdmin() || $this->isManager();
	}

	public function canManageTheaters()
	{
		return $this->isAdmin() || $this->isManager();
	}

	public function canManageStaff()
	{
		return $this->isManager();
	}

	public function canBookTickets()
	{
		return $this->isActive() && ($this->isCustomer() || $this->isStaff());
	}
}
