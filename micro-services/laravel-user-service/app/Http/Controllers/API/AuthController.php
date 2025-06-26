<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\AddItemsToCartRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;


class AuthController extends Controller
{

    protected AuthService $authService;


    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }



    public function register(RegisterRequest $request): object
    {
        return $this->authService->register($request->getDto());
    }

    public function login(LoginRequest $request): object
    {
        $dto = $request->getDto();
        return $this->authService->login($dto);
    }
}
