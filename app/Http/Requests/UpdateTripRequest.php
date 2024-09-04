<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTripRequest extends FormRequest
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
            'template_id' => 'sometimes|exists:templates,id',
            'departure_time' => 'sometimes|date',
            'passenger_cost' => 'sometimes|numeric|min:0',
            'note' => 'nullable|string',
            'trip_day' => 'sometimes|date',
            'trip_time' => 'sometimes|date_format:H:i',
        ];
    }
}
