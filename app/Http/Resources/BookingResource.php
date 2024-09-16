<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
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
            'seat_number' => $this->seat_number,
            'status' => $this->status,
            'paymentCode' => $this->payment_code ?? null,
            'user_id' => $this->user_id ?? null, // You may want to include the user who booked the seat
            'trip_id' => $this->trip_id, // Relation to the trip
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
