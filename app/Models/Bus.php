<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    public $incrementing = false;
    protected $keyType = 'integer';

    protected $fillable = [
        'id',
        'name',
        'places',
    ];


    public function routes()
    {
        return $this->hasMany(Route::class, 'bus_id');
    }
}
