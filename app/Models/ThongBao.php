<?php

namespace App\Models;

use Illuminate\Support\Arr;

class ThongBao extends Base
{
    const MUC_DO_BINH_THUONG = 1;
    const MUC_DO_KHAN = 2;
    const MUC_DO_MAT = 3;

    const MUC_DO = [
        self::MUC_DO_BINH_THUONG => 'Bình Thường',
        self::MUC_DO_KHAN => 'Khẩn',
        self::MUC_DO_MAT => 'Mật'
    ];

    protected $table = 'thong_bao';
    protected $fillable = [
        'slug',
        'tieu_de',
        'chi_nhanh_ids',
        'phong_ban_ids',
        'nhom_nguoi_nhan_ids',
        'nguoi_nhan_ids',
        'loai_thong_bao',
        'muc_do',
        'noi_dung',
        'dinh_kem',
        'xuat_ban',
        'nguoi_gui_id'
    ];

    protected $casts = [
        'chi_nhanh_ids' => 'array',
        'phong_ban_ids' => 'array',
        'nhom_nguoi_nhan_ids' => 'array',
        'nguoi_nhan_ids' => 'array',
        'dinh_kem' => 'array',
        'gui_tat_ca' => 'boolean',
        'xuat_ban' => 'boolean',
        'deleted_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function userRead()
    {
        return $this->hasMany(ThongBaoUser::class, 'thong_bao_id', 'id');
    }

    public function loaiThongBao()
    {
        return $this->hasOne(LoaiThongBao::class, 'id', 'loai_thong_bao');
    }

    public function toAPIArray()
    {
        $user = auth()->user();
        if (!$user) {
            $isRead = false;
        } else {
            $userId = $user->id;
            $status = ThongBaoUser::STATUS_DA_DOC;
            $isRead = $this->userRead->contains(function ($val, $key) use ($userId, $status) {
                return $val->user_id == $userId && $val->status == $status;
            });
        }

        return [
            'id' => $this->id,
            'tieu_de' => $this->tieu_de,
            'muc_do' => Arr::get(self::MUC_DO, $this->muc_do, ''),
            'noi_dung' => $this->noi_dung,
            'loai_thong_bao' => $this->loaiThongBao->ten,
            'dinh_kem' => $this->dinh_kem ?? [],
            'is_read' => $isRead
        ];
    }
}
