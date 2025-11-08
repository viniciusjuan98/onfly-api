<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="CreateTravelOrderRequest",
 *     required={"requester_name", "destination", "departure_date", "return_date"},
 *     @OA\Property(property="requester_name", type="string", example="John Doe"),
 *     @OA\Property(property="destination", type="string", example="New York"),
 *     @OA\Property(property="departure_date", type="string", format="date", example="2025-12-01"),
 *     @OA\Property(property="return_date", type="string", format="date", example="2025-12-10")
 * )
 */
class CreateTravelOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'requester_name' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'departure_date' => 'required|date|after_or_equal:today',
            'return_date' => 'required|date|after:departure_date',
        ];
    }

    public function messages(): array
    {
        return [
            'requester_name.required' => 'O nome do solicitante é obrigatório.',
            'requester_name.string' => 'O nome do solicitante deve ser uma string.',
            'requester_name.max' => 'O nome do solicitante não pode ter mais de 255 caracteres.',

            'destination.required' => 'O destino é obrigatório.',
            'destination.string' => 'O destino deve ser uma string.',
            'destination.max' => 'O destino não pode ter mais de 255 caracteres.',

            'departure_date.required' => 'A data de partida é obrigatória.',
            'departure_date.date' => 'A data de partida deve ser uma data válida.',
            'departure_date.after_or_equal' => 'A data de partida deve ser hoje ou uma data futura.',

            'return_date.required' => 'A data de retorno é obrigatória.',
            'return_date.date' => 'A data de retorno deve ser uma data válida.',
            'return_date.after' => 'A data de retorno deve ser posterior à data de partida.',
        ];
    }

    public function attributes(): array
    {
        return [
            'requester_name' => 'nome do solicitante',
            'destination' => 'destino',
            'departure_date' => 'data de partida',
            'return_date' => 'data de retorno',
        ];
    }
}

