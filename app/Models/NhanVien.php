<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;

class NhanVien extends Base
{
    use SoftDeletes;

    protected $table = 'nhanvien';
    const GIOI_TINH_NU = 0;
    const GIOI_TINH_NAM = 1;
    const GIOI_TINH_LGBT = 2;

    const GIOI_TINH = [
        self::GIOI_TINH_NU => 'Nữ',
        self::GIOI_TINH_NAM => 'Nam',
        self::GIOI_TINH_LGBT => 'LGBT',
    ];

    public function phongBan()
    {
        return $this->hasOne(PhongBan::class, 'id', 'phong_ban_id');
    }

    protected $fillable = [
        'ma',
        'user_id',
        'ho_ten',
        'email',
        'dien_thoai_cong_viec',
        'gioi_tinh',
        'loai_nhan_vien_id',
        'ngay_sinh',
        'chi_nhanh_id',
        'phong_ban_id',
    ];

    protected $casts = [
        'chi_tiet' => 'array',
        'deleted_at' => 'datetime:Y-m-d H:i:s',
        'ngay_sinh' => 'date'
    ];
}
