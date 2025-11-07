<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'email',
        'phone',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }


    public function orders()
    {
        return $this->hasMany(Order::class, 'client_id');
    }

    /**
     * Связь с пассажирами
     */
    public function passengers()
    {
        return $this->hasMany(Passenger::class, 'client_id');
    }
}
