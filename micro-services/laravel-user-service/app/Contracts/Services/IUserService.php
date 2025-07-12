<?php

namespace App\Contracts\Services;



use App\DTOs\UpdateUserProfileDTO;

interface IUserService
{
    public function updateProfile(UpdateUserProfileDTO $dto): object;

}
