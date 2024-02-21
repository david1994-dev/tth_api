<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ChiNhanh extends Base
{
    use SoftDeletes;

    const TRANGTHAI_DANG_HOAT_DONG = 1;

    protected $table = 'chi_nhanh';
    protected $fillable = [
        'ma',
        'ten',
        'slug',
        'trang_thai',
        'nguoi_cap_nhat_id',
    ];

    protected $casts = [
        'deleted_at' => 'datetime:Y-m-d H:i:s'
    ];
}
