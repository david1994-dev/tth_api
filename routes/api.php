<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthenticateController;

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

        Route::middleware('auth:api')->get('/demo', function (Request $request) {
            dd('da login');
        });
    });
});
