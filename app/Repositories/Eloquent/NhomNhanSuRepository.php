<?php

namespace App\Repositories\Eloquent;

use App\Models\NhomNhanSu;
use App\Repositories\Interface\NhomNhanSuRepositoryInterface;

class NhomNhanSuRepository extends BaseRepository implements NhomNhanSuRepositoryInterface
{
    protected array $querySearchTargets = ['ten', 'user_ids'];
    public function getBlankModel()
    {
        return new NhomNhanSu();
    }
}
