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
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'images' => $this->images ?? [],
            'is_read' => $this->userRead()->where('user_id', $this->id)->exists()
        ];
    }
}
