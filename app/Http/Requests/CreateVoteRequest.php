<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateVoteRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'game_id' => 'required|integer|exists:games,id',
            'player_id' => 'required|integer|exists:players,id',
            'point_value_id' => 'required|integer|exists:point_values,id',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'game_id.required' => 'Game ID is required.',
            'game_id.exists' => 'The specified game does not exist.',
            'player_id.required' => 'Player ID is required.',
            'player_id.exists' => 'The specified player does not exist.',
            'point_value_id.required' => 'Point value is required.',
            'point_value_id.exists' => 'The specified point value does not exist.',
        ];
    }

    /**
     * Get the game ID.
     */
    public function getGameId(): int
    {
        return $this->validated('game_id');
    }

    /**
     * Get the player ID.
     */
    public function getPlayerId(): int
    {
        return $this->validated('player_id');
    }

    /**
     * Get the point value ID.
     */
    public function getPointValueId(): int
    {
        return $this->validated('point_value_id');
    }

    /**
     * Get vote data for creation.
     */
    public function getVoteData(): array
    {
        return [
            'game_id' => $this->getGameId(),
            'player_id' => $this->getPlayerId(),
            'point_value_id' => $this->getPointValueId(),
            'voted_at' => now(),
        ];
    }

    /**
     * Check if this is a vote update (player already voted).
     */
    public function isVoteUpdate(): bool
    {
        // This would be implemented in the controller logic
        return false;
    }
}
