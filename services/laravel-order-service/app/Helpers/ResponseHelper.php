<?php

namespace app\Helpers;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;

class ResponseHelper
{

    public static function returnError(int $code, string $msg): JsonResponse
    {
        return response()->json([
            'status' => false,
            'code'   => $code,
            'msg'    => $msg
        ])->setStatusCode($code);
    }


    public static function returnSuccessMessage(string $msg, int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => true,
            'code'   => $code,
            'msg'    => $msg
        ])->setStatusCode($code);
    }


    public static function returnData(array $dataArr, int $code = 200, string $msg = ""): JsonResponse
    {
        return response()->json([
            'status' => true,
            'code'   => $code,
            'msg'    => $msg,
            'data'   => $dataArr
        ])->setStatusCode($code);
    }


    public static function returnValidationError(int $code, Validator $validator): JsonResponse
    {
        $errors = implode(", ", $validator->errors()->all());

        return self::returnError($code, $errors);
    }
}
