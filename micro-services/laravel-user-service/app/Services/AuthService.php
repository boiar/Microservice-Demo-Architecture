<?php

// app/Services/AuthService.php

namespace App\Services;

use App\Contracts\Repositories\IUserRepository;
use App\Contracts\Services\IAuth;
use App\DTOs\LoginUserDTO;
use App\DTOs\RegisterUserDTO;
use App\Helpers\JwtHelper;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;


class AuthService implements IAuth
{
    protected IUserRepository $userRepo;

    public function __construct(IUserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }


    public function register(RegisterUserDTO $dto): object
    {
        if ($this->userRepo->getBy('email', '=', $dto->getEmail())->isNotEmpty()) {
            return response()->json([
                'msg' => 'Email already exists',
                'code' => 409
            ], 409);
        }

        $user = $this->userRepo->create([
             'name'     => $dto->getName(),
             'email'    => $dto->getEmail(),
             'password' => $dto->getPassword(),
        ]);


        $token = JwtHelper::generateToken($user);

        $refreshToken = JwtHelper::generateRefreshToken($user->id);
        Cache::put("refresh_{$refreshToken}", $user->id, now()->addDays(7));


        \Log::info('[AuthService] About to publish user.registered event');


        // publish user registered event
        $result = Redis::publish('user-events', json_encode([
              'event'     => 'user.registered',
              'timestamp' => now()->toDateTimeString(),
              'token'     => $token,
              'data' => [
                  'id'    => $user->id,
                  'name'  => $user->name,
                  'email' => $user->email,
              ],
        ]));

        \Log::info("[AuthService] Redis publish result: $result");

        $data = [
            'user'          => $user,
            'token'         => $token,
            'refresh_token' => $refreshToken,
        ];

        return ResponseHelper::returnData($data);
    }


    public function login(LoginUserDTO $dto): object
    {
        $user = $this->userRepo->getBy('email', '=', $dto->getEmail())->first();


        if (!$user || !Hash::check($dto->getPassword(), $user->password)) {
            return ResponseHelper::returnError(401, 'Invalid email or password');
        }


        $token = JwtHelper::generateToken($user);
        $refreshToken = JwtHelper::generateRefreshToken($user->id);

        Cache::put("refresh_{$refreshToken}", $user->id, now()->addDays(7));

        return ResponseHelper::returnData([
              'user'          => $user,
              'token'         => $token,
              'refresh_token' => $refreshToken,
        ], 200, 'Login successful');
    }

    public function refreshToken(Request $request): object
    {

        $refreshToken = $request->input('refresh_token');

        if (!$refreshToken) {
            return ResponseHelper::returnError(400, 'Refresh token is required');
        }

        $userId = Cache::get("refresh_{$refreshToken}");

        if (!$userId) {
            return ResponseHelper::returnError(401, 'Invalid or expired refresh token');
        }

        $user = $this->userRepo->findById($userId);

        if (!$user) {
            return ResponseHelper::returnError(404, 'User not found');
        }

        // Generate new access token
        $token = JwtHelper::generateToken($user);

        return ResponseHelper::returnData([
              'token' => $token,
        ], 200, 'Token refreshed successfully');

    }

}
