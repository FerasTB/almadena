<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $fillable = [
        'seats_count',
    ];

    /**
     * Get the trips that use this template.
     */
    public function trips()
    {
        return $this->hasMany(Trip::class);
    }
}
