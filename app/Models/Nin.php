<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nin extends Model
{
    use HasFactory;

    protected $fillable = [
        "employmentstatus",
        "gender",
        "heigth",
        "maritalstatus",
        "title",
        "birthcountry",
        "birthdate",
        "birthlga",
        "birthstate",
        "educationallevel",
        "email",
        "firstname",
        "surname",
        "nin",
        "nok_address1",
        "nok_address2",
        "nok_firstname",
        "nok_lga",
        "nok_middlename",
        "nok_postalcode",
        "nok_state",
        "nok_surname",
        "nok_town",
        "spoken_language",
        "ospokenlang",
        "pfirstname",
        "photo",
        "middlename",
        "pmiddlename",
        "profession",
        "psurname",
        "religion",
        "residence_address",
        "residence_town",
        "residence_lga",
        "residence_state",
        "residencestatus",
        "self_origin_lga",
        "self_origin_place",
        "self_origin_state",
        "signature",
        "telephoneno",
        "trackingId"
    ];
    protected $hidden = [
        'id',
    ];
}
