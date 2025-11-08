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
}

