<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TripResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'departure_time' => $this->departure_time,
            'passenger_cost' => $this->passenger_cost,
            'note' => $this->note,
            'data' => $this->trip_day,
            'time' => $this->trip_time,
            'from' => $this->first_point,
            'to' => $this->last_point,
            'availableSeats' => $this->available_seats,
        ];
    }
}
