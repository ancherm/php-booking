<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'position',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }
}
