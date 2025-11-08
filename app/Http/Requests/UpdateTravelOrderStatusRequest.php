<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @OA\Schema(
 *     schema="UpdateTravelOrderStatusRequest",
 *     required={"status"},
 *     @OA\Property(property="status", type="string", enum={"solicitado", "aprovado", "cancelado"}, example="solicitado")
 * )
 */
class UpdateTravelOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|string|in:solicitado,aprovado,cancelado',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'O campo status é obrigatório.',
            'status.string' => 'O campo status deve ser uma string.',
            'status.in' => 'O status deve ser um dos seguintes valores: solicitado, aprovado, cancelado.',
        ];
    }

    public function attributes(): array
    {
        return [
            'status' => 'status',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Status inválido',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}

