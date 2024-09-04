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
    public function rules(): array
    {
        return [
            'template_id' => 'required|exists:templates,id',
            'departure_time' => 'required|date',
            'passenger_cost' => 'required|numeric|min:0',
            'note' => 'nullable|string',
            'trip_day' => 'required|date',
            'trip_time' => 'required|date_format:H:i',
        ];
    }
}
