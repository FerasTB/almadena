<?php

namespace App\Jobs;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RevertPendingSeats implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $seatIds;
    protected $tripId;
    /**
     * Create a new job instance.
     */
    public function __construct(array $seatIds, int $tripId)
    {
        $this->seatIds = $seatIds;
        $this->tripId = $tripId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Find all pending bookings for the trip and seat numbers
        Booking::whereIn('seat_number', $this->seatIds)
            ->where('trip_id', $this->tripId)
            ->where('status', 'pending')
            ->update([
                'status' => 'available',
                'user_id' => null, // Optionally reset the user
            ]);
    }
}
