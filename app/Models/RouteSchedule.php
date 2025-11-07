<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouteSchedule extends Model
{
    protected $primaryKey = 'route_id';
    public $incrementing = false;

    protected $fillable = [
        'route_id',
        'from_date',
        'to_date',
        'period',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
    ];


    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }
}
