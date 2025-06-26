<?php

namespace App\Http\Requests;

use App\DTOs\AddItemToCartDTO;
use App\DTOs\RegisterUserDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddItemsToCartRequest extends FormRequest
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
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
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

    public function getDto(): AddItemToCartDTO
    {
        $dto = new AddItemToCartDTO();

        $dto
            ->setProductId($this->input('product_id'))
            ->setQuantity($this->input('quantity'))
        ;

        return $dto;
    }
}
