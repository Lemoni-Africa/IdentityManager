<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NationalPassport extends Model
{
    use HasFactory;

    protected $fillable = [
        "request_number",
        "first_name",
        "middle_name",
        "last_name",
        "mobile",
        "phone",
        "gender",
        "dob",
        'issued_at',
        'issued_date',
        'expiry_date',
        'reference_id',
        'date_created',
    ];
    
    protected $hidden = [
        'id',
    ];
}
