<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderPassenger extends Model
{
    protected $fillable = [
        'ticket',
        'order_id',
        'passenger_id',
    ];


    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }


    public function passenger()
    {
        return $this->belongsTo(Passenger::class, 'passenger_id');
    }
}
