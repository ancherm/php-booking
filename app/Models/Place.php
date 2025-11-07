<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    protected $fillable = [
        'trip_id',
        'number_place',
        'passenger_id',
    ];


    public function trip()
    {
        return $this->belongsTo(Trip::class, 'trip_id');
    }


    public function passenger()
    {
        return $this->belongsTo(Passenger::class, 'passenger_id');
    }


    public function isAvailable(): bool
    {
        return is_null($this->passenger_id);
    }
}
