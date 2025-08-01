<?php

namespace App\Http\Requests;

use App\DTOs\AddItemToCartDTO;
use App\DTOs\CreateOrderDTO;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateOrderRequest extends FormRequest
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
            'address' => 'required|string|max:255',
            'note'    => 'nullable|string',
        ];
    }


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

    public function getDto(): CreateOrderDTO
    {
        $dto = new CreateOrderDTO();

        $dto
            ->setAddress($this->input('address'))
            ->setNotes($this->input('notes'))
        ;

        return $dto;
    }
}
