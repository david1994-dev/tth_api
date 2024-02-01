<?php

namespace App\Repositories\Eloquent;

use App\Models\LoaiThongBao;
use App\Repositories\Interface\LoaiThongBaoRepositoryInterface;

class LoaiThongBaoRepository extends BaseRepository implements LoaiThongBaoRepositoryInterface
{
    protected array $querySearchTargets = ['ten'];
    public function getBlankModel()
    {
        return new LoaiThongBao();
    }
}
