<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(name="Notifications")
 */
class NotificationController extends Controller
{
    public function __construct(private NotificationService $notificationService) {}

    /**
     * @OA\Get(
     *     path="/api/me/notificacoes",
     *     summary="Get all notifications for authenticated user",
     *     tags={"Notifications"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of notifications",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="string", example="550e8400-e29b-41d4-a716-446655440000"),
     *                 @OA\Property(property="type", type="string", example="App\\Notifications\\TravelOrderStatusChanged"),
     *                 @OA\Property(property="data", type="object",
     *                     @OA\Property(property="travel_order_id", type="integer", example=1),
     *                     @OA\Property(property="destination", type="string", example="São Paulo"),
     *                     @OA\Property(property="old_status", type="string", example="solicitado"),
     *                     @OA\Property(property="new_status", type="string", example="aprovado"),
     *                     @OA\Property(property="message", type="string", example="Seu pedido de viagem para São Paulo foi aprovado.")
     *                 ),
     *                 @OA\Property(property="read_at", type="string", format="date-time", nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time")
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $userId = auth()->id();
        $notifications = $this->notificationService->getNotifications($userId);

        return response()->json(['data' => $notifications]);
    }

    /**
     * @OA\Patch(
     *     path="/api/me/notificacoes/{id}/read",
     *     summary="Mark notification as read",
     *     tags={"Notifications"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notification marked as read",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Notificação marcada como lida."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="string", example="550e8400-e29b-41d4-a716-446655440000"),
     *                 @OA\Property(property="read_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Notification not found"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - not your notification"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function markAsRead(string $id): JsonResponse
    {
        $userId = auth()->id();
        $notification = $this->notificationService->markAsRead($id, $userId);

        return response()->json([
            'message' => 'Notificação marcada como lida.',
            'data' => [
                'id' => $notification->id,
                'read_at' => $notification->read_at,
            ],
        ]);
    }
}

