<?php

namespace App\Repositories\Interface;

interface ThongBaoRepositoryInterface extends BaseRepositoryInterface
{
    public function countByType($user, $filter);
    public function getNotifications($user, $filter, $order, $direction, $offset, $limit);
    public function countNotifications($user, $filter);
    public function getMore($user, $filter, $order, $direction);
}
