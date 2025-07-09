<?php

namespace App\Contracts\Services;



use App\DTOs\UpdateUserProfileDTO;

interface IUser
{
    public function updateProfile(UpdateUserProfileDTO $dto): object;

}
