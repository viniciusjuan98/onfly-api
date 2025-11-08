<?php

namespace App\Data\TravelOrder;

/**
 * @OA\Schema(
 *     schema="UpdateTravelOrderStatusDTO",
 *     required={"status"},
 *     @OA\Property(property="status", type="string", enum={"solicitado", "aprovado", "cancelado"}, example="solicitado")
 * )
 */
class UpdateTravelOrderStatusDTO
{
    public string $status;

    public function __construct(array $data)
    {
        $this->status = $data['status'];
    }
}

