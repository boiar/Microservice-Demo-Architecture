<?php

namespace App\Services;

use App\DTOs\UpdateUserProfileDTO;
use App\Helpers\JwtHelper;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;

class UserService
{

    public function updateProfile(UpdateUserProfileDTO $dto): object
    {
        $user = Auth::user();

        $user->name = $dto->getName();

        if ($dto->getPassword()) {
            $user->password = Hash::make($dto->getPassword());
        }

        $user->save();

        $newToken = JwtHelper::generateToken($user);

        // publish user registered event
        Redis::publish('user.updated_profile', json_encode([
           'token'     => $newToken,
           'timestamp' => now()->toDateTimeString(),
        ]));


        return ResponseHelper::returnData(['user' => $user], 200, 'Profile updated successfully');
    }

}
