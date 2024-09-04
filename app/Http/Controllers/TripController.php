<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Http\Requests\StoreTripRequest;
use App\Http\Requests\UpdateTripRequest;
use App\Http\Resources\TripResource;

class TripController extends Controller
{
    /**
     * Display a listing of trips.
     */
    public function index()
    {
        $trips = Trip::paginate(10); // Adjust pagination as needed
        return TripResource::collection($trips);
    }

    /**
     * Store a newly created trip in storage.
     */
    public function store(StoreTripRequest $request)
    {
        $trip = Trip::create($request->validated());

        return new TripResource($trip);
    }

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
