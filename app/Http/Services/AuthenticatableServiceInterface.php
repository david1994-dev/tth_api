<?php

namespace App\Http\Services;

interface AuthenticatableServiceInterface
{
    public function signIn($input);
    public function getToken($user, $type);
}
