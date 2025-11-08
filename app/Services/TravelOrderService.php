<?php

namespace App\Services;

use App\Data\TravelOrder\CreateTravelOrderDTO;
use App\Data\TravelOrder\UpdateTravelOrderStatusDTO;
use App\Models\TravelOrder;
use Illuminate\Pagination\LengthAwarePaginator;

class TravelOrderService
{
    public function create(CreateTravelOrderDTO $dto, int $userId): TravelOrder
    {
        return TravelOrder::create([
            'user_id' => $userId,
            'requester_name' => $dto->requesterName,
            'destination' => $dto->destination,
            'departure_date' => $dto->departureDate,
            'return_date' => $dto->returnDate,
            'status' => 'solicitado',
        ]);
    }

    public function findById(int $id): ?TravelOrder
    {
        return TravelOrder::with('user')->find($id);
    }

    public function findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = TravelOrder::with('user');

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['destination'])) {
            $query->where('destination', 'like', '%' . $filters['destination'] . '%');
        }

        if (isset($filters['departure_date'])) {
            $query->whereDate('departure_date', $filters['departure_date']);
        }

        if (isset($filters['return_date'])) {
            $query->whereDate('return_date', $filters['return_date']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function updateStatus(int $id, UpdateTravelOrderStatusDTO $dto): ?TravelOrder
    {
        $travelOrder = TravelOrder::find($id);

        if (!$travelOrder) {
            return null;
        }

        $travelOrder->update(['status' => $dto->status]);

        return $travelOrder->fresh();
    }
}

