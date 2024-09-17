<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Http\Requests\StoreTripRequest;
use App\Http\Requests\UpdateTripRequest;
use App\Http\Resources\TripResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class TripController extends Controller
{
    /**
     * Display a listing of trips.
     */
    public function index()
    {
        $trips = Trip::all(); // Adjust pagination as needed
        return TripResource::collection($trips);
    }

    /**
     * Store a newly created trip in storage.
     */
    public function store(StoreTripRequest $request)
    {
        // Wrap everything in a DB transaction
        DB::beginTransaction();

        try {
            // Create the trip with validated data
            $trip = Trip::create($request->only(['departure_time', 'passenger_cost', 'note', 'trip_day', 'trip_time', 'template_id']));

            // Add points to the trip with a sequential 'number'
            $pointNumber = 1;
            foreach ($request->points as $point) {
                // Add the 'number' field to each point
                $point['number'] = $pointNumber++;
                $trip->routes()->create($point); // Create each point related to this trip
            }

            $trip->load('template');
            // Fetch the seat count from the related template
            $seatCount = $trip->template->seat_count;

            // Generate seats for the trip based on seat count with 'available' status
            for ($i = 1; $i <= $seatCount; $i++) {
                $trip->bookings()->create([
                    'seat_number' => $i,
                    'status' => 'available',
                ]);
            }

            // Commit the transaction if everything is successful
            DB::commit();

            // Return the created trip as a resource
            return new TripResource($trip);
        } catch (\Exception $e) {
            // Rollback the transaction if something fails
            DB::rollBack();

            // Return the error response with the exception message
            return response()->json([
                'error' => 'Trip creation failed.',
                'message' => $e->getMessage(), // Include the error message
            ], 500);
        }
    }


    // public function show($id)
    // {
    //     $trip = Trip::findOrFail($id);
    //     return new TripResource($trip);
    // }

    /**
     * Display the specified trip.
     */
    public function show(Trip $trip)
    {

        return new TripResource($trip);
    }

    /**
     * Update the specified trip in storage.
     */
    public function update(UpdateTripRequest $request, Trip $trip)
    {
        $trip->update($request->validated());

        return new TripResource($trip);
    }

    /**
     * Remove the specified trip from storage.
     */
    public function destroy(Trip $trip)
    {
        $trip->delete();

        return response()->json(['message' => 'Trip deleted successfully']);
    }
}
