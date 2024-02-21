<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthenticateController;
use App\Http\Controllers\Api\V1\ThongBaoController;
use App\Http\Controllers\Api\V1\LoaiThongBaoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::group(['namespace' => 'API'], function () {
    Route::group(['prefix' => 'v1', 'namespace' => 'V1'], function () {

        Route::group(['middleware' => []], function () {
            Route::post('signin', [AuthenticateController::class, 'singIn']);
        });

        Route::group(['middleware' => ['auth:api']], function () {
            Route::get('thong-bao', [ThongBaoController::class, 'index']);
            Route::get('thong-bao/them', [ThongBaoController::class, 'getMore']);
            Route::post('thong-bao/doc', [ThongBaoController::class, 'read']);
            Route::get('thong-bao/{id}', [ThongBaoController::class, 'details']);
            Route::get('loai-thong-bao', [LoaiThongBaoController::class, 'index']);
        });
    });
});
