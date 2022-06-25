<?php

use App\Models\Bvn;
use App\Models\DriversLicense;
use App\Models\NationalPassport;
use App\Models\Nin;
use App\Models\PhoneNumber;
use App\Models\VotersCard;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

function checkExpiryDate($date)
{
    $date_now = date("Y-m-d");
    if ($date_now > $date) {
        return true;
    }else{
        return false;
    }
}

function compareText($firstText, $secondText)
{
    if (strtolower(trim($firstText)) == strtolower(trim($secondText))) {
        return true;
    } else{
        return false;
    }

}

function saveBvn($decodedJson)
{
    $newBvn = new Bvn;
    $newBvn->firstName = $decodedJson['bvn_data']['firstName'];
    $newBvn->middleName = $decodedJson['bvn_data']['middleName'];
    $newBvn->lastName = $decodedJson['bvn_data']['lastName'];
    $newBvn->dateOfBirth = $decodedJson['bvn_data']['dateOfBirth'];
    $newBvn->phoneNumber1 = $decodedJson['bvn_data']['phoneNumber1'];
    $newBvn->phoneNumber2 = $decodedJson['bvn_data']['phoneNumber2'];
    $newBvn->registrationDate = $decodedJson['bvn_data']['registrationDate'];
    $newBvn->enrollmentBank = $decodedJson['bvn_data']['enrollmentBank'];
    $newBvn->enrollmentBranch = $decodedJson['bvn_data']['enrollmentBranch'];
    $newBvn->email = $decodedJson['bvn_data']['email'];
    $newBvn->gender = $decodedJson['bvn_data']['gender'];
    $newBvn->levelOfAccount = $decodedJson['bvn_data']['levelOfAccount'];
    $newBvn->lgaOfOrigin = $decodedJson['bvn_data']['lgaOfOrigin'];
    $newBvn->lgaOfResidence = $decodedJson['bvn_data']['lgaOfResidence'];
    $newBvn->maritalStatus = $decodedJson['bvn_data']['maritalStatus'];
    $newBvn->nin = $decodedJson['bvn_data']['nin'];
    $newBvn->nameOnCard = $decodedJson['bvn_data']['nameOnCard'];
    $newBvn->nationality = $decodedJson['bvn_data']['nationality'];
    $newBvn->residentialAddress = $decodedJson['bvn_data']['residentialAddress'];
    $newBvn->stateOfOrigin = $decodedJson['bvn_data']['stateOfOrigin'];
    $newBvn->stateOfResidence = $decodedJson['bvn_data']['stateOfResidence'];
    $newBvn->title = $decodedJson['bvn_data']['title'];
    $newBvn->watchListed = $decodedJson['bvn_data']['watchListed'];
    $newBvn->bvn = $decodedJson['bvn_data']['bvn'];
    $newBvn->base64Image = $decodedJson['bvn_data']['base64Image'];
    $newBvn->provider = "identityPass";
    $newBvn->save();

    return $newBvn;
}

function saveBvn2($decodedJson)
{
    $newBvn = new Bvn;
    $newBvn->firstName = $decodedJson['entity']['first_name'];
    $newBvn->middleName = $decodedJson['entity']['middle_name'];
    $newBvn->lastName = $decodedJson['entity']['last_name'];
    $newBvn->dateOfBirth = date('Y-m-d',strtotime($decodedJson['entity']['date_of_birth']));;
    $newBvn->phoneNumber1 = $decodedJson['entity']['phone_number1'];
    $newBvn->phoneNumber2 = $decodedJson['entity']['phone_number2'];
    $newBvn->registrationDate = date('Y-m-d', strtotime($decodedJson['entity']['registration_date']));
    $newBvn->enrollmentBank = $decodedJson['entity']['enrollment_bank'];
    $newBvn->enrollmentBranch = $decodedJson['entity']['enrollment_branch'];
    $newBvn->email = strtolower($decodedJson['entity']['email']);
    $newBvn->gender = $decodedJson['entity']['gender'];
    $newBvn->levelOfAccount = $decodedJson['entity']['level_of_account'];
    $newBvn->lgaOfOrigin = $decodedJson['entity']['lga_of_origin'];
    $newBvn->lgaOfResidence = $decodedJson['entity']['lga_of_residence'];
    $newBvn->maritalStatus = $decodedJson['entity']['marital_status'];
    $newBvn->nin = $decodedJson['entity']['nin'];
    $newBvn->nameOnCard = $decodedJson['entity']['name_on_card'];
    $newBvn->nationality = $decodedJson['entity']['nationality'];
    $newBvn->residentialAddress = $decodedJson['entity']['residential_address'];
    $newBvn->stateOfOrigin = $decodedJson['entity']['state_of_origin'];
    $newBvn->stateOfResidence = $decodedJson['entity']['state_of_residence'];
    $newBvn->title = $decodedJson['entity']['title'];
    $newBvn->watchListed = $decodedJson['entity']['watch_listed'];
    $newBvn->bvn = $decodedJson['entity']['bvn'];
    $newBvn->base64Image = $decodedJson['entity']['image'];
    $newBvn->provider = "Dojah";
    $newBvn->save();

    return $newBvn;
}


function savePhoneNumber($decodedJson)
{

    $newPhoneNumber = new PhoneNumber;
    $newPhoneNumber->nin = $decodedJson['data']['nin'];
    $newPhoneNumber->firstname = $decodedJson['data']['firstname'];
    $newPhoneNumber->middlename = $decodedJson['data']['middlename'];
    $newPhoneNumber->surname = $decodedJson['data']['surname'];
    // $newPhoneNumber->maidenname = $decodedJson['data']['maidenname'] || null;
    $newPhoneNumber->telephoneno = $decodedJson['data']['telephoneno'];
    // $newPhoneNumber->state = $decodedJson['data']['state'];
    // $newPhoneNumber->place = $decodedJson['data']['place'];
    $newPhoneNumber->title = $decodedJson['data']['title'];
    // $newPhoneNumber->height = $decodedJson['data']['height'];
    $newPhoneNumber->email = $decodedJson['data']['email'];
    $newPhoneNumber->birthdate = $decodedJson['data']['birthdate'];
    $newPhoneNumber->birthstate = $decodedJson['data']['birthstate'];
    $newPhoneNumber->birthcountry = $decodedJson['data']['birthcountry'];
    // $newPhoneNumber->centralID = $decodedJson['data']['centralID'];
    // $newPhoneNumber->documentno = $decodedJson['data']['documentno'];
    $newPhoneNumber->educationallevel = $decodedJson['data']['educationallevel'];
    $newPhoneNumber->employmentstatus = $decodedJson['data']['employmentstatus'];
    $newPhoneNumber->maritalstatus = $decodedJson['data']['maritalstatus'];
    $newPhoneNumber->nok_firstname = $decodedJson['data']['nok_firstname'];
    $newPhoneNumber->nok_middlename = $decodedJson['data']['nok_middlename'];
    $newPhoneNumber->nok_address1 = $decodedJson['data']['nok_address1'];
    $newPhoneNumber->nok_address2 = $decodedJson['data']['nok_address2'];
    $newPhoneNumber->nok_lga = $decodedJson['data']['nok_lga'];
    $newPhoneNumber->nok_state = $decodedJson['data']['nok_state'];
    $newPhoneNumber->nok_town = $decodedJson['data']['nok_town'];
    $newPhoneNumber->nok_postalcode = $decodedJson['data']['nok_postalcode'];
    // $newPhoneNumber->othername = $decodedJson['data']['othername'];
    $newPhoneNumber->pfirstname = $decodedJson['data']['pfirstname'];
    $newPhoneNumber->photo = $decodedJson['data']['photo'];
    $newPhoneNumber->pmiddlename = $decodedJson['data']['pmiddlename'];
    $newPhoneNumber->psurname = $decodedJson['data']['psurname'];
    $newPhoneNumber->profession = $decodedJson['data']['profession'];
    // $newPhoneNumber->nspokenlang = $decodedJson['data']['nspokenlang'];
    $newPhoneNumber->ospokenlang = $decodedJson['data']['ospokenlang'];
    $newPhoneNumber->religion = $decodedJson['data']['religion'];
    $newPhoneNumber->residence_town = $decodedJson['data']['residence_town'];
    $newPhoneNumber->residence_lga = $decodedJson['data']['residence_lga'];
    $newPhoneNumber->residence_state = $decodedJson['data']['residence_state'];
    $newPhoneNumber->residencestatus = $decodedJson['data']['residencestatus'];
    // $newPhoneNumber->residence_AddressLine1 = $decodedJson['data']['residence_AddressLine1'];
    // $newPhoneNumber->residence_AddressLine2 = $decodedJson['data']['residence_AddressLine2'];
    $newPhoneNumber->self_origin_lga = $decodedJson['data']['self_origin_lga'];
    $newPhoneNumber->self_origin_place = $decodedJson['data']['self_origin_place'];
    $newPhoneNumber->self_origin_state = $decodedJson['data']['self_origin_state'];
    $newPhoneNumber->signature = $decodedJson['data']['signature'];
    // $newPhoneNumber->nationality = $decodedJson['data']['nationality'];
    $newPhoneNumber->gender = $decodedJson['data']['gender'];
    $newPhoneNumber->trackingId = $decodedJson['data']['trackingId'];

    $newPhoneNumber->save();

    return $newPhoneNumber;
}

function savePhoneNumber2($decodedJson)
{

    $newPhoneNumber = new PhoneNumber;
    $newPhoneNumber->nin = $decodedJson['entity']['nin'];
    $newPhoneNumber->firstname = $decodedJson['entity']['firstName'];
    $newPhoneNumber->middlename = $decodedJson['entity']['middleName'];
    $newPhoneNumber->surname = $decodedJson['entity']['lastName'];
    // $newPhoneNumber->maidenname = $decodedJson['data']['maidenname'] || null;
    $newPhoneNumber->telephoneno = $decodedJson['entity']['msisdn'];
    $newPhoneNumber->state = $decodedJson['entity']['state'];
    // $newPhoneNumber->place = $decodedJson['data']['place'];
    $newPhoneNumber->title = $decodedJson['entity']['title'];
    $newPhoneNumber->height = $decodedJson['entity']['height'];
    // $newPhoneNumber->email = $decodedJson['entity']['email'] || '';
    $newPhoneNumber->birthdate = date('Y-m-d',strtotime($decodedJson['entity']['birthDate']));


    $newPhoneNumber->birthlga = $decodedJson['entity']['birthLga'];

    $newPhoneNumber->birthstate = $decodedJson['entity']['birthState'];
    // $newPhoneNumber->birthcountry = $decodedJson['entity']['birthcountry'] || '';
    // $newPhoneNumber->centralID = $decodedJson['data']['centralID'];
    // $newPhoneNumber->documentno = $decodedJson['data']['documentno'];
    $newPhoneNumber->educationallevel = $decodedJson['entity']['educationalLevel'];
    $newPhoneNumber->employmentstatus = $decodedJson['entity']['emplymentStatus'];
    $newPhoneNumber->maritalstatus = $decodedJson['entity']['maritalStatus'];
    $newPhoneNumber->nok_firstname = $decodedJson['entity']['firstName'];
    $newPhoneNumber->nok_middlename = $decodedJson['entity']['middleName'];
    // $newPhoneNumber->nok_address1 = $decodedJson['entity']['nok_address1'] || '';
    // $newPhoneNumber->nok_address2 = $decodedJson['entity']['nok_address2'] || '';
    $newPhoneNumber->nok_lga = $decodedJson['entity']['lga'];
    $newPhoneNumber->nok_state = $decodedJson['entity']['state'];
    // $newPhoneNumber->nok_town = $decodedJson['entity']['nok_town'] || '';
    // $newPhoneNumber->nok_postalcode = $decodedJson['entity']['nok_postalcode'] || '';
    // $newPhoneNumber->othername = $decodedJson['data']['othername'];
    $newPhoneNumber->pfirstname = $decodedJson['entity']['firstName'];
    $newPhoneNumber->photo = $decodedJson['entity']['picture'];
    $newPhoneNumber->pmiddlename = $decodedJson['entity']['middleName'];
    $newPhoneNumber->psurname = $decodedJson['entity']['lastName'];
    $newPhoneNumber->profession = $decodedJson['entity']['profession'];
    $newPhoneNumber->nspokenlang = $decodedJson['entity']['nspokenLang'];
    $newPhoneNumber->ospokenlang = $decodedJson['entity']['ospokenlang'];
    $newPhoneNumber->religion = $decodedJson['entity']['religion'];
    $newPhoneNumber->residence_town = $decodedJson['entity']['residenceTown'];
    $newPhoneNumber->residence_lga = $decodedJson['entity']['residenceLga'];
    $newPhoneNumber->residence_state = $decodedJson['entity']['residenceState'];
    $newPhoneNumber->residencestatus = $decodedJson['entity']['residenceStatus'];
    $newPhoneNumber->residence_AddressLine1 = $decodedJson['entity']['residenceAddressLine1'];
    // $newPhoneNumber->residence_AddressLine2 = $decodedJson['data']['residence_AddressLine2'];
    $newPhoneNumber->self_origin_lga = $decodedJson['entity']['selfOriginLga'];
    $newPhoneNumber->self_origin_place = $decodedJson['entity']['selfOriginPlace'];
    $newPhoneNumber->self_origin_state = $decodedJson['entity']['selfOriginState'];
    // $newPhoneNumber->signature = $decodedJson['entity']['signature'] || '';
    // $newPhoneNumber->nationality = $decodedJson['data']['nationality'];
    $newPhoneNumber->gender = $decodedJson['entity']['gender'];
    // $newPhoneNumber->trackingId = $decodedJson['entity']['trackingId'] || '';

    $newPhoneNumber->provider = "Dojah";

    $newPhoneNumber->save();

    return $newPhoneNumber;
}

function saveDriversLicence($decodedJson)
{
    $newLicense = new DriversLicense;
    $newLicense->gender = $decodedJson['data']['gender'];
    $newLicense->licenseNo = $decodedJson['data']['licenseNo'];
    $newLicense->firstName = $decodedJson['data']['firstName'];
    $newLicense->lastName = $decodedJson['data']['lastName'];
    $newLicense->middleName = $decodedJson['data']['middleName'];
    $newLicense->issuedDate = $decodedJson['data']['issuedDate'];
    $newLicense->expiryDate = $decodedJson['data']['expiryDate'];
    $newLicense->stateOfIssue = $decodedJson['data']['stateOfIssue'];
    $newLicense->birthDate = $decodedJson['data']['birthDate'];
    $newLicense->photo = $decodedJson['data']['photo'];
    $newLicense->provider = "identityPass";
    $newLicense->save();
    return $newLicense;
}

function saveDriversLicence2($decodedJson)
{
    $newLicense = new DriversLicense;
    $newLicense->gender = $decodedJson['entity']['gender'];
    $newLicense->licenseNo = $decodedJson['entity']['licenseNo'];
    $newLicense->firstName = $decodedJson['entity']['firstName'];
    $newLicense->lastName = $decodedJson['entity']['lastName'];
    $newLicense->middleName = $decodedJson['entity']['middleName'];
    $newLicense->issuedDate = $decodedJson['entity']['issuedDate'];
    $newLicense->issuedDate = date('Y-m-d',strtotime($decodedJson['entity']['issuedDate']));
    $newLicense->stateOfIssue = $decodedJson['entity']['stateOfIssue'];
    $newLicense->expiryDate = date('Y-m-d',strtotime($decodedJson['entity']['expiryDate']));
    $newLicense->birthDate = date('Y-m-d',strtotime($decodedJson['entity']['birthDate']));
    $newLicense->photo = $decodedJson['entity']['photo'];
    $newLicense->uuid = $decodedJson['entity']['uuid'];
    $newLicense->provider = "Dojah";
    $newLicense->save();
    return $newLicense;
}

function saveVotersCard($decodedJson)
{
    $newVotersCard = new VotersCard;
    $newVotersCard->gender = $decodedJson['vc_data']['gender'];
    $newVotersCard->vin = $decodedJson['vc_data']['vin'];
    $newVotersCard->first_name = $decodedJson['vc_data']['first_name'];
    $newVotersCard->last_name = $decodedJson['vc_data']['last_name'];
    $newVotersCard->date_of_birth = $decodedJson['vc_data']['date_of_birth'];
    $newVotersCard->fullName = $decodedJson['vc_data']['fullName'];
    $newVotersCard->occupation = $decodedJson['vc_data']['occupation'];
    $newVotersCard->timeOfRegistration = $decodedJson['vc_data']['timeOfRegistration'];
    $newVotersCard->lga = $decodedJson['vc_data']['lga'];
    $newVotersCard->state = $decodedJson['vc_data']['state'];
    $newVotersCard->registrationAreaWard = $decodedJson['vc_data']['registrationAreaWard'];
    $newVotersCard->pollingUnit = $decodedJson['vc_data']['pollingUnit'];
    $newVotersCard->pollingUnitCode = $decodedJson['vc_data']['pollingUnitCode'];
    $newVotersCard->provider = "identityPass";
    $newVotersCard->save();

    return $newVotersCard;
}


function saveVotersCard2($decodedJson)
{
    $newVotersCard = new VotersCard;
    $newVotersCard->gender = $decodedJson['entity']['gender:'];
    $newVotersCard->vin = $decodedJson['entity']['voter_identification_number:'];
    // $newVotersCard->first_name = $decodedJson['entity']['first_name'];
    // $newVotersCard->last_name = $decodedJson['entity']['last_name'];
    // $newVotersCard->date_of_birth = $decodedJson['entity']['date_of_birth:'];
    $newVotersCard->fullName = $decodedJson['entity']['full_name'];
    $newVotersCard->occupation = $decodedJson['entity']['occupation:'];
    $newVotersCard->timeOfRegistration = $decodedJson['entity']['time_of_registration:'];
    $newVotersCard->lga = $decodedJson['entity']['local_government:'];
    $newVotersCard->state = $decodedJson['entity']['state:'];
    $newVotersCard->registrationAreaWard = $decodedJson['entity']['registration_area_ward:'];
    $newVotersCard->pollingUnit = $decodedJson['entity']['polling_unit:'];
    $newVotersCard->pollingUnitCode = $decodedJson['entity']['polling_unit_code'];
    $newVotersCard->provider = "Dojah";
    $newVotersCard->save();

    return $newVotersCard;
}

function saveNin($decodedJson)
{
    $newNin = new Nin;
    $newNin->employmentstatus = $decodedJson['nin_data']['employmentstatus'];
    $newNin->gender = $decodedJson['nin_data']['gender'];
    $newNin->heigth = $decodedJson['nin_data']['heigth'];
    $newNin->maritalstatus = $decodedJson['nin_data']['maritalstatus'];
    $newNin->title = $decodedJson['nin_data']['title'];
    $newNin->birthcountry = $decodedJson['nin_data']['birthcountry'];
    $newNin->birthdate = $decodedJson['nin_data']['birthdate'];
    $newNin->birthlga = $decodedJson['nin_data']['birthlga'];
    $newNin->birthstate = $decodedJson['nin_data']['birthstate'];
    $newNin->educationallevel = $decodedJson['nin_data']['educationallevel'];
    $newNin->email = $decodedJson['nin_data']['email'];
    $newNin->firstname = $decodedJson['nin_data']['firstname'];
    $newNin->surname = $decodedJson['nin_data']['surname'];
    $newNin->nin = $decodedJson['nin_data']['nin'];
    $newNin->nok_address1 = $decodedJson['nin_data']['nok_address1'];
    $newNin->nok_address2 = $decodedJson['nin_data']['nok_address2'];
    $newNin->nok_firstname = $decodedJson['nin_data']['nok_firstname'];
    $newNin->nok_lga = $decodedJson['nin_data']['nok_lga'];
    $newNin->nok_middlename = $decodedJson['nin_data']['nok_middlename'];
    $newNin->nok_postalcode = $decodedJson['nin_data']['nok_postalcode'];
    $newNin->nok_state = $decodedJson['nin_data']['nok_state'];
    $newNin->nok_surname = $decodedJson['nin_data']['nok_surname'];
    $newNin->nok_town = $decodedJson['nin_data']['nok_town'];
    $newNin->spoken_language = $decodedJson['nin_data']['spoken_language'];
    $newNin->ospokenlang = $decodedJson['nin_data']['ospokenlang'];
    $newNin->pfirstname = $decodedJson['nin_data']['pfirstname'];
    $newNin->photo = $decodedJson['nin_data']['photo'];
    $newNin->middlename = $decodedJson['nin_data']['middlename'] || '';
    $newNin->pmiddlename = $decodedJson['nin_data']['pmiddlename'];
    $newNin->profession = $decodedJson['nin_data']['profession'];
    $newNin->psurname = $decodedJson['nin_data']['psurname'];
    $newNin->religion = $decodedJson['nin_data']['religion'];
    $newNin->residence_address = $decodedJson['nin_data']['residence_address'];
    $newNin->residence_town = $decodedJson['nin_data']['residence_town'];
    $newNin->residence_lga = $decodedJson['nin_data']['residence_lga'];
    $newNin->residence_state = $decodedJson['nin_data']['residence_state'];
    $newNin->residencestatus = $decodedJson['nin_data']['residencestatus'];
    $newNin->self_origin_lga = $decodedJson['nin_data']['self_origin_lga'];
    $newNin->self_origin_place = $decodedJson['nin_data']['self_origin_place'];
    $newNin->self_origin_state = $decodedJson['nin_data']['self_origin_state'];
    $newNin->signature = $decodedJson['nin_data']['signature'];
    $newNin->telephoneno = $decodedJson['nin_data']['telephoneno'];
    $newNin->trackingId = $decodedJson['nin_data']['trackingId'];
    $newNin->provider = "identityPass";
    $newNin->save();

    return $newNin;
}


function saveNin2($decodedJson)
{
    $newNin = new Nin;
    $newNin->employmentstatus = $decodedJson['entity']['employmentstatus'];
    $newNin->gender = $decodedJson['entity']['gender'];
    $newNin->height = $decodedJson['entity']['height'];
    // $newNin->maritalstatus = $decodedJson['entity']['maritalstatus'];
    $newNin->title = $decodedJson['entity']['title'];
    $newNin->birthcountry = $decodedJson['entity']['birthcountry'];
    // $newNin->birthlga = $decodedJson['entity']['birthlga'];
    $newNin->birthstate = $decodedJson['entity']['birthstate'];
    $newNin->birthdate = date('Y-m-d',strtotime($decodedJson['entity']['birthdate']));
    $newNin->educationallevel = $decodedJson['entity']['educationallevel'];
    $newNin->email = $decodedJson['entity']['email'];
    $newNin->firstname = $decodedJson['entity']['firstname'];
    $newNin->surname = $decodedJson['entity']['surname'];
    $newNin->nin = $decodedJson['entity']['nin'];
    $newNin->nok_address1 = $decodedJson['entity']['nok_address1'];
    $newNin->nok_address2 = $decodedJson['entity']['nok_address2'];
    $newNin->nok_firstname = $decodedJson['entity']['nok_firstname'];
    $newNin->nok_lga = $decodedJson['entity']['nok_lga'];
    $newNin->nok_middlename = $decodedJson['entity']['nok_middlename'];
    $newNin->nok_postalcode = $decodedJson['entity']['nok_postalcode'];
    $newNin->nok_state = $decodedJson['entity']['nok_state'];
    $newNin->nok_surname = $decodedJson['entity']['nok_lastname'];
    $newNin->nok_town = $decodedJson['entity']['nok_town'];
    // $newNin->spoken_language = $decodedJson['entity']['spoken_language'];
    $newNin->ospokenlang = $decodedJson['entity']['ospokenlang'];
    $newNin->pfirstname = $decodedJson['entity']['pfirstname'];
    $newNin->photo = $decodedJson['entity']['photo'];
    $newNin->middlename = $decodedJson['entity']['middlename'] || '';
    $newNin->pmiddlename = $decodedJson['entity']['pmiddlename'];
    $newNin->profession = $decodedJson['entity']['profession'];
    $newNin->psurname = $decodedJson['entity']['psurname'];
    $newNin->religion = $decodedJson['entity']['religion'];
    // $newNin->residence_address = $decodedJson['entity']['residence_address'];
    $newNin->residence_town = $decodedJson['entity']['residence_Town'];
    $newNin->residence_lga = $decodedJson['entity']['residence_lga'];
    $newNin->residence_state = $decodedJson['entity']['residence_state'];
    $newNin->residencestatus = $decodedJson['entity']['residencestatus'];
    $newNin->self_origin_lga = $decodedJson['entity']['self_origin_lga'];
    $newNin->self_origin_place = $decodedJson['entity']['self_origin_place'];
    $newNin->self_origin_state = $decodedJson['entity']['self_origin_state'];
    $newNin->signature = $decodedJson['entity']['signature'];
    $newNin->telephoneno = $decodedJson['entity']['telephoneno'];
    $newNin->trackingId = $decodedJson['entity']['trackingId'];
    $newNin->maidenname = $decodedJson['entity']['maidenname'];
    $newNin->state = $decodedJson['entity']['state'];
    $newNin->place = $decodedJson['entity']['place'];
    $newNin->centralID = $decodedJson['entity']['centralID'];
    $newNin->documentno = $decodedJson['entity']['documentno'];
    $newNin->othername = $decodedJson['entity']['othername'];
    $newNin->nspokenlang = $decodedJson['entity']['nspokenlang'];
    $newNin->residence_AddressLine1 = $decodedJson['entity']['residence_AddressLine1'];
    $newNin->residence_AddressLine2 = $decodedJson['entity']['residence_AddressLine2'];
    $newNin->nationality = $decodedJson['entity']['nationality'];
    $newNin->provider = "Dojah";

    $newNin->save();

    return $newNin;
}

function savePassport($decodedJson, $request)
{
    $newPassport = new NationalPassport;
    $newPassport->first_name = $decodedJson['data']['first_name'];
    $newPassport->middle_name = $decodedJson['data']['middle_name'];
    $newPassport->last_name = $decodedJson['data']['last_name'];
    $newPassport->mobile = $decodedJson['data']['mobile'];
    $newPassport->photo = $decodedJson['data']['photo'];
    $newPassport->gender = $decodedJson['data']['gender'];
    $newPassport->dob = $decodedJson['data']['dob'];
    $newPassport->issued_at = $decodedJson['data']['issued_at'];
    $newPassport->issued_date = $decodedJson['data']['issued_date'];
    $newPassport->expiry_date = $decodedJson['data']['expiry_date'];
    $newPassport->reference_id = $decodedJson['data']['reference_id'];
    $newPassport->date_created = $decodedJson['data']['date_created'] || '';
    $newPassport->request_number = $request->number;
    $newPassport->save();
}


function createSms($baseUrl, $key, $from, $body, $to)
{
    $url = "{$baseUrl}/api/v2/sms/create";
    // Log::info($url);

    return Http::withHeaders([
        'Accept' => 'application/json',
    ])->get($url, [
        'api_token'=> $key,
        'to'=> $to,
        'from'=> $from,
        'body'=> $body,
        'gateway'=> '0',
        'append_sender'=> '0',
        'dnd' =>  'international'
    ]);
}



function getPrices($baseUrl, $currency, $page, $perPage)
{
    $url = "{$baseUrl}/api/v3/coins/markets?vs_currency={$currency}&page={$page}&per_page={$perPage}";

    return Http::withHeaders([
        // 'Authorization' => $header[0]
    ])->get($url);
}

function httpGetDojah($url)
{
    return Http::withHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => env('AUTHKEY'),
        'AppId' => env('APPID')
    ])->get($url);
}

function dojahBvn($request, $baseUrl)
{
    // $headers = [
    //     'Content-Type' => 'application/json',
    //     'Authorization' => env('AUTHKEY'),
    //     'AppId' => env('APPID')
    // ];
    $url = "{$baseUrl}/api/v1/kyc/bvn/full?bvn={$request->number}";
    return httpGetDojah($url);
}

function dojahNumber($request, $baseUrl)
{
    $url = "{$baseUrl}/api/v1/kyc/phone_number?phone_number={$request->number}";
    return httpGetDojah($url);

}

function dojahDriverLicense($baseUrl, $number, $dob)
{
    $url = "{$baseUrl}/api/v1/kyc/dl?license_number={$number}&dob={$dob}";
    return httpGetDojah($url);

}

function dojahVotersCard($baseUrl, $number, $state,$last_name)
{
    $url = "{$baseUrl}/api/v1/kyc/vin?mode=vin&vin={$number}&state={$state}&lastname={$last_name}";
    return httpGetDojah($url);

}

function dojahNin($baseUrl, $number)
{
    $url = "{$baseUrl}/api/v1/kyc/nin?nin={$number}";
    return httpGetDojah($url);

}

function httpPostRequest($url, $body)
{
    // Log::info($auth);
    $data = Http::withHeaders([
        'Content-Type' => 'application/json',
        'Authorization' => env('AUTHKEY'),
        'AppId' => env('APPID')
    ])->post($url, $body);

    return $data;
}

function bvnSelfieDojah($bvn, $selfie_image, $baseUrl)
{
    $body = [
        'bvn' => $bvn,
        'selfie_image' => $selfie_image
    ];
    $url = "{$baseUrl}/api/v1/kyc/bvn/verify";

    return httpPostRequest($url, $body);
}

function NinSelfieDojah($nin, $selfie_image, $baseUrl)
{
    $body = [
        'nin' => $nin,
        'selfie_image' => $selfie_image
    ];
    $url = "{$baseUrl}/api/v1/kyc/nin/verify";

    return httpPostRequest($url, $body);
}

