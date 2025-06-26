<?php

namespace App\Contracts;



use App\DTOs\LoginUserDTO;
use App\DTOs\RegisterUserDTO;
use Illuminate\Http\Request;

interface IAuth
{
    public function register(RegisterUserDTO $dto): object;

    public function login(LoginUserDTO $dto): object;

    public function refreshToken(Request $request): object;
}
