<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ViTriCongViec extends Base
{
    use SoftDeletes;

    protected $table = 'vi_tri_cong_viec';
    protected $fillable = [
        'ten',
        'nguoi_tao_id',
    ];

    protected $casts = [
        'deleted_at' => 'datetime:Y-m-d H:i:s'
    ];
}
