<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriversLicense extends Model
{
    use HasFactory;

    protected $fillable = [
        "gender",
        "licenseNo",
        "firstName",
        "lastName",
        "middleName",
        "issuedDate",
        "expiryDate",
        "stateOfIssue",
        "birthDate",
        "photo"
    ];
    
    protected $hidden = [
        'id',
    ];
}
