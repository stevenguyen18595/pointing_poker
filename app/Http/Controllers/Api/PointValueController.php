<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PointValueResource;
use App\Models\PointValue;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PointValueController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = PointValue::query();

        // Filter by active status if requested
        if ($request->boolean('active')) {
            $query->active();
        }

        // Filter by card type if requested
        if ($request->filled('type')) {
            $query->byType($request->get('type'));
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
