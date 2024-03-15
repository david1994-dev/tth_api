<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Responses\Api\V1\Response;

class NhanVienController extends Controller
{
    public function profile()
    {
        $user = auth()->user();
        $nhanVien = $user->nhanVien;
        return Response::response(200, $nhanVien->toProfileAPI());
    }
}
