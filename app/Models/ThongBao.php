<?php

namespace App\Models;

use App\Helpers\URLHelper;
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
        'nguoi_gui_id',
        'gui_tat_ca'
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

    public function phanHoi()
    {
        return $this->hasMany(ThongBaoPhanHoi::class, 'thong_bao_id', 'id');
    }

    public function toAPIArray()
    {
        return [
            'id' => $this->id,
            'tieu_de' => $this->tieu_de,
            'muc_do' => Arr::get(self::MUC_DO, $this->muc_do, []),
            'noi_dung' => $this->noi_dung,
            'loai_thong_bao' => $this->loaiThongBao ? $this->loaiThongBao->ten : 'deleted',
            'loai_thong_bao_int' => $this->loai_thong_bao,
            'is_read' => $this->isread,
            'created_at' => $this->created_at->format('Y-m-d h:i:s'),
            'nguoi_gui' => $this->sendFrom,
            'dinh_kem' => $this->dinh_kem ? URLHelper::getFullPathURL($this->dinh_kem) : []
        ];
    }

    public function toDetailArray()
    {
        return [
            'id' => $this->id,
            'nguoi_gui' => $this->sendFrom,
            'nguoi_nhan' => $this->sendTo,
            'ngay_tao' => $this->created_at->format('d-m-Y h:i:s'),
            'nguoi_da_xem' => $this->danhSachNguoiXem,
            'tieu_de' => $this->tieu_de,
            'muc_do' => Arr::get(self::MUC_DO, $this->muc_do, []),
            'noi_dung' => $this->noi_dung,
            'loai_thong_bao' => $this->loaiThongBao ? $this->loaiThongBao->ten : 'deleted',
            'loai_thong_bao_int' => $this->loai_thong_bao,
            'dinh_kem' => $this->dinh_kem ? URLHelper::getFullPathURL($this->dinh_kem) : [],
            'phan_hoi' => $this->phanHoiThongBao
        ];
    }

    public function getSendFromAttribute()
    {
        $user = $this->nguoiGui;
        if (!$user) return 'Không xác định';

        $nhanVien = $user->nhanVien;
        if (!$nhanVien) return 'Không xác định';

        $sendFrom = $nhanVien->ho_ten;
        $phongBan = $nhanVien->phongBan;
        if ($phongBan) {
            $sendFrom = $sendFrom . ' - '. $phongBan->ten;
        }

        return $sendFrom;
    }

    public function getSendToAttribute()
    {
        $sendTo = [];
        if ($this->gui_tat_ca) return 'Toàn bộ công ty';
        $chiNhanhIds = $this->chi_nhanh_ids;
        if ($chiNhanhIds) {
            $chiNhanhs = ChiNhanh::query()->whereIn('id', $chiNhanhIds)->pluck('ten')->toArray();
            $sendTo = array_merge($sendTo, $chiNhanhs);
        }

        $phongBanIds = $this->phong_ban_ids;
        if ($phongBanIds) {
            $phongBans = PhongBan::query()->whereIn('id', $phongBanIds)->pluck('ten')->toArray();
            $sendTo = array_merge($sendTo, $phongBans);
        }

        $nhomNguoiNhans = $this->nhom_nguoi_nhan_ids;
        if ($nhomNguoiNhans) {
            foreach ($nhomNguoiNhans as $nhomNguoiNhanId) {
                $nhomNguoiNhan = NhomNhanSu::query()->find($nhomNguoiNhanId);
                $nNguoiNhan = NhanVien::query()->whereIn('user_id', $nhomNguoiNhan->user_ids)->pluck('ho_ten')->toArray();
                $sendTo = array_merge($sendTo, $nNguoiNhan);
            }
        }

        $nguoiNhanIds = $this->nguoi_nhan_ids;
        if ($nguoiNhanIds) {
            $tenNguoiNhan = NhanVien::query()->whereIn('user_id', $nguoiNhanIds)->pluck('ho_ten')->toArray();
            $sendTo = array_merge($sendTo, $tenNguoiNhan);
        }

        $sendTo = array_unique($sendTo);

        return implode(',', $sendTo);
    }

    public function getDanhSachNguoiXemAttribute()
    {
        $nguoiXemIds = ThongBaoUser::query()
            ->where('thong_bao_id', $this->id)
            ->where('status', ThongBaoUser::STATUS_DA_DOC)
            ->pluck('user_id')
            ->toArray();

        return NhanVien::query()->whereIn('user_id', $nguoiXemIds)->pluck('ho_ten')->toArray();
    }

    public function getPhanHoiThongBaoAttribute()
    {
        $user = auth()->user();
        $phanHoi = $this->phanHoi()
            ->where('gui_tat_ca', true)
            ->orWhereJsonContains('nguoi_nhan_ids', $user->id)
            ->get();

        $results = [];
        foreach ($phanHoi as $ph) {
            if ($ph->gui_tat_ca) {
                $sendTo = $this->sendTo;
            } else {
                $nguoiNhanIds = $ph->nguoi_nhan_ids ?? [];
                $sendTo = NhanVien::query()->whereIn('user_id', $nguoiNhanIds)->pluck('ho_ten')->toArray();
                $sendTo = implode(',', $sendTo);
            }

            $result = [
                'nguoi_phan_hoi' => $ph->nguoiGui->nhanVien->ho_ten,
                'nguoi_nhan' => $sendTo,
                'thoi_gian_phan_hoi' => $ph->created_at->format('Y-m-d h:i:s'),
                'noi_dung' => $ph->noi_dung,
                'dinh_kem' => $ph->dinh_kem ? URLHelper::getFullPathURL($ph->dinh_kem) : []
            ];

            $results[] = $result;
        }

        return $results;
    }
}
