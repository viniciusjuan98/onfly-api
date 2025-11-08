<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTravelOrderRequest;
use App\Http\Requests\UpdateTravelOrderStatusRequest;
use App\Data\TravelOrder\CreateTravelOrderDTO;
use App\Data\TravelOrder\UpdateTravelOrderStatusDTO;
use App\Services\TravelOrderService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(name="Travel Orders")
 */
class TravelOrderController extends Controller
{
    public function __construct(private TravelOrderService $travelOrderService) {}

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     summary="Create a new travel order",
     *     tags={"Travel Orders"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CreateTravelOrderRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Travel order created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Travel order created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/TravelOrder")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function store(CreateTravelOrderRequest $request): JsonResponse
    {
        $dto = new CreateTravelOrderDTO($request->validated());
        $travelOrder = $this->travelOrderService->create($dto, auth()->id());

        return response()->json([
            'data' => $travelOrder,
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/orders/{id}",
     *     summary="Get a specific travel order",
     *     tags={"Travel Orders"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Travel order details",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/TravelOrder")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Travel order not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $travelOrder = $this->travelOrderService->findById($id);

        return response()->json(['data' => $travelOrder]);
    }

    /**
     * @OA\Get(
     *     path="/api/orders",
     *     summary="List all travel orders with filters",
     *     tags={"Travel Orders"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         @OA\Schema(type="string", enum={"solicitado", "aprovado", "cancelado"}, example="solicitado")
     *     ),
     *     @OA\Parameter(
     *         name="destination",
     *         in="query",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="departure_date",
     *         in="query",
     *         description="Exact departure date (format: YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="return_date",
     *         in="query",
     *         description="Exact return date (format: YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="departure_date_from",
     *         in="query",
     *         description="Departure date range start (format: YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="departure_date_to",
     *         in="query",
     *         description="Departure date range end (format: YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="return_date_from",
     *         in="query",
     *         description="Return date range start (format: YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="return_date_to",
     *         in="query",
     *         description="Return date range end (format: YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of travel orders",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/TravelOrder"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'status',
            'destination',
            'departure_date',
            'return_date',
            'departure_date_from',
            'departure_date_to',
            'return_date_from',
            'return_date_to'
        ]);

        $travelOrders = $this->travelOrderService->findAll($filters);

        return response()->json(['data' => $travelOrders]);
    }

    /**
     * @OA\Patch(
     *     path="/api/orders/{id}/status",
     *     summary="Update travel order status (Admin only)",
     *     tags={"Travel Orders"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateTravelOrderStatusRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Status updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/TravelOrder")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Travel order not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Admin only"
     *     )
     * )
     */
    public function updateStatus(int $id, UpdateTravelOrderStatusRequest $request): JsonResponse
    {
        $dto = new UpdateTravelOrderStatusDTO($request->validated());
        $travelOrder = $this->travelOrderService->updateStatus($id, $dto);

        return response()->json([
            'data' => $travelOrder,
        ]);
    }
}
