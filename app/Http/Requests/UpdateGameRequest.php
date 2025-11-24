<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGameRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255|min:3',
            'description' => 'sometimes|nullable|string|max:1000',
            'settings' => 'sometimes|nullable|array',
            'status_id' => 'sometimes|exists:game_statuses,id',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.min' => 'Game name must be at least 3 characters.',
            'name.max' => 'Game name cannot exceed 255 characters.',
            'description.max' => 'Description cannot exceed 1000 characters.',
            'status_id.exists' => 'The selected status is invalid.',
        ];
    }

    /**
     * Get the game name from the request if provided.
     */
    public function getName(): ?string
    {
        return $this->validated('name');
    }

    /**
     * Get the game description from the request if provided.
     */
    public function getDescription(): ?string
    {
        return $this->validated('description');
    }

    /**
     * Get the game settings from the request if provided.
     */
    public function getSettings(): ?array
    {
        return $this->validated('settings');
    }

    /**
     * Get the status ID from the request if provided.
     */
    public function getStatusId(): ?int
    {
        return $this->validated('status_id');
    }

    /**
     * Get only the validated data for updating.
     */
    public function getUpdateData(): array
    {
        return $this->validated();
    }

    /**
     * Check if any field is being updated.
     */
    public function hasUpdates(): bool
    {
        return count($this->validated()) > 0;
    }
}
