<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JoinGameRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'game_code' => 'required|string|size:8',
            'player_name' => 'required|string|max:255|min:2',
            'is_moderator' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'game_code.required' => 'Game code is required.',
            'game_code.size' => 'Game code must be exactly 8 characters.',
            'player_name.required' => 'Player name is required.',
            'player_name.min' => 'Player name must be at least 2 characters.',
            'player_name.max' => 'Player name cannot exceed 255 characters.',
        ];
    }

    /**
     * Get the game code from the request.
     */
    public function getGameCode(): string
    {
        return $this->validated('game_code');
    }

    /**
     * Get the player name from the request.
     */
    public function getPlayerName(): string
    {
        return $this->validated('player_name');
    }

    /**
     * Get whether player should be moderator.
     */
    public function getIsModerator(): bool
    {
        return $this->validated('is_moderator') ?? false;
    }

    /**
     * Get player data for creation.
     */
    public function getPlayerData(int $gameId): array
    {
        return [
            'game_id' => $gameId,
            'name' => $this->getPlayerName(),
            'is_moderator' => $this->getIsModerator(),
            'session_id' => session()->getId(),
            'last_seen_at' => now(),
        ];
    }
}
