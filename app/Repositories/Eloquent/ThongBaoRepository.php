<?php

namespace App\Repositories\Eloquent;

use App\Models\ThongBao;
use App\Repositories\Interface\ThongBaoRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ThongBaoRepository extends BaseRepository implements ThongBaoRepositoryInterface
{
    protected array $querySearchTargets = ['title'];
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
        //todo filter
        $nhanVien = $user->nhanVien;
        $query = $this->getBlankModel()
            ->where('id', '>', max($user->last_notification_id, $filter['last_id']))
            ->where('xuat_ban', true)
            ->where(function ($qr) use ($nhanVien) {
                $qr->where('gui_tat_ca', true)
                    ->orWhere(function ($q) use ($nhanVien) {
                        $q->whereJsonContains('chi_nhanh_ids', $nhanVien->chi_nhanh_id)
                            ->orWhereJsonContains('phong_ban_ids', $nhanVien->phong_ban_id)
                            ->orWhereJsonContains('phong_ban_ids', $nhanVien->phong_ban_id)
                            ->orWhereJsonContains('nguoi_nhan_ids', $nhanVien->user_id);
                    });
            });

        if ($order && $direction) {
            $query = $query->orderBy($order, $direction);
        }

        if ($offset && $limit) {
            $query = $query->skip($offset)->take($limit);
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
