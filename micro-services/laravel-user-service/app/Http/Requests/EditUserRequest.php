<?php

namespace App\Http\Requests;

use App\DTOs\EditUserDTO;
use App\DTOs\LoginUserDTO;
use App\DTOs\UpdateUserProfileDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class EditUserRequest extends FormRequest
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
            'name'  => 'required|string|max:255',
            'password' => 'nullable|string|min:6|confirmed',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                 'status' => false,
                 'code'   => 400,
                 'errors' => $validator->errors()->all(),
            ], 400)
        );
    }

    public function getDto(): UpdateUserProfileDTO
    {
        $dto = new UpdateUserProfileDTO();

        $dto
            ->setName($this->input('name'))
            ->setPassword($this->input('password'))
        ;

        return $dto;
    }


}
