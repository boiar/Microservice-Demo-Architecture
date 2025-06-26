<?php

namespace app\Contracts;



use App\DTOs\LoginUserDTO;
use App\DTOs\RegisterUserDTO;
use App\DTOs\UpdateUserProfileDTO;
use Illuminate\Http\Request;

interface IUser
{
    public function updateProfile(UpdateUserProfileDTO $dto): object;

}
