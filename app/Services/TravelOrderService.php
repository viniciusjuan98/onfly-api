<?php

namespace App\Services;

use App\Data\TravelOrder\CreateTravelOrderDTO;
use App\Data\TravelOrder\UpdateTravelOrderStatusDTO;
use App\Exceptions\TravelOrderException;
use App\Models\TravelOrder;
use Illuminate\Database\Eloquent\Collection;

class TravelOrderService
{
    public function create(CreateTravelOrderDTO $dto, int $userId): TravelOrder
    {
        // Validate dates
        if ($dto->returnDate < $dto->departureDate) {
            throw TravelOrderException::invalidDates();
        }

        return TravelOrder::create([
            'user_id' => $userId,
            'requester_name' => $dto->requesterName,
            'destination' => $dto->destination,
            'departure_date' => $dto->departureDate,
            'return_date' => $dto->returnDate,
            'status' => 'solicitado',
        ]);
    }

    public function findById(int $id): TravelOrder
    {
        $travelOrder = TravelOrder::find($id);

        if (!$travelOrder) {
            throw TravelOrderException::notFound();
        }

        return $travelOrder;
    }

    public function findAll(array $filters = []): Collection
    {
        $query = TravelOrder::query();

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

        if (isset($filters['departure_date_from'])) {
            $query->whereDate('departure_date', '>=', $filters['departure_date_from']);
        }

        if (isset($filters['departure_date_to'])) {
            $query->whereDate('departure_date', '<=', $filters['departure_date_to']);
        }

        if (isset($filters['return_date_from'])) {
            $query->whereDate('return_date', '>=', $filters['return_date_from']);
        }

        if (isset($filters['return_date_to'])) {
            $query->whereDate('return_date', '<=', $filters['return_date_to']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function updateStatus(int $id, UpdateTravelOrderStatusDTO $dto): TravelOrder
    {
        $travelOrder = TravelOrder::find($id);

        if (!$travelOrder) {
            throw TravelOrderException::notFound();
        }

        if (in_array($travelOrder->status, ['aprovado', 'cancelado'])) {
            throw TravelOrderException::invalidStatus($travelOrder->status);
        }

        $travelOrder->update(['status' => $dto->status]);

        return $travelOrder->fresh();
    }
}
