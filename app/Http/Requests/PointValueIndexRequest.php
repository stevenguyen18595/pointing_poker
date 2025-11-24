<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PointValueIndexRequest extends FormRequest
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
            'active' => 'sometimes|boolean',
            'type' => 'sometimes|string|in:fibonacci,tshirt,powers_of_two,custom',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'type.in' => 'Type must be one of: fibonacci, tshirt, powers_of_two, custom.',
        ];
    }

    /**
     * Check if only active point values should be returned.
     */
    public function shouldFilterActive(): bool
    {
        return $this->boolean('active');
    }

    /**
     * Get the type filter if provided.
     */
    public function getType(): ?string
    {
        return $this->validated('type');
    }

    /**
     * Check if type filter is provided.
     */
    public function hasTypeFilter(): bool
    {
        return $this->filled('type');
    }
}