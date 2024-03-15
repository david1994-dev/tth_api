<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Responses\API\V1\Response;
use App\Http\Services\AuthenticatableServiceInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthenticateController extends Controller
{
    private AuthenticatableServiceInterface $authenticatableService;

    public function __construct(AuthenticatableServiceInterface $authenticatableService)
    {
        $this->authenticatableService = $authenticatableService;
    }
    public function singIn(Request $request)
    {
        $input = $request->only(['email', 'password']);
        $isSuccess = $this->authenticatableService->signIn($input);
        if (!$isSuccess) {
            return Response::response(40101);
        }

        $user = auth()->user();
        $accessToken = $this->authenticatableService->getToken($user, 'Personal Access Token');

        return Response::response(200, [
            'access_token' => $accessToken->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $accessToken->token->expires_at
            )->toDateTimeString()
        ]);
    }

    public function changePassword(Request $request)
    {
        $user = auth()->user();
        $input = $request->only(['current_password', 'new_password', 're_password']);

        if (!Hash::check($input['current_password'], $user->password)) {
            return Response::response(40102);
        }

        if ($input['new_password'] != $input['re_password']) {
            return Response::response(40003);
        }

        $user->password = Hash::make($input['new_password']);
        $user->save();

        return Response::response(200);
    }
}
