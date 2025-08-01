<?php

namespace App\Contracts\Services;



use App\DTOs\LoginUserDTO;
use App\DTOs\RegisterUserDTO;
use Illuminate\Http\Request;

interface IAuthService
{
    /**
     * @param RegisterUserDTO $dto
     * @return array<string, mixed>
     */
    public function register(RegisterUserDTO $dto): object;

    public function login(LoginUserDTO $dto): object;

    public function refreshToken(Request $request): object;
}
