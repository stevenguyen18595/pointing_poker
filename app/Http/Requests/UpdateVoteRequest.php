<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVoteRequest extends FormRequest
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
            'point_value_id' => 'required|integer|exists:point_values,id',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'point_value_id.required' => 'Point value is required.',
            'point_value_id.exists' => 'The specified point value does not exist.',
        ];
    }

    /**
     * Get the point value ID.
     */
    public function getPointValueId(): int
    {
        return $this->validated('point_value_id');
    }

    /**
     * Get vote update data.
     */
    public function getVoteUpdateData(): array
    {
        return [
            'point_value_id' => $this->getPointValueId(),
            'voted_at' => now(),
        ];
    }
}
