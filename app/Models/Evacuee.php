<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evacuee extends Model
{
    use HasFactory;
    protected $fillable = [
        'phone',
        'people_count',
        'situation',
        'needs_pet',
        'needs_disabled',
        'latitude',
        'longitude',
    ];

}
