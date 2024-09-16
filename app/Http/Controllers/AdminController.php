<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\RegisterUserAdminRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Booking;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $users = User::paginate(10); // Paginate results
        return UserResource::collection($users);
    }

    public function store(CreateUserRequest $request)
    {
        // Create a new user with the default password
        $user = User::create(array_merge(
            $request->validated(),
            ['password' => '12345678']
        ));

        return new UserResource($user);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->validated());

        return new UserResource($user);
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    /**
     * Toggle the admin status of the specified user.
     */
    public function toggleAdmin(User $user)
    {
        $user->is_admin = !$user->is_admin;
        $user->save();

        return response()->json(['message' => 'User admin status updated successfully', 'is_admin' => $user->is_admin]);
    }

    public function getSeats(Trip $trip)
    {
        // Fetch the trip with its related bookings
        $trip->load('bookings');

        // Initialize an empty array to store grouped bookings
        $groupedSeats = [];

        // Iterate through each booking and group by payment code and status
        foreach ($trip->bookings as $booking) {
            // Check if the booking is available (assuming 'available' is the status for open bookings)
            // if ($booking->status === 'available') {
            //     // Treat available bookings as individual entries with the payment code "متاح للحجز"
            //     $groupedSeats[] = [
            //         'bookingIds' => [$booking->id],
            //         'seatNumbers' => [$booking->seat_number],
            //         'paymentCode' => 'متاح للحجز',
            //         'status' => $booking->status,
            //     ];
            // } else {
            // For other bookings, group by payment code and status
            $groupKey = $booking->payment_code . '-' . $booking->status;

            // If the group already exists, append the seat number and booking ID to the existing entry
            // if (isset($groupedSeats[$groupKey])) {
            //     $groupedSeats[$groupKey]['seatNumbers'][] = $booking->seat_number;
            //     $groupedSeats[$groupKey]['bookingIds'][] = $booking->id;
            // } else {
            // Create a new entry for the group
            $groupedSeats[$booking->id] = [
                'bookingIds' => [$booking->id], // Initialize with the first booking ID
                'seatNumbers' => [$booking->seat_number], // Initialize with the first seat number
                'paymentCode' => $booking->payment_code,
                'status' => $booking->status, // Grouped by status
            ];
            //     }
            // }
        }

        // Convert the associative array to a numeric array to match the desired format
        $seats = array_values($groupedSeats);

        return response()->json($seats);
    }



    public function approveBookings(Request $request)
    {
        // Validate the incoming request to ensure we have an array of booking IDs
        $validatedData = $request->validate([
            'booking_ids' => 'required|array',
            'booking_ids.*' => 'exists:bookings,id', // Ensure each ID exists in the bookings table
            'user_id' => 'nullable|exists:users,id', // Validate user_id if provided
        ]);

        // Prepare the data to be updated
        $updateData = ['status' => 'approved'];

        // If the request has a user_id, include it in the update data
        if ($request->has('user_id')) {
            $updateData['user_id'] = $request->input('user_id');
        }

        // Fetch the bookings by their IDs and update their status and user_id if applicable
        Booking::whereIn('id', $validatedData['booking_ids'])
            ->update($updateData);

        return response()->json(['message' => 'تمت الموافقة على الحجوزات بنجاح.']);
    }


    public function rejectBookings(Request $request)
    {
        // Validate the incoming request to ensure we have an array of booking IDs
        $validatedData = $request->validate([
            'booking_ids' => 'required|array',
            'booking_ids.*' => 'exists:bookings,id', // Ensure each ID exists in the bookings table
        ]);

        // Fetch the bookings by their IDs and update their status to 'rejected'
        Booking::whereIn('id', $validatedData['booking_ids'])
            ->update(['status' => 'available']);

        return response()->json(['message' => 'تم رفض الحجوزات وتمت إعادة المقاعد إلى المتاحة.']);
    }

    public function getNonAdminUsers()
    {
        // Fetch users who are not admins (assuming is_admin column where 0 is non-admin)
        // $nonAdminUsers = User::where('is_admin', 0)
        $nonAdminUsers = User::all()
            ->map(function ($user) {
                // Concatenate first_name, middle_name, last_name, and mother_name into the desired format
                $fullName = $user->first_name . ' ' .
                    ($user->middle_name ? $user->middle_name . ' ' : '') .
                    $user->last_name . ' - ' . $user->mother_name;

                return [
                    'id' => $user->id,
                    'name' => $fullName,
                    'phone' => $user->phone,
                ];
            });

        // Return the list of users as a JSON response
        return response()->json($nonAdminUsers);
    }

    public function registerWithoutPassword(RegisterUserAdminRequest $request)
    {
        // Set a default password ('12345678') for the user
        $defaultPassword = bcrypt('12345678'); // Encrypt the password

        // Merge the default password with the validated request data
        $userData = array_merge($request->validated(), ['password' => $defaultPassword]);

        // Create the user using the merged data
        $user = User::create($userData);
        $fullName = $user->first_name . ' ' .
            ($user->middle_name ? $user->middle_name . ' ' : '') .
            $user->last_name . ' - ' . $user->mother_name;

        $data = [
            'id' => $user->id,
            'name' => $fullName,
            'phone' => $user->phone,
        ];
        return response()->json($data, 201);
    }
}
