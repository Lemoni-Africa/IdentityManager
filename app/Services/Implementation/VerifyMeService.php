<?php

namespace App\Services\Implementation;

use App\Contract\Responses\DefaultApiResponse;
use App\Http\Requests\BvnRequest;
use App\Http\Requests\CardsRequest;
use App\Http\Requests\SelfieRequest;
use App\Http\Resources\BvnResource;
use App\Http\Resources\DriversLicenseResource;
use App\Http\Resources\NINResource;
use App\Http\Resources\VotersCardResource;
use App\Models\Bvn;
use App\Models\DriversLicense;
use App\Models\Nin;
use App\Models\VotersCard;
use App\Services\Interfaces\IVerifyMeService;
use Illuminate\Support\Facades\Log;

class VerifyMeService implements IVerifyMeService
{
    public DefaultApiResponse $response;
    private $baseUrl;
    private $environment;
    public function __construct()
    {
        $this->response = new DefaultApiResponse();
        $this->baseUrl = env('VERIFY_ME_URL');
        $this->environment = env('VERIFICATION_ENV');
    }


    public function validateBvn(BvnRequest $request): DefaultApiResponse
    {
        Log::info('********** BVN Verification from VerifyMe Service *************');
        Log::info($request->all());

        $checker = $this->checkIfBvnExists($request->number);
        if(!empty($checker)){
            // compare text
            $isLastNameMatching = compareText($request->lastName, $checker['lastName']);
            $isFirstNameMatching = compareText($request->lastName, $checker['firstName']);
            if (!($isLastNameMatching || $isFirstNameMatching)) {
                $this->response->responseCode = '1';
                $this->response->message = "Name doesn't Match";
                $this->response->isSuccessful = false;
                Log::info('response gotten ' .json_encode($this->response));
                return $this->response;
            }
            //check dob
            if ($request->dob != $checker['dateOfBirth']) {
                $this->response->responseCode = '1';
                $this->response->message = "Invalid Date of Birth";
                $this->response->isSuccessful = false;
                Log::info('response gotten ' .json_encode($this->response));
                return $this->response;
            }
            $this->response->responseCode = '0';
            $this->response->message = "Verification Successful";
            $this->response->isSuccessful = true;
            $this->response->data = new BvnResource($checker);
            Log::info('response gotten ' .json_encode($this->response));
            return $this->response;
        }

        $number = $request->number;
        $lastName = $request->lastName;
        $dob = $request->dob;
        $data = verifyMeBvn($request, $this->baseUrl);
        Log::info('data gotten  from verifyMe' .$data);
        // return;
        if ($data['validationResponse']['responseCode'] == "00") {
            $newBvn = saveBvn3($data, $request);
            $isLastNameMatching = compareText($lastName, $data['validationResponse']['lastName']);
            $isFirstNameMatching = compareText($lastName, $data['validationResponse']['firstName']);
            if (!($isLastNameMatching || $isFirstNameMatching)) {
                $this->response->responseCode = '1';
                $this->response->message = "Name doesn't Match";
                $this->response->isSuccessful = false;
                Log::info('response gotten ' .json_encode($this->response));
                return $this->response;
            }
            //check dob
            if ($request->dob !== $newBvn['dateOfBirth']) {
                $this->response->responseCode = '1';
                $this->response->message = "Invalid Date of Birth";
                $this->response->isSuccessful = false;
                Log::info('response gotten ' .json_encode($this->response));
                return $this->response;
            }
            $this->response->responseCode = '0';
            $this->response->message = "Verification Successful";
            $this->response->isSuccessful = true;
            $this->response->data = new BvnResource($newBvn);
            Log::info('response gotten ' .json_encode($this->response));
            return $this->response;
        }
        $this->response->responseCode = '1';
        $this->response->message = "Bank Verification failed";
        $this->response->isSuccessful = false;
        $this->response->data = "BVN not Found";
        Log::info('response gotten ' .json_encode($this->response));
        return $this->response;
    }

    public function BvnSelfie(SelfieRequest $request): DefaultApiResponse
    {
        Log::info($request->all());
        $bvn = $request->number;
        $selfie_image = $request->selfie_image;
        $decodedJson = bvnSelfieVerifyMe($bvn, $selfie_image, $this->baseUrl);
        Log::info('data gotten from verifyMe ' . $decodedJson);
        Log::info("*****************BVN VERIFYME SELFIE******************************");
        Log::info("Request Image => " . $selfie_image);
        Log::info("Response Image => " . $decodedJson['biometricValidationResponse']['data']['photo']);


        if ($decodedJson->successful()) {
            if ($decodedJson['acceptableMatch']) {
                $this->response->responseCode = '0';
                $this->response->message = "Verification Successful";
                $this->response->isSuccessful = true;
                $this->response->data = [
                    'message' => $decodedJson['responseMessage'],
                    'match' => true
                ];
                Log::info('response gotten ' .json_encode($this->response));
                return $this->response;
            }
            $this->response->responseCode = '1';
            $this->response->message = "Verification Failed";
            $this->response->isSuccessful = false;
            $this->response->data = $decodedJson['responseMessage'];
            Log::info('response gotten ' .json_encode($this->response));
            return $this->response;
        }
        $this->response->responseCode = '1';
        $this->response->message = "Verification Failed";
        $this->response->isSuccessful = false;
        $this->response->data = $decodedJson['entity']['selfie_verification'] ?? 'Failed';
        Log::info('response gotten ' .json_encode($this->response));
        return $this->response;
    }

    public function Nin(CardsRequest $request): DefaultApiResponse
    {
        Log::info('********** NIN Verification from VerifyMe Service *************');
        Log::info($request->all());
        if ($this->environment === "TEST") {
            $request->number = "10000000001";
            $request->last_name = "John";
            $request->dob = "1944-04-04";
            $request->first_name = "test";
        }
        $checker = $this->checkIfNinExists($request->number);
        if(!empty($checker)){
            $isLastNameMatching = compareText($request->last_name, $checker['surname']);
            $isFirstNameMatching = compareText($request->last_name, $checker['firstname']);
            if (!($isLastNameMatching || $isFirstNameMatching)) {
                $this->response->responseCode = '1';
                $this->response->message = "Name doesn't Match";
                $this->response->isSuccessful = false;
                Log::info('response gotten ' .json_encode($this->response));
                return $this->response;
            }
            $this->response->responseCode = '0';
            $this->response->message = "Verification Successful";
            $this->response->isSuccessful = true;
            $this->response->data = new NINResource($checker);
            Log::info('response gotten ' .json_encode($this->response));
            return $this->response;
        }

        $data = ninVerifyMe($request, $this->baseUrl);
        // Log::info($decodedJson);
        Log::info('data gotten  from verifyMe ' . $data);
        if ($data->successful()) {
            $newNIN = saveNinVerifyMe($data, $request);
            
            $isLastNameMatching = compareText($request->last_name, $data['ninValidationResponse']['data']['lastname']);
            $isFirstNameMatching = compareText($request->last_name, $data['ninValidationResponse']['data']['firstname']);
            if (!($isLastNameMatching || $isFirstNameMatching)) {
                $this->response->responseCode = '1';
                $this->response->message = "Name doesn't Match";
                $this->response->isSuccessful = false;
                Log::info('response gotten ' .json_encode($this->response));
                return $this->response;
            }
            $this->response->responseCode = '0';
            $this->response->message = $data['responseMessage'];
            $this->response->isSuccessful = true;
            $this->response->data = new NINResource($newNIN);
            Log::info('response gotten ' .json_encode($this->response));
            return $this->response;
        }
        $this->response->responseCode = '1';
        $this->response->message = "Verification Failed";
        $this->response->isSuccessful = false;
        $this->response->data = 'Failed';
        Log::info('response gotten ' .json_encode($this->response));
        return $this->response;
    }


    public function votersCard(CardsRequest $request): DefaultApiResponse
    {
        Log::info('********** Voters Card Verification from verifyMe Service *************');
        Log::info($request->all());
        if ($this->environment === "TEST") {
            $request->number = "987f545AJ67890";
            $request->last_name = "test";
            $request->state = "Lagos";
            $request->first_name = "test";
            $request->dob = "1944-04-04";
        }
        $checker = $this->checkIfVotersCardExists($request->number);
        if(!empty($checker)){ 
            $isLastNameMatching = compareText($request->last_name, $checker['last_name']);
            $isFirstNameMatching = compareText($request->last_name, $checker['first_name']);
            if (!($isLastNameMatching || $isFirstNameMatching)) {
                $this->response->responseCode = '1';
                $this->response->message = "Name doesn't Match";
                $this->response->isSuccessful = false;
                Log::info('response gotten ' .json_encode($this->response));
                return $this->response;
            }
            $this->response->responseCode = '0';
            $this->response->message = "VIN Verification Successful";
            $this->response->isSuccessful = true;
            $this->response->data = new VotersCardResource($checker);
            Log::info('response gotten ' .json_encode($this->response));
            return $this->response;
        }

        $decodedJson = votersCardVerifyMe($request, $this->baseUrl);

        $number = $request->number;
        $last_name = $request->last_name;
        $state = $request->state;
        if ($decodedJson->successful()) {
            $newVotersCard = saveVotersCardVerifyMe($decodedJson);
            $isLastNameMatching = compareText($request->last_name, $decodedJson['votersCardValidationResponse']['data']['lastName']);
            $isFirstNameMatching = compareText($request->last_name, $decodedJson['votersCardValidationResponse']['data']['firstName']);
            if (!($isLastNameMatching || $isFirstNameMatching)) {
                $this->response->responseCode = '1';
                $this->response->message = "Name doesn't Match";
                $this->response->isSuccessful = false;
                Log::info('response gotten ' .json_encode($this->response));
                return $this->response;
            }
            $this->response->responseCode = '0';
            $this->response->message = $decodedJson['responseMessage'];
            $this->response->isSuccessful = true;
            $this->response->data = new VotersCardResource($newVotersCard);
            Log::info('response gotten ' .json_encode($this->response));
            return $this->response;
        }
        $this->response->responseCode = '1';
        $this->response->message = 'Verification Failed';
        $this->response->isSuccessful = false;
        Log::info('response gotten ' .json_encode($this->response));
        return $this->response;
    }

    public function driversLicense(CardsRequest $request): DefaultApiResponse
    {
        Log::info('********** Drivers License Verification from verifyMe Service *************');
        Log::info($request->all());
        if ($this->environment === "TEST") {
            $request->number = "10000000001";
            $request->last_name = "Doe";
            $request->dob = "1992-03-08";
            $request->first_name = "John";
        }
        $checker = $this->checkIfLicenseExists($request->number);
        if(!empty($checker)){
            //check expiry date
            $isExpired = checkExpiryDate($checker->expiryDate);
            if ($isExpired) {
                $this->response->responseCode = '1';
                $this->response->message = "DL Verification Failed";
                $this->response->isSuccessful = false;
                $this->response->data = "License Expired at " . $checker->expiryDate;
                Log::info('response gotten ' .json_encode($this->response));
                return $this->response;
            }

            $isLastNameMatching = compareText($request->last_name, $checker['lastName']);
            $isFirstNameMatching = compareText($request->last_name, $checker['firstName']);
            if (!($isLastNameMatching || $isFirstNameMatching)) {
                $this->response->responseCode = '1';
                $this->response->message = "Name doesn't Match";
                $this->response->isSuccessful = false;
                Log::info('response gotten ' .json_encode($this->response));
                return $this->response;
            }
            $this->response->responseCode = '0';
            $this->response->message = "DL Verification Successful";
            $this->response->isSuccessful = true;
            $this->response->data = new DriversLicenseResource($checker);
            Log::info('response gotten ' .json_encode($this->response));
            return $this->response;
        }

        $number = $request->number;
        $dob = $request->dob;
        $lastName = $request->lastName;

        
        $decodedJson = driversLicenseVerifyMe($request, $this->baseUrl);
        Log::info('response from verifyMe ' .$decodedJson);
    if ($decodedJson->successful()) {
        // $newDriversLicense = saveDriversLicence($decodedJson);
        $newDriversLicense = saveDriversLicenceVerifyMe($decodedJson);
        $isExpired = checkExpiryDate($newDriversLicense->expiryDate);
        if ($isExpired) {
            $this->response->responseCode = '1';
            $this->response->message = "DL Verification Failed";
            $this->response->isSuccessful = false;
            $this->response->data = "License Expired at " . $newDriversLicense->expiryDate;
            Log::info('response gotten ' .json_encode($this->response));
            return $this->response;
        }
        // Log::info('sdsdsdsd ' . $newDriversLicense->lastName);
        $isLastNameMatching = compareText($request->last_name, $newDriversLicense->lastName);
        $isFirstNameMatching = compareText($request->last_name, $newDriversLicense->firstName);
        if (!($isLastNameMatching || $isFirstNameMatching)) {
            $this->response->responseCode = '1';
            $this->response->message = "Name doesn't Match";
            $this->response->isSuccessful = false;
            Log::info('response gotten ' .json_encode($this->response));
            return $this->response;
        }
        $this->response->responseCode = '0';
        $this->response->message = "DL Verification Successful";
        $this->response->isSuccessful = true;
        $this->response->data = new DriversLicenseResource($newDriversLicense);
        Log::info('response gotten ' .json_encode($this->response));
        return $this->response;

        }
        $this->response->responseCode = '1';
        $this->response->message = "DL Verification Failed";
        $this->response->isSuccessful = false;
        Log::info('response gotten ' .json_encode($this->response));
        return $this->response;

    }

    public static function checkIfBvnExists($bvn)
    {
        return Bvn::where('bvn', $bvn)->first();
    }

    public static function checkIfNinExists($nin)
    {
        return Nin::where('nin', $nin)->first();
    }

    public static function checkIfVotersCardExists($card)
    {
        return VotersCard::where('vin', $card)->first();
    }

    public static function checkIfLicenseExists($license)
    {
        return DriversLicense::orderBy('created_at', 'DESC')->where('licenseNo', $license)->first();
    }
}