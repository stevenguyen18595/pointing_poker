<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VoteResource;
use App\Http\Requests\CreateVoteRequest;
use App\Http\Requests\UpdateVoteRequest;
use App\Models\Game;
use App\Models\Vote;
use App\Models\Player;
use Illuminate\Http\JsonResponse;

class VoteController extends Controller
{
    public function index(Game $game): JsonResponse
    {
        $votes = $game->votes()
            ->with(['player', 'pointValue'])
            ->recent()
            ->get();

        return response()->json([
            'data' => VoteResource::collection($votes),
        ]);
    }

    public function store(CreateVoteRequest $request, Game $game): JsonResponse
    {
        // Check if player already voted for this game
        $existingVote = Vote::where('game_id', $game->id)
            ->where('player_id', $request->getPlayerId())
            ->first();

        if ($existingVote) {
            // Update existing vote
            $existingVote->update([
                'point_value_id' => $request->getPointValueId(),
                'voted_at' => now(),
            ]);
            $vote = $existingVote;
        } else {
            // Create new vote
            $vote = Vote::create([
                'game_id' => $game->id,
                'player_id' => $request->getPlayerId(),
                'point_value_id' => $request->getPointValueId(),
                'voted_at' => now(),
            ]);
        }

        $vote->load(['player', 'pointValue']);

        return response()->json([
            'data' => new VoteResource($vote),
            'message' => 'Vote submitted successfully.',
        ], $existingVote ? 200 : 201);
    }

    public function show(Game $game, Player $player): JsonResponse
    {
        $vote = Vote::where('game_id', $game->id)
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

    public function update(UpdateVoteRequest $request, Vote $vote): JsonResponse
    {
        $vote->update($request->getVoteUpdateData());
        $vote->load(['player', 'pointValue']);

        return response()->json([
            'data' => new VoteResource($vote),
            'message' => 'Vote updated successfully.',
        ]);
    }

    public function destroy(Game $game, Player $player): JsonResponse
    {
        $vote = Vote::where('game_id', $game->id)
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

    public function reveal(Game $game): JsonResponse
    {
        // Update game status to 'revealed'
        $revealedStatus = \App\Models\GameStatus::where('name', 'revealed')->first();
        if ($revealedStatus) {
            $game->update(['status_id' => $revealedStatus->id]);
        }

        $votes = $game->votes()
            ->with(['player', 'pointValue'])
            ->get();

        return response()->json([
            'data' => [
                'votes' => VoteResource::collection($votes),
                'revealed' => true,
                'statistics' => $this->calculateVoteStatistics($votes),
            ],
            'message' => 'Votes revealed.',
        ]);
    }

    public function reset(Game $game): JsonResponse
    {
        // Update game status back to 'voting'
        $votingStatus = \App\Models\GameStatus::where('name', 'voting')->first();
        if ($votingStatus) {
            $game->update(['status_id' => $votingStatus->id]);
        }

        $game->votes()->delete();

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
            ->map(function ($group) use ($votes) {
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
