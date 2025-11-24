<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlayerRequest extends FormRequest
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
            'name' => 'sometimes|string|max:100|min:2',
            'email' => 'sometimes|nullable|email|max:255',
            'role' => 'sometimes|nullable|in:facilitator,developer,tester,business_analyst,product_owner',
            'avatar_color' => 'sometimes|nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_facilitator' => 'sometimes|boolean',
            'is_observer' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'name.min' => 'Player name must be at least 2 characters.',
            'name.max' => 'Player name cannot exceed 100 characters.',
            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'Email cannot exceed 255 characters.',
            'role.in' => 'Role must be one of: facilitator, developer, tester, business_analyst, product_owner.',
            'avatar_color.regex' => 'Avatar color must be a valid hex color code (e.g., #FF5733).',
        ];
    }

    /**
     * Get the player name if provided.
     */
    public function getName(): ?string
    {
        return $this->validated('name');
    }

    /**
     * Get the player email if provided.
     */
    public function getEmail(): ?string
    {
        return $this->validated('email');
    }

    /**
     * Get the player role if provided.
     */
    public function getRole(): ?string
    {
        return $this->validated('role');
    }

    /**
     * Get the avatar color if provided.
     */
    public function getAvatarColor(): ?string
    {
        return $this->validated('avatar_color');
    }

    /**
     * Get if player is facilitator if provided.
     */
    public function getIsFacilitator(): ?bool
    {
        return $this->validated('is_facilitator');
    }

    /**
     * Get if player is observer if provided.
     */
    public function getIsObserver(): ?bool
    {
        return $this->validated('is_observer');
    }

    /**
     * Get only the validated data for updating.
     */
    public function getUpdateData(): array
    {
        $data = $this->validated();
        
        // Update is_facilitator based on role if role is being updated
        if (isset($data['role'])) {
            $data['is_facilitator'] = $data['role'] === 'facilitator';
        }
        
        return $data;
    }

    /**
     * Check if any field is being updated.
     */
    public function hasUpdates(): bool
    {
        return count($this->validated()) > 0;
    }
}
