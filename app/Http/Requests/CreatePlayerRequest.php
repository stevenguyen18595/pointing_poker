<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePlayerRequest extends FormRequest
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
            'name' => 'required|string|max:100|min:2',
            'email' => 'nullable|email|max:255',
            'role' => 'nullable|in:facilitator,developer,tester,business_analyst,product_owner',
            'avatar_color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
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
            'name.required' => 'Player name is required.',
            'name.min' => 'Player name must be at least 2 characters.',
            'name.max' => 'Player name cannot exceed 100 characters.',
            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'Email cannot exceed 255 characters.',
            'role.in' => 'Role must be one of: facilitator, developer, tester, business_analyst, product_owner.',
            'avatar_color.regex' => 'Avatar color must be a valid hex color code (e.g., #FF5733).',
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
     * Get the player name.
     */
    public function getName(): string
    {
        return $this->validated('name');
    }

    /**
     * Get the player email.
     */
    public function getEmail(): ?string
    {
        return $this->validated('email');
    }

    /**
     * Get the player role.
     */
    public function getRole(): ?string
    {
        return $this->validated('role');
    }

    /**
     * Get the avatar color.
     */
    public function getAvatarColor(): ?string
    {
        return $this->validated('avatar_color');
    }

    /**
     * Get player data for creation.
     */
    public function getPlayerData(): array
    {
        return [
            'game_id' => $this->getGameId(),
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'role' => $this->getRole(),
            'avatar_color' => $this->getAvatarColor() ?? $this->generateRandomColor(),
            'is_facilitator' => $this->getRole() === 'facilitator',
        ];
    }

    /**
     * Generate a random color if none provided.
     */
    private function generateRandomColor(): string
    {
        $colors = [
            '#FF5733', '#33FF57', '#3357FF', '#FF33F5', '#F5FF33',
            '#33FFF5', '#FF8C33', '#8C33FF', '#33FF8C', '#FF3333'
        ];
        
        return $colors[array_rand($colors)];
    }
}
