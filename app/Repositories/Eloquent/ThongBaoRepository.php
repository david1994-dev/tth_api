<?php

namespace App\Repositories\Eloquent;

use App\Models\ThongBao;
use App\Repositories\Interface\ThongBaoRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ThongBaoRepository extends BaseRepository implements ThongBaoRepositoryInterface
{
    protected array $querySearchTargets = ['tieu_de'];
    public function getBlankModel()
    {
        return new ThongBao();
    }

    public function countByType($user, $filter)
    {
        $query = $this->buildQuery($user, $filter);

        return $query->select('loai_thong_bao', DB::raw('count(*) as total'))
            ->groupBy('loai_thong_bao')
            ->pluck('total', 'loai_thong_bao')->toArray();
    }

    private function buildQuery($user, $filter, $order=null, $direction=null, $offset=null, $limit=null)
    {
        $nhanVien = $user->nhanVien;
        $nhomNhanSu = $user->nhomNhanSu;
        $query = $this->getBlankModel()
            ->where('id', '>', $user->last_notification_id)
            ->where('xuat_ban', true)
            ->where(function ($qr) use ($nhanVien, $nhomNhanSu) {
                $qr->where('gui_tat_ca', true)
                    ->orWhere(function ($q) use ($nhanVien, $nhomNhanSu) {
                        $q->whereJsonContains('chi_nhanh_ids', $nhanVien->chi_nhanh_id)
                            ->orWhereJsonContains('phong_ban_ids', $nhanVien->phong_ban_id)
                            ->orWhereJsonContains('nhom_nguoi_nhan_ids', $nhomNhanSu)
                            ->orWhereJsonContains('nguoi_nhan_ids', $nhanVien->user_id);
                    });
            });

        if (!empty($filter['last_id'])) {
            if ($filter['is_new']) {
                $query = $query->where('id', '>', $filter['last_id']);
            } else {
                $query = $query->where('id', '<', $filter['last_id']);
            }
        }

        if (!empty($filter['category'])) {
            $query = $query->where('loai_thong_bao', $filter['category']);
        }

        if (!empty($filter['created_at_from'])) {
            $query = $query->where('created_at', '>=', Carbon::parse($filter['created_at_from']));
        }

        if (!empty($filter['created_at_to'])) {
            $query = $query->where('created_at', '<=', Carbon::parse($filter['created_at_to']));
        }

        if (!empty($filter['query'])) {
            $query = $query->where('tieu_de', 'LIKE', '%'.$filter['query'].'%');
        }

        if ($order && $direction) {
            $query = $query->orderBy($order, $direction);
        }

        if ($offset) {
            $query = $query->skip($offset);
        }

        if ($limit) {
            $query = $query->take($limit);
        }

        return $query;
    }

    public function getNotifications($user, $filter, $order, $direction, $offset, $limit)
    {
        return $this->buildQuery($user, $filter, $order, $direction, $offset, $limit)->get();
    }

    public function countNotifications($user, $filter)
    {
        return $this->buildQuery($user, $filter)->count();
    }

    public function getMore($user, $filter, $order, $direction)
    {
        return $this->buildQuery($user, $filter, $order, $direction)->get();
    }
}
