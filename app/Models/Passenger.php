<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Passenger extends Model
{
    protected $fillable = [
        'client_id',
        'first_name',
        'last_name',
        'passport',
    ];


    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }


    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_passengers', 'passenger_id', 'order_id')
            ->withPivot('ticket')
            ->withTimestamps();
    }


    public function places()
    {
        return $this->hasMany(Place::class, 'passenger_id');
    }


    public function getFullNameAttribute(): string
    {
        return $this->last_name . ' ' . $this->first_name;
    }
}
