<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ThongBaoPhanHoi extends Base
{
    protected $table = 'thong_bao_phan_hoi';
    use SoftDeletes;

    protected $fillable = [
        'thong_bao_id',
        'gui_tat_ca',
        'nguoi_nhan_ids',
        'noi_dung',
        'dinh_kem',
        'nguoi_gui_id',
    ];

    protected $casts = [
        'nguoi_nhan_ids' => 'array',
        'dinh_kem' => 'array',
        'gui_tat_ca' => 'boolean',
        'deleted_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function nguoiGui()
    {
        return $this->hasOne(User::class, 'id', 'nguoi_gui_id');
    }
}
