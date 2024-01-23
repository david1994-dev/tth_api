<?php

namespace App\Http\Services\Production;

use App\Http\Services\AuthenticatableServiceInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class AuthenticatableService implements AuthenticatableServiceInterface
{
    public function signIn($input)
    {
        $rememberMe = (bool) Arr::get($input, 'remember_me', 0);
        $guard = $this->getGuard();
        if (!$guard->attempt(['email' => $input['email'], 'password' => $input['password']], $rememberMe, true)) {
            return false;
        }

        return $guard->user();
    }

    protected function getGuard()
    {
        return Auth::guard();
    }

    public function getToken($user, $type)
    {
        if (!$user) {
            return null;
        }

        return $user->createToken($type);
    }
}
