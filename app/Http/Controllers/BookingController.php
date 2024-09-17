<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Jobs\RevertPendingSeats;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{    // 1. Get available seats for a specific trip
    public function getSeats(Trip $trip)
    {
        // Fetch the trip with its seats
        $trip->load('bookings');

        // Get all seats associated with the trip
        $seats = $trip->bookings->map(function ($seat) {
            return [
                'id' => $seat->id,
                'seat_number' => $seat->seat_number,
                'status' => $seat->status,
                'paymentCode' => $seat->payment_code, // Include payment code if present
            ];
        });

        return response()->json($seats);
    }
    // 2. Book selected seats
    public function bookSeats(Request $request, Trip $trip)
    {
        $validated = $request->validate([
            'seat_ids' => 'required|array',
            'seat_ids.*' => 'integer',
            'payment_code' => 'required|string',
        ]);

        // Fetch the trip to validate the seat count
        $trip->load('template');
        $totalSeats = $trip->template->seat_count;

        // Ensure selected seat IDs are within range of the total seat count
        // foreach ($validated['seat_ids'] as $seatId) {
        //     if ($seatId < 1 || $seatId > $totalSeats) {
        //         return response()->json(['error' => 'Invalid seat selection.'], 400);
        //     }
        // }

        // Check if any of the selected seats are already booked or pending
        $existingSeats = Booking::whereIn('seat_number', $validated['seat_ids'])
            ->where('trip_id', $trip->id)
            ->whereIn('status', ['booked', 'pending'])
            ->pluck('seat_number')
            ->toArray();

        if (!empty($existingSeats)) {
            return response()->json(['error' => 'Some seats are already booked or pending.'], 400);
        }

        // Update the status of the selected seats to 'pending'
        Booking::whereIn('id', $validated['seat_ids'])
            ->where('trip_id', $trip->id)
            ->update([
                'status' => 'pending',
                'payment_code' => $validated['payment_code'],
                'user_id' => auth()->id(),
            ]);

        // Schedule the job to revert the seat status after 3 hours
        $job = new RevertPendingSeats($validated['seat_ids'], $trip->id);
        dispatch($job)->delay(Carbon::now()->addHours(3));

        return response()->json(['message' => 'Booking submitted for approval.']);
    }


    // 3. Update pending booking (e.g. payment code)
    public function updateSeat(Request $request, Trip $trip, $booking)
    {
        $validated = $request->validate([
            'payment_code' => 'required|string'
        ]);

        $seat = Booking::where('trip_id', $trip->id)
            ->where('seat_number', $booking)
            ->where('status', 'pending')
            ->firstOrFail();

        $seat->update([
            'payment_code' => $validated['payment_code']
        ]);

        return response()->json(['message' => 'Payment code updated successfully.']);
    }

    // 4. Get pending bookings for the current user
    public function getPendingBookings()
    {
        $Seats = Booking::where('status', 'pending')
            ->where('user_id', auth()->id())
            ->get();

        return BookingResource::collection($Seats);
    }

    public function getAllBookings(Request $request)
    {
        // Retrieve all bookings from the database
        $bookings = Booking::where('user_id', auth()->id());

        // Map the booking data to match the required interface format
        $formattedBookings = $bookings->map(function ($booking) {
            // Assuming 'created_at' and 'payment_code' exist in your Booking model

            // Calculate the cancellation deadline (3 hours from booking creation)
            $cancelDeadline = $booking->updated_at->addHours(3);

            // Calculate the remaining time in minutes
            $remainingMinutes = now()->diffInMinutes($cancelDeadline, false); // Use 'false' to get negative values if past deadline

            return [
                'id' => $booking->id,
                'tripId' => $booking->trip_id,
                'status' => $booking->status,
                'createdAt' => $booking->created_at->toDateTimeString(),
                'paymentCode' => $booking->payment_code,
                'cancelDeadline' => $remainingMinutes > 0 ? $remainingMinutes : '0' // Show 'Expired' if past deadline
            ];
        });

        // Return the formatted bookings as a JSON response
        return response()->json($formattedBookings);
    }


    // Helper function to calculate the cancel deadline
    private function calculateCancelDeadline($createdAt)
    {
        // Set the cancellation deadline to 3 hours after booking creation
        return Carbon::parse($createdAt)->addHours(3)->toDateTimeString();
    }
}
