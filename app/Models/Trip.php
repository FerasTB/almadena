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

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    /**
     * Get the routes associated with the trip.
     */
    public function routes()
    {
        return $this->hasMany(Route::class);
    }
}
