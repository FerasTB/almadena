<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_id',
        'departure_time',
        'passenger_cost',
        'note',
        'trip_day',
        'trip_time',
    ];


    public function routes()
    {
        return $this->hasMany(Route::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function getAvailableSeatsAttribute()
    {
        $totalSeats = $this->template->seat_count;
        $approvedBookings = $this->bookings()->where('status', 'approved')->count();

        return $totalSeats - $approvedBookings;
    }

    public function getFirstPointAttribute()
    {
        return $this->routes()->first()->name ?? null;
    }

    public function getLastPointAttribute()
    {
        return $this->routes()->latest()->first()->name ?? null;
    }
}
