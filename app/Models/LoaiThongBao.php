<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class LoaiThongBao extends Base
{
    use SoftDeletes;

    protected $table = 'loai_thong_bao';
    protected $fillable = [
        'nguoi_tao_id',
        'ten'
    ];

    protected $casts = [
        'deleted_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function toAPIArray()
    {
        return [
            'name' => $this->ten,
            'id' => $this->id
        ];
    }
}
