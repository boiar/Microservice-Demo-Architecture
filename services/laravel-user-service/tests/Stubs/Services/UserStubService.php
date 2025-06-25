<?php

namespace Tests\Stubs\Services;

use App\Contracts\IUser;
use App\DTOs\UpdateUserProfileDTO;
use App\Helpers\ResponseHelper;
use App\Models\User;

class UserStubService implements IUser
{

    protected static ?User $authenticatedUser = null;

    public static function setAuthenticatedUser(User $user): void
    {
        self::$authenticatedUser = $user;
    }

    public function updateProfile(UpdateUserProfileDTO $dto): object
    {
        $user = self::$authenticatedUser;

        if (!$user) {
            return ResponseHelper::returnError(401, 'Unauthorized');
        }

        $user->name = $dto->getName();

        if ($dto->getPassword()) {
            $user->password = $dto->getPassword();
        }

        self::$authenticatedUser = $user;

        return ResponseHelper::returnData([
              'user' => $user,
        ], 200, 'Profile updated successfully');
    }
}
