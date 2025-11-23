<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GameResource;
use App\Http\Resources\PlayerResource;
use App\Models\Game;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class GameController extends Controller
{
    public function index(): JsonResponse
    {
        $games = Game::with(['status'])
            ->withCount(['players', 'stories'])
            ->latest()
            ->get();

        return response()->json([
            'data' => GameResource::collection($games),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'settings' => 'nullable|array',
        ]);

        $game = Game::create([
            'name' => $validated['name'],
            'status_id' => 1, // Default to 'waiting' status
            'settings' => $validated['settings'] ?? [],
        ]);

        $game->load(['status']);

        return response()->json([
            'data' => new GameResource($game),
            'message' => 'Game created successfully.',
        ], 201);
    }

    public function show(Game $game): JsonResponse
    {
        $game->load([
            'status',
            'players',
            'stories.votes.pointValue',
            'currentStory'
        ]);
        
        $game->loadCount(['players', 'stories']);

        return response()->json([
            'data' => new GameResource($game),
        ]);
    }

    public function update(Request $request, Game $game): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'settings' => 'sometimes|array',
            'status_id' => 'sometimes|exists:game_statuses,id',
        ]);

        $game->update($validated);
        $game->load(['status']);

        return response()->json([
            'data' => new GameResource($game),
            'message' => 'Game updated successfully.',
        ]);
    }

    public function destroy(Game $game): JsonResponse
    {
        $game->delete();

        return response()->json([
            'message' => 'Game deleted successfully.',
        ]);
    }

    public function join(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'game_code' => 'required|string',
            'player_name' => 'required|string|max:255',
        ]);

        $game = Game::where('game_code', $validated['game_code'])->first();

        if (!$game) {
            throw ValidationException::withMessages([
                'game_code' => 'Game not found.',
            ]);
        }

        // Check if player name already exists in the game
        $existingPlayer = $game->players()->where('name', $validated['player_name'])->first();
        
        if ($existingPlayer) {
            throw ValidationException::withMessages([
                'player_name' => 'Player name already exists in this game.',
            ]);
        }

        $player = Player::create([
            'game_id' => $game->id,
            'name' => $validated['player_name'],
            'session_id' => session()->getId(),
            'last_seen_at' => now(),
        ]);

        $game->load(['status']);

        return response()->json([
            'data' => [
                'game' => new GameResource($game),
                'player' => new PlayerResource($player),
            ],
            'message' => 'Successfully joined the game.',
        ]);
    }

    public function updateStatus(Request $request, Game $game): JsonResponse
    {
        $validated = $request->validate([
            'status_id' => 'required|exists:game_statuses,id',
        ]);

        $game->update(['status_id' => $validated['status_id']]);
        $game->load(['status']);

        return response()->json([
            'data' => new GameResource($game),
            'message' => 'Game status updated successfully.',
        ]);
    }
}
