<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    protected $fillable = ['bus_id', 'number', 'is_window'];

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function isBooked()
    {
        return $this->tickets()->whereIn('status', ['pending', 'paid'])->exists();
    }
}
