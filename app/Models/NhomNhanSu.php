<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class NhomNhanSu extends Base
{
    use SoftDeletes;


    protected $table = 'nhansu_nhom';
    protected $fillable = [
        'ma',
        'ten',
        'slug',
        'user_ids',
        'nguoi_cap_nhat_id'
    ];

    protected $casts = [
        'deleted_at' => 'datetime:Y-m-d H:i:s',
        'user_ids' => 'array'
    ];
}
