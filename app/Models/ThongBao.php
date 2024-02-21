<?php

namespace App\Models;

use Illuminate\Support\Arr;

class ThongBao extends Base
{
    const MUC_DO_BINH_THUONG = 1;
    const MUC_DO_KHAN = 2;
    const MUC_DO_MAT = 3;

    const MUC_DO = [
        self::MUC_DO_BINH_THUONG => [
            'id' => self::MUC_DO_BINH_THUONG,
            'name' => 'Bình Thường',
            'color' => '0xff8f8f8f'
        ],
        self::MUC_DO_KHAN => [
            'id' => self::MUC_DO_KHAN,
            'name' => 'Khẩn',
            'color' => '0xFFC2002F'
        ],
        self::MUC_DO_MAT => [
            'id' => self::MUC_DO_MAT,
            'name' => 'Mật',
            'color' => '0xFF9575CD'
        ],
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

    public function nguoiGui()
    {
        return $this->hasOne(User::class, 'id', 'nguoi_gui_id');
    }

    public function toAPIArray()
    {
        $user = auth()->user();
        if (!$user) {
            $isRead = false;
        } else {
            $isRead = ThongBaoUser::query()
                ->where('user_id', $user->id)
                ->where('thong_bao_id', $this->id)
                ->where('status', ThongBaoUser::STATUS_DA_DOC)
                ->exists();
        }

        return [
            'id' => $this->id,
            'tieu_de' => $this->tieu_de,
            'muc_do' => Arr::get(self::MUC_DO, $this->muc_do, []),
            'noi_dung' => $this->noi_dung,
            'loai_thong_bao' => $this->loaiThongBao ? $this->loaiThongBao->ten : 'deleted',
            'loai_thong_bao_int' => $this->loai_thong_bao,
            'is_read' => $isRead,
            'created_at' => $this->created_at->format('Y-m-d h:i:s'),
            'details' => 'https://dieuhanh1.tthgroup.vn:81/thongbaonoidung/so-46qd-tth-ve-viec-tham-gia-doi-van-nghe-khoi-vp-tct20240220101007',
            'nguoi_gui' => $this->nguoiGui->nhanVien->ho_ten . ' - ' . $this->nguoiGui->nhanVien->phongBan->ten
        ];
    }

    public function toDetailArray()
    {
        $nguoiGui = $this->nguoiGui;
        $nhaVienGui = $nguoiGui->nhanVien;
        return [
        ];
    }
}
