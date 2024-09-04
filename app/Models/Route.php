<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'location_name',
        'expected_time_to_next',
    ];

    /**
     * Get the trip associated with the route.
     */
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
}
