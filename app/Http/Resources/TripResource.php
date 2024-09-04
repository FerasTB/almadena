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
            'template_id' => $this->template_id,
            'departure_time' => $this->departure_time,
            'passenger_cost' => $this->passenger_cost,
            'note' => $this->note,
            'trip_day' => $this->trip_day,
            'trip_time' => $this->trip_time,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // Add any related data you need to include
        ];
    }
}
