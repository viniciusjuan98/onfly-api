<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(name="Health Check")
 */
class HealthController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/ping",
     *     summary="Health check endpoint",
     *     description="Simple endpoint to check if the API is running",
     *     tags={"Health Check"},
     *     @OA\Response(
     *         response=200,
     *         description="API is running",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="pong")
     *         )
     *     )
     * )
     */
    public function ping(): JsonResponse
    {
        return response()->json(['message' => 'pong']);
    }
}

