<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlayerResource;
use App\Http\Requests\UpdatePlayerRequest;
use App\Models\Game;
use App\Models\Player;
use Illuminate\Http\JsonResponse;

class PlayerController extends Controller
{
    public function index(Game $game): JsonResponse
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

    public function update(UpdatePlayerRequest $request, Game $game, Player $player): JsonResponse
    {
        // Ensure the player belongs to the game
        if ($player->game_id !== $game->id) {
            abort(404, 'Player not found in this game.');
        }

        if (!$request->hasUpdates()) {
            return response()->json([
                'data' => new PlayerResource($player),
                'message' => 'No updates provided.',
            ]);
        }

        // Check if name is unique in the game (if updating name)
        if ($request->getName() && $request->getName() !== $player->name) {
            $existingPlayer = $game->players()
                ->where('name', $request->getName())
                ->where('id', '!=', $player->id)
                ->first();

            if ($existingPlayer) {
                return response()->json([
                    'message' => 'Player name already exists in this game.',
                    'errors' => ['name' => ['Player name already exists in this game.']],
                ], 422);
            }
        }

        $player->update($request->getUpdateData());

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

    public function updateActivity(Game $game, Player $player): JsonResponse
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
