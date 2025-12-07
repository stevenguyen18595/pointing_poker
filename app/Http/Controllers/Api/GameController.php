<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateGameRequest;
use App\Http\Requests\JoinGameRequest;
use App\Http\Requests\UpdateGameRequest;
use App\Http\Requests\UpdateGameStatusRequest;
use App\Http\Resources\GameResource;
use App\Http\Resources\PlayerResource;
use App\Models\Game;
use App\Models\Player;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class GameController extends Controller
{
    public function index(): JsonResponse
    {
        $games = Game::with(['status'])
            ->withCount(['players', 'votes'])
            ->latest()
            ->get();

        return response()->json([
            'data' => GameResource::collection($games),
        ]);
    }

    public function store(CreateGameRequest $request): JsonResponse
    {
        // Now we have strongly-typed access to request data
        // Similar to .NET DTOs with IntelliSense support
        $game = Game::create($request->getGameData());

        // Create the creator as a player if name provided
        $player = null;
        if ($request->getCreatorName()) {
            $player = Player::create([
                'game_id' => $game->id,
                'name' => $request->getCreatorName(),
                'is_moderator' => true, // Creator is always moderator
                'session_id' => session()->getId(),
                'last_seen_at' => now(),
            ]);
        }

        $game->load(['status']);

        return response()->json([
            'data' => [
                'game' => new GameResource($game),
                'player' => $player ? new PlayerResource($player) : null,
            ],
            'message' => 'Game created successfully.',
        ], 201);
    }

    public function show(Game $game): JsonResponse
    {
        $game->load([
            'status',
            'players',
            'votes.pointValue',
        ]);

        $game->loadCount(['players', 'votes']);

        return response()->json([
            'data' => new GameResource($game),
        ]);
    }

    public function update(UpdateGameRequest $request, Game $game): JsonResponse
    {
        // Strongly-typed request with validation
        if (!$request->hasUpdates()) {
            return response()->json([
                'data' => new GameResource($game),
                'message' => 'No changes provided.',
            ]);
        }

        $game->update($request->getUpdateData());
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

    public function join(JoinGameRequest $request): JsonResponse
    {
        // Strongly-typed request with automatic validation
        $game = Game::where('game_code', $request->getGameCode())->first();

        if (!$game) {
            throw ValidationException::withMessages([
                'game_code' => 'Game not found.',
            ]);
        }

        // Check if player name already exists in the game
        $existingPlayer = $game->players()->where('name', $request->getPlayerName())->first();

        $player = null;
        if (!$existingPlayer) {
            $player = Player::create($request->getPlayerData($game->id));
        } else {
            $player = $existingPlayer;
        }

        $game->load(['status']);

        return response()->json([
            'data' => [
                'game' => new GameResource($game),
                'player' => new PlayerResource($player),
            ],
            'message' => 'Successfully joined the game.',
        ]);
    }

    public function updateStatus(UpdateGameStatusRequest $request, Game $game): JsonResponse
    {
        // Strongly-typed request with validation
        $game->update($request->getStatusUpdateData());
        $game->load(['status']);

        return response()->json([
            'data' => new GameResource($game),
            'message' => 'Game status updated successfully.',
        ]);
    }
}
