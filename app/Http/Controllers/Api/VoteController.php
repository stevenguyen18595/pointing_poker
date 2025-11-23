<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VoteResource;
use App\Models\Story;
use App\Models\Vote;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VoteController extends Controller
{
    public function index(Story $story): JsonResponse
    {
        $votes = $story->votes()
            ->with(['player', 'pointValue'])
            ->recent()
            ->get();

        return response()->json([
            'data' => VoteResource::collection($votes),
        ]);
    }

    public function store(Request $request, Story $story): JsonResponse
    {
        $validated = $request->validate([
            'point_value_id' => 'required|exists:point_values,id',
            'player_id' => 'sometimes|exists:players,id',
        ]);

        // If no player_id provided, we need to implement session-based player identification
        // For now, let's assume it's provided or use a default
        $playerId = $validated['player_id'] ?? 1; // TODO: Implement proper player identification

        // Check if player already voted for this story
        $existingVote = Vote::where('story_id', $story->id)
            ->where('player_id', $playerId)
            ->first();

        if ($existingVote) {
            // Update existing vote
            $existingVote->update([
                'point_value_id' => $validated['point_value_id'],
                'voted_at' => now(),
            ]);
            $vote = $existingVote;
        } else {
            // Create new vote
            $vote = Vote::create([
                'story_id' => $story->id,
                'player_id' => $playerId,
                'point_value_id' => $validated['point_value_id'],
            ]);
        }

        $vote->load(['player', 'pointValue']);

        return response()->json([
            'data' => new VoteResource($vote),
            'message' => 'Vote submitted successfully.',
        ], $existingVote ? 200 : 201);
    }

    public function show(Story $story, Player $player): JsonResponse
    {
        $vote = Vote::where('story_id', $story->id)
            ->where('player_id', $player->id)
            ->with(['player', 'pointValue'])
            ->first();

        if (!$vote) {
            return response()->json([
                'data' => null,
                'message' => 'No vote found.',
            ], 404);
        }

        return response()->json([
            'data' => new VoteResource($vote),
        ]);
    }

    public function update(Request $request, Vote $vote): JsonResponse
    {
        $validated = $request->validate([
            'point_value_id' => 'required|exists:point_values,id',
        ]);

        $vote->update([
            'point_value_id' => $validated['point_value_id'],
            'voted_at' => now(),
        ]);

        $vote->load(['player', 'pointValue']);

        return response()->json([
            'data' => new VoteResource($vote),
            'message' => 'Vote updated successfully.',
        ]);
    }

    public function destroy(Story $story, Player $player): JsonResponse
    {
        $vote = Vote::where('story_id', $story->id)
            ->where('player_id', $player->id)
            ->first();

        if (!$vote) {
            return response()->json([
                'message' => 'No vote found.',
            ], 404);
        }

        $vote->delete();

        return response()->json([
            'message' => 'Vote deleted successfully.',
        ]);
    }

    public function reveal(Story $story): JsonResponse
    {
        $votes = $story->votes()
            ->with(['player', 'pointValue'])
            ->get();

        // TODO: Implement vote revelation logic (e.g., update story status, calculate statistics)

        return response()->json([
            'data' => [
                'votes' => VoteResource::collection($votes),
                'revealed' => true,
                'statistics' => $this->calculateVoteStatistics($votes),
            ],
            'message' => 'Votes revealed.',
        ]);
    }

    public function reset(Story $story): JsonResponse
    {
        $story->votes()->delete();

        return response()->json([
            'message' => 'Votes reset successfully.',
        ]);
    }

    private function calculateVoteStatistics($votes): array
    {
        if ($votes->isEmpty()) {
            return [
                'total_votes' => 0,
                'average' => null,
                'consensus' => null,
                'distribution' => [],
            ];
        }

        $pointValues = $votes->pluck('pointValue.value')->filter();
        $numericValues = $pointValues->filter(function ($value) {
            return is_numeric($value);
        })->map(function ($value) {
            return (float) $value;
        });

        $distribution = $votes->groupBy('pointValue.label')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'percentage' => round(($group->count() / $votes->count()) * 100, 1),
                ];
            });

        return [
            'total_votes' => $votes->count(),
            'average' => $numericValues->isNotEmpty() ? round($numericValues->avg(), 1) : null,
            'consensus' => $distribution->count() === 1 ? $distribution->keys()->first() : null,
            'distribution' => $distribution,
        ];
    }
}
