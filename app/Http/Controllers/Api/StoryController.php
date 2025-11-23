<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StoryResource;
use App\Models\Game;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StoryController extends Controller
{
    public function index(Request $request, Game $game): JsonResponse
    {
        $stories = $game->stories()
            ->with(['votes.player', 'votes.pointValue'])
            ->withCount(['votes'])
            ->ordered()
            ->get();

        return response()->json([
            'data' => StoryResource::collection($stories),
        ]);
    }

    public function store(Request $request, Game $game): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'acceptance_criteria' => 'nullable|string|max:2000',
        ]);

        // Get the highest sort order and increment it
        $maxSortOrder = $game->stories()->max('sort_order') ?? 0;

        $story = Story::create([
            'game_id' => $game->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'acceptance_criteria' => $validated['acceptance_criteria'],
            'sort_order' => $maxSortOrder + 1,
        ]);

        $story->load(['votes']);
        $story->loadCount(['votes']);

        return response()->json([
            'data' => new StoryResource($story),
            'message' => 'Story created successfully.',
        ], 201);
    }

    public function show(Story $story): JsonResponse
    {
        $story->load(['votes.player', 'votes.pointValue']);
        $story->loadCount(['votes']);

        return response()->json([
            'data' => new StoryResource($story),
        ]);
    }

    public function update(Request $request, Story $story): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string|max:2000',
            'acceptance_criteria' => 'sometimes|nullable|string|max:2000',
            'estimated_points' => 'sometimes|nullable|string|max:50',
            'sort_order' => 'sometimes|integer|min:0',
            'is_completed' => 'sometimes|boolean',
        ]);

        $story->update($validated);
        $story->load(['votes']);
        $story->loadCount(['votes']);

        return response()->json([
            'data' => new StoryResource($story),
            'message' => 'Story updated successfully.',
        ]);
    }

    public function destroy(Story $story): JsonResponse
    {
        $story->delete();

        return response()->json([
            'message' => 'Story deleted successfully.',
        ]);
    }

    public function setCurrent(Story $story): JsonResponse
    {
        $story->setCurrent();
        $story->load(['votes']);
        $story->loadCount(['votes']);

        return response()->json([
            'data' => new StoryResource($story),
            'message' => 'Story set as current.',
        ]);
    }

    public function complete(Request $request, Story $story): JsonResponse
    {
        $validated = $request->validate([
            'estimated_points' => 'nullable|string|max:50',
        ]);

        $story->complete($validated['estimated_points'] ?? null);
        $story->load(['votes']);
        $story->loadCount(['votes']);

        return response()->json([
            'data' => new StoryResource($story),
            'message' => 'Story completed successfully.',
        ]);
    }

    public function startVoting(Story $story): JsonResponse
    {
        // Clear existing votes for this story
        $story->votes()->delete();

        // Set as current story
        $story->setCurrent();

        $story->load(['votes']);
        $story->loadCount(['votes']);

        return response()->json([
            'data' => new StoryResource($story),
            'message' => 'Voting started for story.',
        ]);
    }
}
