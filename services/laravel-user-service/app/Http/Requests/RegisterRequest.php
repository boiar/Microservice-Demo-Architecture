<?php

namespace App\Http\Requests;

use App\DTOs\RegisterUserDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ];
    }


    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return void
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                 'status' => 'error',
                 'code'   => 400,
                 'errors' => $validator->errors()->all(),
            ], 400)
        );
    }

    public function getDto(): RegisterUserDTO
    {
        $dto = new RegisterUserDTO();

        $dto
            ->setName($this->input('name'))
            ->setEmail($this->input('email'))
            ->setPassword($this->input('password'))
        ;

        return $dto;
    }
}
