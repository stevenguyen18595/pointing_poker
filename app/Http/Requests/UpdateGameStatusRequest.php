<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGameStatusRequest extends FormRequest
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
            'status_id' => 'required|integer|exists:game_statuses,id',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'status_id.required' => 'Status ID is required.',
            'status_id.integer' => 'Status ID must be a valid integer.',
            'status_id.exists' => 'The selected status does not exist.',
        ];
    }

    /**
     * Get the status ID from the request.
     */
    public function getStatusId(): int
    {
        return $this->validated('status_id');
    }

    /**
     * Get the update data for the game status.
     */
    public function getStatusUpdateData(): array
    {
        return [
            'status_id' => $this->getStatusId(),
        ];
    }
}
