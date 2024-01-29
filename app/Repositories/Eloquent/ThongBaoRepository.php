<?php

namespace App\Repositories\Eloquent;

use App\Models\ThongBao;
use App\Repositories\Interface\ThongBaoRepositoryInterface;

class ThongBaoRepository extends BaseRepository implements ThongBaoRepositoryInterface
{
    protected array $querySearchTargets = ['title'];
    public function getBlankModel()
    {
        return new ThongBao();
    }
}
