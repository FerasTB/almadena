<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTripRequest extends FormRequest
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
    public function rules()
    {
        return [
            'departure_time' => 'required|date_format:H:i:s',
            'passenger_cost' => 'required|numeric|min:0',
            'note' => 'nullable|string',
            'trip_day' => 'required|date',
            'trip_time' => 'required|date_format:H:i:s',
            'template_id' => 'required|exists:templates,id',
            'points' => 'required|array|min:2',
            'points.*.name' => 'required|string|max:255',
            'points.*.duration' => 'nullable|integer|min:0'
        ];
    }

    public function messages()
    {
        return [
            'departure_time.required' => 'Departure time is required.',
            'passenger_cost.required' => 'Passenger cost is required.',
            'trip_day.required' => 'Trip day is required.',
            'trip_time.required' => 'Trip time is required.',
            'template_id.exists' => 'Selected template does not exist.',
            'points.required' => 'At least two points are required for the trip.',
            'points.*.name.required' => 'Each point must have a name.',
            'points.*.duration.integer' => 'Duration must be a valid integer.',
        ];
    }
}
