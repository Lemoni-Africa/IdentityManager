<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VotersCard extends Model
{
    use HasFactory;

    protected $fillable = [
        "gender",
        "vin",
        "first_name",
        "last_name",
        "date_of_birth",
        "fullName",
        "occupation",
        "timeOfRegistration",
        "lga",
        "state",
        "registrationAreaWard",
        "pollingUnit",
        "pollingUnitCode"
    ];
    
    protected $hidden = [
        'id',
    ];
}
