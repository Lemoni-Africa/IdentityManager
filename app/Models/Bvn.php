<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bvn extends Model
{
    use HasFactory;

    protected $fillable = [
        // 'number',
        'firstName',
        'middleName',
        'lastName',
        'dateOfBirth',
        'phoneNumber1',
        'phoneNumber2',
        'registrationDate',
        'enrollmentBank',
        'enrollmentBranch',
        'email',
        'gender',
        'levelOfAccount',
        'lgaOfOrigin',
        'lgaOfResidence',
        'maritalStatus',
        'nin',
        'nameOnCard',
        'nationality',
        'residentialAddress',
        'stateOfOrigin',
        'stateOfResidence',
        'title',
        'watchListed',
        'bvn',
        'base64Image',
        'provider'
    ];
    protected $hidden = [
        'id',
    ];
}
