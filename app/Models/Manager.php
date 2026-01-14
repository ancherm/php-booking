<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Manager extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'department',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }
}


