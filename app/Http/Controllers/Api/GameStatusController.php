<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GameStatusResource;
use App\Models\GameStatus;
use Illuminate\Http\JsonResponse;

class GameStatusController extends Controller
{
    public function index(): JsonResponse
    {
        $statuses = GameStatus::active()->ordered()->get();

        return response()->json([
            'data' => GameStatusResource::collection($statuses),
        ]);
    }

    public function show(GameStatus $gameStatus): JsonResponse
    {
        return response()->json([
            'data' => new GameStatusResource($gameStatus),
        ]);
    }
}
