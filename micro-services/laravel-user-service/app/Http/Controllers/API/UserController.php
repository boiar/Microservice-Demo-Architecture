<?php
namespace app\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\EditUserRequest;
use App\Services\UserService;


class UserController extends Controller
{

    protected UserService $userService;


    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }


    public function updateProfile(EditUserRequest $request): object
    {
        $dto = $request->getDto();
        return $this->userService->updateProfile($dto);
    }



}
