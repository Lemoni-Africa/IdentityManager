<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneNumber extends Model
{
    use HasFactory;

    protected $fillable = [
        "nin",
        "firstname",
        "middlename",
        "surname",
        "maidenname",
        "telephoneno",
        "state",
        "place",
        "title",
        "height",
        "email",
        "birthdate",
        "birthstate",
        "birthcountry",
        "centralID",
        "documentno",
        "educationallevel",
        "employmentstatus",
        "maritalstatus",
        "nok_firstname",
        "nok_middlename",
        "nok_address1",
        "nok_address2",
        "nok_lga",
        "nok_state",
        "nok_town",
        "nok_postalcode",
        "othername",
        "pfirstname",
        "photo",
        "pmiddlename",
        "psurname",
        "profession",
        "nspokenlang",
        "ospokenlang",
        "religion",
        "residence_town",
        "residence_lga",
        "residence_state",
        "residencestatus",
        "residence_AddressLine1",
        "residence_AddressLine2",
        "self_origin_lga",
        "self_origin_place",
        "self_origin_state",
        "signature",
        "nationality",
        "gender",
        "trackingId"
    ];
    protected $hidden = [
        'id',
    ];
}

