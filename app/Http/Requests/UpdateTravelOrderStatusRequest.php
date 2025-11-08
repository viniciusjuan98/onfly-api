<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
}

