<?php

namespace Tests\Stubs\Services;

use App\Contracts\IAuth;
use App\DTOs\LoginUserDTO;
use App\DTOs\RegisterUserDTO;
use App\Models\User;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Request;

class AuthStubService implements IAuth
{
    protected static array $users = [];


    public static function getUsers(): array
    {
        return self::$users;
    }

    public function register(RegisterUserDTO $dto): object
    {
        foreach (self::$users as $user) {
            if ($user['email'] === $dto->getEmail()) {
                return ResponseHelper::returnError(409, 'Email already exists');
            }
        }

        // Simulate user creation
        $newUser = [
            'id'    => count(self::$users) + 1,
            'name'  => $dto->getName(),
            'email' => $dto->getEmail(),
            'password' => $dto->getPassword(), // <-- Add this line
        ];

        self::$users[] = $newUser;

        return ResponseHelper::returnData([
          'user'          => new User($newUser),
          'token'         => 'fake.jwt.token',
          'refresh_token' => 'fake.refresh.token',
        ]);
    }

    public function login(LoginUserDTO $dto): object
    {
        foreach (self::$users as $user) {
            if ($user['email'] === $dto->getEmail()) {

                if ($dto->getPassword() === 'password123') {

                    return ResponseHelper::returnData([
                        'user' => new User($user),
                        'token' => 'fake.jwt.token',
                        'refresh_token' => 'fake.refresh.token',
                   ], 200, 'Login successful');

                } else {
                    return ResponseHelper::returnError(401, 'Invalid Credentials');
                }
            }
        }

        return ResponseHelper::returnError(401, 'Invalid Credentials');
    }



    public function refreshToken(Request|\Illuminate\Http\Request $request): object
    {
        return ResponseHelper::returnData([
            'token' => 'refreshed.jwt.token',
        ], 200, 'Token refreshed');
    }
}
