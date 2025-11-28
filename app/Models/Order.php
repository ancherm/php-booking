<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'trip_id',
        'client_id',
    ];


    public function trip()
    {
        return $this->belongsTo(Trip::class, 'trip_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    
    public function orderPassengers()
    {
        return $this->hasMany(OrderPassenger::class, 'order_id');
    }


    public function passengers()
    {
        return $this->belongsToMany(Passenger::class, 'order_passengers', 'order_id', 'passenger_id')
            ->withPivot('ticket')
            ->withTimestamps();
    }
}
