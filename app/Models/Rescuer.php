<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Rescuer extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = [
        'phone',
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];
    
    public function evacuees()
    {
        return $this->hasMany(Evacuee::class);
    }
}