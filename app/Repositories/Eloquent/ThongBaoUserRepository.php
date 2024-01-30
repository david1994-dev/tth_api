<?php

namespace App\Repositories\Eloquent;

use App\Models\ThongBaoUser;
use App\Repositories\Interface\ThongBaoUserRepositoryInterface;

class ThongBaoUserRepository extends BaseRepository implements ThongBaoUserRepositoryInterface
{
    protected array $querySearchTargets = [];
    public function getBlankModel()
    {
        return new ThongBaoUser();
    }
}
