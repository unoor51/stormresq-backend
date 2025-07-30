<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Rescuer extends Authenticatable
{
    use HasApiTokens,Notifiable;

    protected $fillable = [
        'phone',
        'first_name',
        'last_name',
        'email',
        'password',
        'status',
    ];

    protected $hidden = [
        'password',
    ];
    
    public function evacuees()
    {
        return $this->hasMany(Evacuee::class);
    }
}