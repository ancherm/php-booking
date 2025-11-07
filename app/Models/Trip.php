<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    protected $fillable = [
        'route_id',
        'date',
        'free_places',
    ];

    protected $casts = [
        'date' => 'date',
    ];


    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }


    public function orders()
    {
        return $this->hasMany(Order::class, 'trip_id');
    }


    public function places()
    {
        return $this->hasMany(Place::class, 'trip_id');
    }


    public function hasAvailablePlaces($count = 1): bool
    {
        return $this->free_places >= $count;
    }


    public function reservePlaces($count)
    {
        $this->free_places -= $count;
        $this->save();
    }
}
