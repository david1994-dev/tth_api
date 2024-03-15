<?php

namespace App\Repositories\Eloquent;

use App\Models\ThongBao;
use App\Models\ThongBaoUser;
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

    private function buildQuery($user, $filter)
    {
        $nhanVien = $user->nhanVien;
        $nhomNhanSu = $user->nhomNhanSu;
        $query = $this->getBlankModel()
            ->where('thong_bao.id', '>', $user->last_notification_id)
            ->where('thong_bao.xuat_ban', true)

            ->where(function ($qr) use ($nhanVien, $nhomNhanSu) {
                $qr->where('thong_bao.gui_tat_ca', true)
                    ->orWhere(function ($q) use ($nhanVien, $nhomNhanSu) {
                        $q->whereJsonContains('thong_bao.chi_nhanh_ids', $nhanVien->chi_nhanh_id)
                            ->orWhereJsonContains('thong_bao.phong_ban_ids', $nhanVien->phong_ban_id)
                            ->orWhereJsonContains('thong_bao.nguoi_nhan_ids', $nhanVien->user_id);

                        foreach ($nhomNhanSu as $nnsId) {
                            $q->orWhereJsonContains('thong_bao.nhom_nguoi_nhan_ids', $nnsId);
                        }
                    });
            });

        if (!empty($filter['last_id'])) {
            if ($filter['is_new']) {
                $query = $query->where('thong_bao.id', '>', $filter['last_id']);
            } else {
                $query = $query->where('thong_bao.id', '<', $filter['last_id']);
            }
        }

        if (!empty($filter['category'])) {
            $query = $query->where('thong_bao.loai_thong_bao', $filter['category']);
        }

        if (!empty($filter['created_at_from'])) {
            $query = $query->where('thong_bao.created_at', '>=', Carbon::parse($filter['created_at_from'])->startOfDay());
        }

        if (!empty($filter['created_at_to'])) {
            $query = $query->where('thong_bao.created_at', '<=', Carbon::parse($filter['created_at_to'])->endOfDay());
        }

        if (!empty($filter['query'])) {
            $query = $query->where('thong_bao.tieu_de', 'LIKE', '%'.$filter['query'].'%');
        }

        return $query;
    }

    public function getNotifications($user, $filter, $order, $direction, $offset, $limit)
    {
        $userId = $user->id;
        $stRead = ThongBaoUser::STATUS_DA_DOC;

        $query = $this->buildQuery($user, $filter);
        $query->select([
            'thong_bao.*',
            DB::raw("EXISTS (SELECT 1 FROM thong_bao_users WHERE thong_bao.id = thong_bao_users.thong_bao_id AND thong_bao_users.user_id = $userId AND thong_bao_users.status = $stRead) AS isRead")
        ]);

        if ($order && $direction) {
            $query = $query->orderBy($order, $direction);
        }

        if ($offset) {
            $query = $query->skip($offset);
        }

        if ($limit) {
            $query = $query->take($limit);
        }

        return $query->get();
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
