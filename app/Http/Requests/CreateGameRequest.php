<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateGameRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow all requests (adjust based on your auth requirements)
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100|min:3',
            'description' => 'nullable|string|max:500',
            'settings' => 'nullable|array',
            'creator_name' => 'sometimes|string|max:100|min:2',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Game name is required.',
            'name.max' => 'Game name cannot exceed 255 characters.',
            'description.max' => 'Description cannot exceed 1000 characters.',
        ];
    }

    /**
     * Get the game name from the request.
     * Similar to properties in .NET DTOs
     */
    public function getName(): string
    {
        return $this->validated('name');
    }

    /**
     * Get the game description from the request.
     */
    public function getDescription(): ?string
    {
        return $this->validated('description');
    }

    /**
     * Get the game settings from the request.
     */
    public function getSettings(): array
    {
        return $this->validated('settings') ?? [];
    }

    /**
     * Get the creator name from the request.
     */
    public function getCreatorName(): ?string
    {
        return $this->validated('creator_name');
    }

    /**
     * Transform the validated data into an array suitable for model creation.
     */
    public function getGameData(): array
    {
        return [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'settings' => $this->getSettings(),
            'status_id' => 1, // Default to 'waiting' status
        ];
    }
}
