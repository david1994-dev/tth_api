<?php

namespace App\Models;

class ThongBao extends Base
{
    const RECEIVE_ALL = 0;

    protected $table = 'thong_bao';
    protected $fillable = [
        'receive_id',
        'title',
        'content',
        'images'
    ];

    protected $casts = [
        'images' => 'array'
    ];

    public function userRead()
    {
        return $this->hasMany(ThongBaoUser::class, 'thong_bao_id', 'id');
    }

    public function toAPIArray()
    {
        $user = auth()->user();
        if (!$user) {
            $isRead = false;
        } else {
            $userId = $user->id;
            $status = ThongBaoUser::STATUS_DA_DOC;
            $isRead = $this->userRead->contains(function ($val, $key) use ($userId, $status) {
                return $val->user_id == $userId && $val->status == $status;
            });
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'images' => $this->images ?? [],
            'is_read' => $isRead
        ];
    }
}
