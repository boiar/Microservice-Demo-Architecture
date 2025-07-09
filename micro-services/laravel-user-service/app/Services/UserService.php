<?php

namespace App\Services;

use App\Contracts\Repositories\IUserRepository;
use App\Contracts\Services\IUser;
use App\DTOs\UpdateUserProfileDTO;
use App\Helpers\JwtHelper;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class UserService implements IUser
{
    protected IUserRepository $userRepo;

    public function __construct(IUserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function updateProfile(UpdateUserProfileDTO $dto): object
    {
        $user = Auth::user();

        $updateData = [
            'name' => $dto->getName(),
        ];

        if ($dto->getPassword()) {
            $updateData['password'] = Hash::make($dto->getPassword());
        }

        $updatedUser = $this->userRepo->update($user->id, $updateData);

        $newToken = JwtHelper::generateToken($updatedUser);

        Log::info('[UserService] About to publish user.updated event');

        // publish user updated event
        Redis::publish('user-events', json_encode([
              'event'     => 'user.updated',
              'timestamp' => now()->toDateTimeString(),
              'token'     => $newToken,
              'data' => [
                  'id'    => $updatedUser->id,
                  'name'  => $updatedUser->name,
                  'email' => $updatedUser->email,
              ],
        ]));


        return ResponseHelper::returnData(['user' => $updatedUser], 200, 'Profile updated successfully');
    }

}
