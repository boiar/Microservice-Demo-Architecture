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

        \Log::info('[AuthService] About to publish user.updated event');

        // publish user updated event
        Redis::publish('user-events', json_encode([
              'event'     => 'user.updated',
              'timestamp' => now()->toDateTimeString(),
              'token'     => $newToken,
              'data' => [
                  'id'    => $user->id,
                  'name'  => $user->name,
                  'email' => $user->email,
              ],
        ]));;


        return ResponseHelper::returnData(['user' => $user], 200, 'Profile updated successfully');
    }

}
