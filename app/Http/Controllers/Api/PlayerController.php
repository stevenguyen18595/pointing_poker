<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlayerResource;
use App\Models\Game;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PlayerController extends Controller
{
    public function index(Request $request, Game $game): JsonResponse
    {
        $players = $game->players()
            ->withCount(['votes'])
            ->latest('last_seen_at')
            ->get();

        return response()->json([
            'data' => PlayerResource::collection($players),
        ]);
    }

    public function show(Game $game, Player $player): JsonResponse
    {
        // Ensure the player belongs to the game
        if ($player->game_id !== $game->id) {
            abort(404, 'Player not found in this game.');
        }

        $player->loadCount(['votes']);

        return response()->json([
            'data' => new PlayerResource($player),
        ]);
    }

    public function update(Request $request, Game $game, Player $player): JsonResponse
    {
        // Ensure the player belongs to the game
        if ($player->game_id !== $game->id) {
            abort(404, 'Player not found in this game.');
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'is_moderator' => 'sometimes|boolean',
        ]);

        // Check if name is unique in the game (if updating name)
        if (isset($validated['name']) && $validated['name'] !== $player->name) {
            $existingPlayer = $game->players()
                ->where('name', $validated['name'])
                ->where('id', '!=', $player->id)
                ->first();

            if ($existingPlayer) {
                return response()->json([
                    'message' => 'Player name already exists in this game.',
                    'errors' => ['name' => ['Player name already exists in this game.']],
                ], 422);
            }
        }

        $player->update($validated);

        return response()->json([
            'data' => new PlayerResource($player),
            'message' => 'Player updated successfully.',
        ]);
    }

    public function destroy(Game $game, Player $player): JsonResponse
    {
        // Ensure the player belongs to the game
        if ($player->game_id !== $game->id) {
            abort(404, 'Player not found in this game.');
        }

        $player->delete();

        return response()->json([
            'message' => 'Player removed from game successfully.',
        ]);
    }

    public function updateActivity(Request $request, Game $game, Player $player): JsonResponse
    {
        // Ensure the player belongs to the game
        if ($player->game_id !== $game->id) {
            abort(404, 'Player not found in this game.');
        }

        $player->updateActivity();

        return response()->json([
            'data' => new PlayerResource($player),
            'message' => 'Player activity updated.',
        ]);
    }
}
