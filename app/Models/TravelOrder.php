<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="TravelOrder",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="requester_name", type="string", example="John Doe"),
 *     @OA\Property(property="destination", type="string", example="New York"),
 *     @OA\Property(property="departure_date", type="string", format="date", example="2025-12-01"),
 *     @OA\Property(property="return_date", type="string", format="date", example="2025-12-10"),
 *     @OA\Property(property="status", type="string", enum={"solicitado", "aprovado", "cancelado"}, example="solicitado")
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class TravelOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'requester_name',
        'destination',
        'departure_date',
        'return_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'departure_date' => 'date',
            'return_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
