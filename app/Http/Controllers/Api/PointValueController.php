<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PointValueResource;
use App\Http\Requests\PointValueIndexRequest;
use App\Models\PointValue;
use Illuminate\Http\JsonResponse;

class PointValueController extends Controller
{
    public function index(PointValueIndexRequest $request): JsonResponse
    {
        $query = PointValue::query();

        // Filter by active status if requested
        if ($request->shouldFilterActive()) {
            $query->active();
        }

        // Filter by card type if requested
        if ($request->hasTypeFilter()) {
            $query->byType($request->getType());
        }

        $pointValues = $query->ordered()->get();

        return response()->json([
            'data' => PointValueResource::collection($pointValues),
        ]);
    }

    public function show(PointValue $pointValue): JsonResponse
    {
        return response()->json([
            'data' => new PointValueResource($pointValue),
        ]);
    }
}
