<?php

namespace App\Data\TravelOrder;

/**
 * @OA\Schema(
 *     schema="CreateTravelOrderDTO",
 *     required={"requester_name", "destination", "departure_date", "return_date"},
 *     @OA\Property(property="requester_name", type="string", example="John Doe"),
 *     @OA\Property(property="destination", type="string", example="New York"),
 *     @OA\Property(property="departure_date", type="string", format="date", example="2025-12-01"),
 *     @OA\Property(property="return_date", type="string", format="date", example="2025-12-10")
 * )
 */
class CreateTravelOrderDTO
{
    public string $requesterName;
    public string $destination;
    public string $departureDate;
    public string $returnDate;

    public function __construct(array $data)
    {
        $this->requesterName = $data['requester_name'];
        $this->destination = $data['destination'];
        $this->departureDate = $data['departure_date'];
        $this->returnDate = $data['return_date'];
    }
}

