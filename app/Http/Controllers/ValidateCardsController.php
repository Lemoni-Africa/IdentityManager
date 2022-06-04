<?php

namespace App\Http\Controllers;

use App\Http\Requests\CardsRequest;
use App\Http\Resources\DriversLicenseResource;
use App\Http\Resources\NationalPassportResource;
use App\Http\Resources\NINResource;
use App\Http\Resources\VotersCardResource;
use App\Models\DriversLicense;
use App\Models\NationalPassport;
use App\Models\Nin;
use App\Models\VotersCard;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ValidateCardsController extends Controller
{
    // 'https://api.dojah.io/api/v1/kyc/dl?license_number=wwww&dob=1992-02-12'
    private $baseUrl;
    private $baseUrl2;
    public function __construct()
    {
        $this->baseUrl = env('BASE_URL');
        $this->baseUrl2 = env('BASE_URL2');
    }
        /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CardsRequest $request)
    {
        switch ($request->type) {
            case "DRIVLICE":
                try {
                    $response = [
                        'isSuccesful' =>  false,
                        'responseCode' => null,
                        'data'=> null,
                        'message' => null,
                    ];
                    Log::info('********** Drivers License Verification from IdentityPass Service *************');
                    Log::info($request->all());
                    $checker = $this->checkIfLicenseExists($request->number);
                    if(!empty($checker)){
                        //check expiry date 
                        $isExpired = checkExpiryDate($checker->expiryDate);
                        if ($isExpired) {
                            $response['responseCode'] = '1';
                            $response['message'] = "DL Verification Successful";
                            $response['isSuccesful'] = false;
                            $response['data'] = "License Expired at " . $checker->expiryDate;
                            Log::info('response gotten ' .json_encode($response));
                            return response()->json($response, 400);
                            // return  response([
                            //     'isSuccesful' => true,
                            //     'message' => "DL Verification Successful",
                            //     'data' => "License Expired at " . $checker->expiryDate
                            
                            // ], 200);
                        }
                        $isLastNameMatching = compareText($request->lastName, $checker['lastName']);
                        $isFirstNameMatching = compareText($request->lastName, $checker['firstName']);
                        if (!($isLastNameMatching || $isFirstNameMatching)) {
                            $response['responseCode'] = '1';
                            $response['message'] =  "Name doesn't Match";
                            $response['isSuccesful'] = false;
                            // $response['data'] = $decodedJson['bvn_data'];
                            Log::info('response gotten ' .json_encode($response));
                            return response()->json($response, 400);
                            // return response([
                            //     'isSuccesful' => true,
                            //     'message' => "Name doesn't Match",
                            //     // 'data' => $decodedJson['bvn_data']
                            // ]);
                        }
                        $response['responseCode'] = '0';
                        $response['message'] =   "DL Verification Successful";
                        $response['isSuccesful'] = true;
                        $response['data'] = new DriversLicenseResource($checker);
                        Log::info('response gotten ' .json_encode($response));
                        return response()->json($response, 200);
                        // return  response([
                        //     'isSuccesful' => true,
                        //     'message' => "DL Verification Successful",
                        //     'data' => new DriversLicenseResource($checker) 
                        
                        // ], 200);
                    }
                    $headers = [
                        'Content-Type' => 'application/json',
                        'x-api-key' => env('API_KEY'),
                    ];
                    $client = new Client([
                        'headers' => $headers
                    ]);
                    $url = "{$this->baseUrl}/api/v1/biometrics/merchant/data/verification/drivers_license";
            
                    $number = $request->number;
                    $dob = $request->dob;
                    $lastName = $request->lastName;
    
                    $response2 = $client->request('POST', $url, [
                        'form_params' => [
                            'number' => $number,
                            'dob' => $dob,
                            'lastName' => $lastName
                        ]
                    ]);
                    $decodedJson = json_decode($response2->getBody(), TRUE);
                if ($decodedJson['response_code'] === "00") {
                    $newDriversLicense = saveDriversLicence($decodedJson);
                    $isExpired = checkExpiryDate($decodedJson['data']['expiryDate']);
                    if ($isExpired) {
                        $response['responseCode'] = '1';
                        $response['message'] =   "DL Verification Successful";
                        $response['isSuccesful'] = false;
                        $response['data'] = "License Expired at " . $decodedJson['data']['expiryDate'];
                        Log::info('response gotten ' .json_encode($response));
                        return response()->json($response, 400);

                        // return  response([
                        //     'isSuccesful' => true,
                        //     'message' => "DL Verification Successful",
                        //     'data' => "License Expired at " . $decodedJson['data']['expiryDate']
                        
                        // ], 200);
                    }
                    $isLastNameMatching = compareText($request->lastName, $decodedJson['data']['lastName']);
                    $isFirstNameMatching = compareText($request->lastName, $decodedJson['data']['firstName']);
                    if (!($isLastNameMatching || $isFirstNameMatching)) {
                        $response['responseCode'] = '1';
                        $response['message'] =  "Name doesn't Match";
                        $response['isSuccesful'] = false;
                        // $response['data'] = "License Expired at " . $decodedJson['data']['expiryDate'];
                        Log::info('response gotten ' .json_encode($response));
                        return response()->json($response, 400);
                        // return response([
                        //     'isSuccesful' => true,
                        //     'message' => "Name doesn't Match",
                        //     // 'data' => $decodedJson['bvn_data']
                        // ]);
                    }
                    $response['responseCode'] = '0';
                    $response['message'] =  $decodedJson['detail'];
                    $response['isSuccesful'] = true;
                    $response['data'] = new DriversLicenseResource($newDriversLicense) ;
                    Log::info('response gotten ' .json_encode($response));
                    return response()->json($response, 200);
                    // return response([
                    //     'isSuccesful' => true,
                    //     'message' => $decodedJson['detail'],
                    //     'data' => new DriversLicenseResource($newDriversLicense) 
                    // ],200);
                }
                    $response['responseCode'] = '0';
                    $response['message'] =  $decodedJson['detail'];
                    $response['isSuccesful'] = true;
                    $response['data'] = $decodedJson['message'];
                    Log::info('response gotten ' .json_encode($response));
                    return response()->json($response, 400);
                    // return response([
                    //     'isSuccesful' => true,
                    //     'message' => $decodedJson['detail'],
                    //     'data' => $decodedJson['message']
                    
                    // ], 200);
                } catch (\Exception $e) {
                    Log::info(json_encode($e));
                    return response([
                        'isSuccesful' => false,
                        'message' => 'Processing Failed, Contact Support',
                        'error' => $e->getMessage()
                    
                    ], 500);
                }
                
    
            break;
            case "VTRCARD":
                try {
                    $response = [
                        'isSuccesful' =>  false,
                        'responseCode' => null,
                        'data'=> null,
                        'message' => null,
                    ];
                    Log::info('********** Voters Card Verification from IdentityPass Service *************');
                    Log::info($request->all());
                    $checker = $this->checkIfVotersCardExists($request->number);
                    if(!empty($checker)){
                        $isLastNameMatching = compareText($request->last_name, $checker['last_name']);
                        $isFirstNameMatching = compareText($request->last_name, $checker['first_name']);
                        if (!($isLastNameMatching || $isFirstNameMatching)) {
                            $response['responseCode'] = '1';
                            $response['message'] =  "Name doesn't Match";
                            $response['isSuccesful'] = false;
                            // $response['data'] = $decodedJson['bvn_data'];
                            Log::info('response gotten ' .json_encode($response));
                            return response()->json($response, 400);
                            // return response([
                            //     'isSuccesful' => true,
                            //     'message' => "Name doesn't Match",
                            //     // 'data' => $decodedJson['bvn_data']
                            // ]);
                        }
                        $response['responseCode'] = '0';
                        $response['message'] = "VIN Verification Successful";
                        $response['isSuccesful'] = true;
                        $response['data'] = new VotersCardResource($checker);
                        Log::info('response gotten ' .json_encode($response));
                        return response()->json($response, 200);
                        // return  response([
                        //     'isSuccesful' => true,
                        //     'message' => "VIN Verification Successful",
                        //     'data' => new VotersCardResource($checker)
                        
                        // ], 200);
                    }
                    $headers = [
                        'Content-Type' => 'application/json',
                        'x-api-key' => env('API_KEY'),
                    ];
                    $client = new Client([
                        'headers' => $headers
                    ]);
                    $url = "{$this->baseUrl}/api/v1/biometrics/merchant/data/verification/voters_card";
            
                    $number = $request->number;
                    $last_name = $request->last_name;
                    $state = $request->state;
            
                    $response2 = $client->request('POST', $url, [
                        'form_params' => [
                            'number' => $number,
                            'last_name' => $last_name,
                            'state' => $state
                        ]
                    ]);
            
                    $decodedJson = json_decode($response2->getBody(), TRUE);
                    if ($decodedJson['response_code'] === "00") {
                        $newVotersCard = saveVotersCard($decodedJson);
                        $isLastNameMatching = compareText($request->last_name, $checker['last_name']);
                        $isFirstNameMatching = compareText($request->last_name, $checker['first_name']);
                        if (!($isLastNameMatching || $isFirstNameMatching)) {
                            $response['responseCode'] = '1';
                            $response['message'] =  "Name doesn't Match";
                            $response['isSuccesful'] = false;
                            // $response['data'] = $decodedJson['bvn_data'];
                            Log::info('response gotten ' .json_encode($response));
                            return response()->json($response, 400);
                            // return response([
                            //     'isSuccesful' => true,
                            //     'message' => "Name doesn't Match",
                            //     // 'data' => $decodedJson['bvn_data']
                            // ]);
                        }
                        $response['responseCode'] = '0';
                        $response['message'] = $decodedJson['detail'];
                        $response['isSuccesful'] = true;
                        $response['data'] = new VotersCardResource($newVotersCard);
                        Log::info('response gotten ' .json_encode($response));
                        return response()->json($response, 200);
                        // return response([
                        //     'isSuccesful' => true,
                        //     'message' => $decodedJson['detail'],
                        //     'data' => new VotersCardResource($newVotersCard)
                        // ],200);
                    }
                    $response['responseCode'] = '0';
                    $response['message'] =  $decodedJson['detail'];
                    $response['isSuccesful'] = true;
                    $response['data'] = $decodedJson['message'];
                    Log::info('response gotten ' .json_encode($response));
                    return response()->json($response, 400);
                    // return response([
                    //     'isSuccesful' => true,
                    //     'message' => $decodedJson['detail'],
                    //     'data' => $decodedJson['message']
                    
                    // ], 200);
                } catch (\Exception $e) {
                    Log::info(json_encode($e));
                    return response([
                        'isSuccesful' => false,
                        'message' => 'Processing Failed, Contact Support',
                        'error' => $e->getMessage()
                    ], 500);
                }


            break;
            case "NIN":
                try {
                    $response = [
                        'isSuccesful' =>  false,
                        'responseCode' => null,
                        'data'=> null,
                        'message' => null,
                    ];
                    Log::info('********** NIN Verification from IdentityPass Service *************');
                    Log::info($request->all());
                    $checker = $this->checkIfNinExists($request->number);
                    if(!empty($checker)){
                        $isLastNameMatching = compareText($request->last_name, $checker['surname']);
                        $isFirstNameMatching = compareText($request->last_name, $checker['firstname']);
                        if (!($isLastNameMatching || $isFirstNameMatching)) {
                            $response['responseCode'] = '1';
                            $response['message'] =  "Name doesn't Match";
                            $response['isSuccesful'] = false;
                            // $response['data'] = $decodedJson['bvn_data'];
                            Log::info('response gotten ' .json_encode($response));
                            return response()->json($response, 400);
                            // return response([
                            //     'isSuccesful' => true,
                            //     'message' => "Name doesn't Match",
                            //     // 'data' => $decodedJson['bvn_data']
                            // ]);
                            // return response([
                            //     'isSuccesful' => true,
                            //     'message' => "Name doesn't Match",
                            //     // 'data' => $decodedJson['bvn_data']
                            // ]);
                        }
                        $response['responseCode'] = '0';
                        $response['message'] = "Verification Successful";
                        $response['isSuccesful'] = true;
                        $response['data'] = new NINResource($checker);
                        Log::info('response gotten ' .json_encode($response));
                        return response()->json($response, 200);
                        // return  response([
                        //     'isSuccesful' => true,
                        //     'message' => "Verification Successful",
                        //     'data' => new NINResource($checker)
                        
                        // ], 200);
                    }
                    $headers = [
                        'Content-Type' => 'application/json',
                        'x-api-key' => env('API_KEY'),
                    ];
                    $client = new Client([
                        'headers' => $headers
                    ]);
                    $url = "{$this->baseUrl}/api/v1/biometrics/merchant/data/verification/nin_wo_face";
            
                    $number = $request->number;
                    $last_name = $request->last_name;
            
                    $response2 = $client->request('POST', $url, [
                        'form_params' => [
                            'number' => $number,
                            'last_name' => $last_name
                        ]
                    ]);
            
                    $decodedJson = json_decode($response2->getBody(), TRUE);
                    // Log::info($decodedJson);
                    Log::info($decodedJson);
                    if ($decodedJson['response_code'] === "00") {
                        $newNIN = saveNin($decodedJson);
                        $isLastNameMatching = compareText($last_name, $decodedJson['nin_data']['surname']);
                        $isFirstNameMatching = compareText($last_name, $decodedJson['nin_data']['firstname']);
                        if (!($isLastNameMatching || $isFirstNameMatching)) {
                            $response['responseCode'] = '1';
                            $response['message'] =  "Name doesn't Match";
                            $response['isSuccesful'] = false;
                            // $response['data'] = $decodedJson['bvn_data'];
                            Log::info('response gotten ' .json_encode($response));
                            return response()->json($response, 400);
                            // return response([
                            //     'isSuccesful' => true,
                            //     'message' => "Name doesn't Match",
                            // ]);
                        }
                        $response['responseCode'] = '0';
                        $response['message'] = $decodedJson['detail'];
                        $response['isSuccesful'] = true;
                        $response['data'] = new NINResource($newNIN);
                        Log::info('response gotten ' .json_encode($response));
                        return response()->json($response, 200);
                        // return response([
                        //     'isSuccesful' => true,
                        //     'message' => $decodedJson['detail'],
                        //     'data' => new NINResource($newNIN)
                        // ],200);
                    }
                    $response['responseCode'] = '1';
                    $response['message'] = $decodedJson['detail'];
                    $response['isSuccesful'] = true;
                    $response['data'] = $decodedJson['message'];
                    Log::info('response gotten ' .json_encode($response));
                    return response()->json($response, 200);
                    // return response([
                    //     'isSuccesful' => true,
                    //     'message' => $decodedJson['detail'],
                    //     'data' => $decodedJson['message']
                    
                    // ], 200);
                } catch (\Exception $e) {
                    Log::info(json_encode($e));
                    return response([
                        'isSuccesful' => false,
                        'message' => 'Processing Failed, Contact Support',
                        'error' => $e->getMessage()
                    ], 500);
                }
            break;
            case "PASSPORT":
                try {
                    $response = [
                        'isSuccesful' =>  false,
                        'responseCode' => null,
                        'data'=> null,
                        'message' => null,
                    ];
                    Log::info('********** National Passport Verification from IdentityPass Service *************');
                    Log::info($request->all());
                    $checker = $this->checkIfNationalPassportsExists($request->number);
                    if(!empty($checker)){
                        //check expiry date 
                         $isExpired = checkExpiryDate($checker->expiry_date);
                        if ($isExpired) {
                            $response['responseCode'] = '1';
                            $response['message'] = "Verification Successful";
                            $response['isSuccesful'] = false;
                            $response['data'] = "License Expired at " . $checker->expiry_date;
                            Log::info('response gotten ' .json_encode($response));
                            return response()->json($response, 400);
                            // return  response([
                            //     'isSuccesful' => true,
                            //     'message' => "Verification Successful",
                            //     'data' => "License Expired at " . $checker->expiry_date
                            
                            // ], 200);
                            // return  response([
                            //     'isSuccesful' => true,
                            //     'message' => "Verification Successful",
                            //     'data' => "License Expired at " . $checker->expiry_date
                            
                            // ], 200);
                        }
                        $isLastNameMatching = compareText($request->last_name, $checker['last_name']);
                        $isFirstNameMatching = compareText($request->last_name, $checker['first_name']);
                        if (!($isLastNameMatching || $isFirstNameMatching)) {
                            $response['responseCode'] = '1';
                            $response['message'] =  "Name doesn't Match";
                            $response['isSuccesful'] = false;
                            // $response['data'] = $decodedJson['bvn_data'];
                            Log::info('response gotten ' .json_encode($response));
                            return response()->json($response, 400);
                            // return response([
                            //     'isSuccesful' => true,
                            //     'message' => "Name doesn't Match",
                            //     // 'data' => $decodedJson['bvn_data']
                            // ]);
                        }
                        $response['responseCode'] = '0';
                        $response['message'] =  "DL Verification Successful";
                        $response['isSuccesful'] = false;
                        $response['data'] = new NationalPassportResource($checker);
                        Log::info('response gotten ' .json_encode($response));
                        return response()->json($response, 200);
                        return  response([
                            'isSuccesful' => true,
                            'message' => "DL Verification Successful",
                            'data' =>  new NationalPassportResource($checker)
                        
                        ], 200);
                    }
                    $headers = [
                        'Content-Type' => 'application/json',
                        'x-api-key' => env('API_KEY'),
                    ];
                    $client = new Client([
                        'headers' => $headers
                    ]);
                    $url = "{$this->baseUrl}/api/v1/biometrics/merchant/data/verification/national_passport";
            
                    $number = $request->number;
                    $dob = $request->dob;
                    $first_name = $request->first_name;
                    $last_name = $request->last_name;
            
                    $response2 = $client->request('POST', $url, [
                        'form_params' => [
                            'number' => $number,
                            'dob' => $dob,
                            'first_name' => $first_name,
                            'last_name' => $last_name
                        ]
                    ]);
                    $decodedJson = json_decode($response2->getBody(), TRUE);
                    if ($decodedJson['response_code'] === "00") {
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
                        $newPassport->provider = "identityPass";
                        $newPassport->save();
                        $isExpired = checkExpiryDate($newPassport->expiry_date);
                        if ($isExpired) {
                            $response['responseCode'] = '1';
                            $response['message'] = "DL Verification Successful";
                            $response['isSuccesful'] = true;
                            $response['data'] = "License Expired at " . $newPassport->expiry_date;
                            Log::info('response gotten ' .json_encode($response));
                            return response()->json($response, 200);
                            // return  response([
                            //     'isSuccesful' => true,
                            //     'message' => "DL Verification Successful",
                            //     'data' => "License Expired at " . $newPassport->expiry_date
                            
                            // ], 200);
                            // return  response([
                            //     'isSuccesful' => true,
                            //     'message' => "DL Verification Successful",
                            //     'data' => "License Expired at " . $newPassport->expiry_date
                            
                            // ], 200);
                        }
                        $response['responseCode'] = '0';
                        $response['message'] = $decodedJson['detail'];
                        $response['isSuccesful'] = true;
                        $response['data'] = new NationalPassportResource($newPassport);
                        Log::info('response gotten ' .json_encode($response));
                        return response()->json($response, 200);
                        // return response([
                        //     'isSuccesful' => true,
                        //     'message' => $decodedJson['detail'],
                        //     'data' => new NationalPassportResource($newPassport)
                        // ],200);
        
                    }
                    $response['responseCode'] = '1';
                    $response['message'] = $decodedJson['detail'];
                    $response['isSuccesful'] = true;
                    $response['data'] = $decodedJson['message'];
                    Log::info('response gotten ' .json_encode($response));
                    return response()->json($response, 200);
                    // return response([
                    //     'isSuccesful' => true,
                    //     'message' => $decodedJson['detail'],
                    //     'data' => $decodedJson['message']
                    
                    // ], 200);
                    // return response([
                    //     'isSuccesful' => true,
                    //     'message' => $decodedJson['detail'],
                    //     'data' => $decodedJson['message']
                    
                    // ], 200);
                } catch (\Exception $e) {
                    Log::info(json_encode($e));
                    return response([
                        'isSuccesful' => false,
                        'message' => 'Processing Failed, Contact Support',
                        'error' => $e->getMessage()
                    
                    ], 500);
                }
            break;
            default:
            //   code to be executed if n is different from all labels;
        }
    }





    public function DojahStore(CardsRequest $request)
    {
        switch ($request->type) {
            case "DRIVLICE":
                try {
                    $checker = $this->checkIfLicenseExists($request->number);
                    if(!empty($checker)){
                        //check expiry date 
                        $isExpired = checkExpiryDate($checker->expiryDate);
                        if ($isExpired) {
                            return  response([
                                'isSuccesful' => true,
                                'message' => "DL Verification Successful",
                                'data' => "License Expired at " . $checker->expiryDate
                            
                            ], 200);
                        }
                        $isLastNameMatching = compareText($request->lastName, $checker['lastName']);
                        $isFirstNameMatching = compareText($request->lastName, $checker['firstName']);
                        if (!($isLastNameMatching || $isFirstNameMatching)) {
                            return response([
                                'isSuccesful' => true,
                                'message' => "Name doesn't Match",
                                // 'data' => $decodedJson['bvn_data']
                            ]);
                        }
                        
                        return  response([
                            'isSuccesful' => true,
                            'message' => "DL Verification Successful",
                            'data' => new DriversLicenseResource($checker) 
                        
                        ], 200);
                    }
                    $headers = [
                        'Content-Type' => 'application/json',
                        'Authorization' => env('AUTHKEY'),
                        'AppId' => env('APPID')
                    ];
                    $client = new Client([
                        'headers' => $headers
                    ]);
                    $number = $request->number;
                    $lastName = $request->lastName;
                    $dob = $request->dob;

                    $url = "{$this->baseUrl2}/api/v1/kyc/dl?license_number={$number}&dob={$dob}";

                    
                    $response = $client->request('GET', $url, [
                       
                    ]);
                    $statusCode = $response->getStatusCode();
                    $decodedJson = json_decode($response->getBody(), TRUE);
                    Log::info($url);
                if ($statusCode === 200) {
                    $newDriversLicense = saveDriversLicence2($decodedJson);
                    $isExpired = checkExpiryDate($decodedJson['entity']['expiryDate']);
                    if ($isExpired) {
                        return  response([
                            'isSuccesful' => true,
                            'message' => "DL Verification Successful",
                            'data' => "License Expired at " . $decodedJson['entity']['expiryDate']
                        
                        ], 200);
                    }
                    $isLastNameMatching = compareText($request->lastName, $decodedJson['entity']['lastName']);
                    $isFirstNameMatching = compareText($request->lastName, $decodedJson['entity']['firstName']);
                    if (!($isLastNameMatching || $isFirstNameMatching)) {
    
                        return response([
                            'isSuccesful' => true,
                            'message' => "Name doesn't Match",
                            // 'data' => $decodedJson['bvn_data']
                        ]);
                    }
                    
                    return response([
                        'isSuccesful' => true,
                        'message' => "DL Verification Successful",
                        'data' => new DriversLicenseResource($newDriversLicense) 
                    ],200);
                }
                    return response([
                        'isSuccesful' => true,
                        "message" => "DL Verification failed",
                        "data" => "Record not found"
                    
                    ], 200);
                } catch (\Exception $e) {
                    return response([
                        'isSuccesful' => false,
                        'message' => 'Processing Failed, Contact Support',
                        'error' => $e->getMessage()
                    
                    ], 500);
                }
                
    
            break;
            case "VTRCARD":
                try {
                    $checker = $this->checkIfVotersCardExists($request->number);
                    if(!empty($checker)){
                        // $isLastNameMatching = compareText($request->last_name, $checker['last_name']);
                        // $isFirstNameMatching = compareText($request->last_name, $checker['first_name']);
                        // if (!($isLastNameMatching || $isFirstNameMatching)) {
                        //     return response([
                        //         'isSuccesful' => true,
                        //         'message' => "Name doesn't Match",
                        //         // 'data' => $decodedJson['bvn_data']
                        //     ]);
                        // }
                        return  response([
                            'isSuccesful' => true,
                            'message' => "VIN Verification Successful",
                            'data' => new VotersCardResource($checker)
                        
                        ], 200);
                    }
                    $headers = [
                        'Content-Type' => 'application/json',
                        'Authorization' => env('AUTHKEY'),
                        'AppId' => env('APPID')
                    ];
                    $client = new Client([
                        'headers' => $headers
                    ]);
                    $number = $request->number;
                    $last_name = $request->last_name;
                    $state = $request->state;

                    $url = "{$this->baseUrl2}/api/v1/kyc/vin?mode=vin&vin={$number}&state={$state}&lastname={$last_name}";

                    
                    $response = $client->request('GET', $url, [
                       
                    ]);
                   
                    Log::info($url);

                    $statusCode = $response->getStatusCode();
                    $decodedJson = json_decode($response->getBody(), TRUE);
                    Log::info($response->getBody());
                    if ($statusCode === 200) {
                        $newVotersCard = saveVotersCard2($decodedJson);
                        // $isLastNameMatching = compareText($request->last_name, $checker['last_name']);
                        // $isFirstNameMatching = compareText($request->last_name, $checker['first_name']);
                        // if (!($isLastNameMatching || $isFirstNameMatching)) {
                        //     return response([
                        //         'isSuccesful' => true,
                        //         'message' => "Name doesn't Match",
                        //         // 'data' => $decodedJson['bvn_data']
                        //     ]);
                        // }
                        return response([
                            'isSuccesful' => true,
                            'message' => "VIN Verification Successful",
                            'data' => new VotersCardResource($newVotersCard)
                        ],200);
                    }
            
                    return response([
                        'isSuccesful' => true,
                        "message" => "VIN Verification failed",
                        "data" => "Record not found"
                    
                    ], 200);
                } catch (\Exception $e) {
                    return response([
                        'isSuccesful' => false,
                        'message' => 'Processing Failed, Contact Support',
                        'error' => $e->getMessage()
                    ], 500);
                }


            break;
            case "NIN":
                try {
                    $checker = $this->checkIfNinExists($request->number);
                    if(!empty($checker)){
                        $isLastNameMatching = compareText($request->last_name, $checker['surname']);
                        $isFirstNameMatching = compareText($request->last_name, $checker['firstname']);
                        if (!($isLastNameMatching || $isFirstNameMatching)) {

                            return response([
                                'isSuccesful' => true,
                                'message' => "Name doesn't Match",
                                // 'data' => $decodedJson['bvn_data']
                            ]);
                        }
                        return  response([
                            'isSuccesful' => true,
                            'message' => "Verification Successful",
                            'data' => new NINResource($checker)
                        
                        ], 200);
                    }
                    $headers = [
                        'Content-Type' => 'application/json',
                        'Authorization' => env('AUTHKEY'),
                        'AppId' => env('APPID')
                    ];
                    $client = new Client([
                        'headers' => $headers
                    ]);
                    $number = $request->number;
                    $last_name = $request->last_name;
                    $state = $request->state;

                    $url = "{$this->baseUrl2}/api/v1/kyc/nin?nin={$number}";

                    
                    $response = $client->request('GET', $url, [
                       
                    ]);
                   
                    Log::info($url);

                    $statusCode = $response->getStatusCode();
                    $decodedJson = json_decode($response->getBody(), TRUE);
                    Log::info($response->getBody());
                    // Log::info($decodedJson);
            
                    if ($statusCode === 200) {
                        $newNIN = saveNin2($decodedJson);
                        Log::info($newNIN);
                        $isLastNameMatching = compareText($last_name, $newNIN['surname']);
                        $isFirstNameMatching = compareText($last_name, $newNIN['firstname']);
                        if (!($isLastNameMatching || $isFirstNameMatching)) {
                            return response([
                                'isSuccesful' => true,
                                'message' => "Name doesn't Match",
                            ]);
                        }
                        return response([
                            'isSuccesful' => true,
                            'message' =>  "Verification Successful",
                            'data' => new NINResource($newNIN)
                        ],200);
                    }
            
                    return response([
                        'isSuccesful' => true,
                        'message' => "NIN Verification failed",
                        'data' => "Record not found"
                    
                    ], 200);
                } catch (\Exception $e) {
                    return response([
                        'isSuccesful' => false,
                        'message' => 'Processing Failed, Contact Support',
                        'error' => $e->getMessage()
                    ], 500);
                }
            break;
            case "PASSPORT":
                try {
                    $checker = $this->checkIfNationalPassportsExists($request->number);
                    if(!empty($checker)){
                        //check expiry date 
                         $isExpired = checkExpiryDate($checker->expiry_date);
                        if ($isExpired) {
                            return  response([
                                'isSuccesful' => true,
                                'message' => "Verification Successful",
                                'data' => "License Expired at " . $checker->expiry_date
                            
                            ], 200);
                        }
                        $isLastNameMatching = compareText($request->last_name, $checker['last_name']);
                        $isFirstNameMatching = compareText($request->last_name, $checker['first_name']);
                        if (!($isLastNameMatching || $isFirstNameMatching)) {
            
                            return response([
                                'isSuccesful' => true,
                                'message' => "Name doesn't Match",
                                // 'data' => $decodedJson['bvn_data']
                            ]);
                        }
                        return  response([
                            'isSuccesful' => true,
                            'message' => "DL Verification Successful",
                            'data' =>  new NationalPassportResource($checker)
                        
                        ], 200);
                    }
                    $headers = [
                        'Content-Type' => 'application/json',
                        'x-api-key' => env('API_KEY'),
                    ];
                    $client = new Client([
                        'headers' => $headers
                    ]);
                    $url = "{$this->baseUrl}/api/v1/biometrics/merchant/data/verification/national_passport";
            
                    $number = $request->number;
                    $dob = $request->dob;
                    $first_name = $request->first_name;
                    $last_name = $request->last_name;
            
                    $response = $client->request('POST', $url, [
                        'form_params' => [
                            'number' => $number,
                            'dob' => $dob,
                            'first_name' => $first_name,
                            'last_name' => $last_name
                        ]
                    ]);
                    $decodedJson = json_decode($response->getBody(), TRUE);
                    if ($decodedJson['response_code'] === "00") {
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
                        $newPassport->provider = "identityPass";
                        $newPassport->save();
                        $isExpired = checkExpiryDate($newPassport->expiry_date);
                        if ($isExpired) {
                            
                            return  response([
                                'isSuccesful' => true,
                                'message' => "DL Verification Successful",
                                'data' => "License Expired at " . $newPassport->expiry_date
                            
                            ], 200);
                        }
                        
                        return response([
                            'isSuccesful' => true,
                            'message' => $decodedJson['detail'],
                            'data' => new NationalPassportResource($newPassport)
                        ],200);
        
                    }
                    return response([
                        'isSuccesful' => true,
                        'message' => $decodedJson['detail'],
                        'data' => $decodedJson['message']
                    
                    ], 200);
                } catch (\Exception $e) {
                    return response([
                        'isSuccesful' => false,
                        'message' => 'Processing Failed, Contact Support',
                        'error' => $e->getMessage()
                    
                    ], 500);
                }
            break;
            default:
            //   code to be executed if n is different from all labels;
        }
    }

    public static function checkIfLicenseExists($license)
    {
        return DriversLicense::orderBy('created_at', 'DESC')->where('licenseNo', $license)->first();
    }

    public static function checkIfVotersCardExists($card)
    {
        return VotersCard::where('vin', $card)->first();
    }

    public static function checkIfNinExists($nin)
    {
        return Nin::where('nin', $nin)->first();
    }

    public static function checkIfNationalPassportsExists($passport)
    {
        return NationalPassport::orderBy('created_at', 'DESC')->where('request_number', $passport)->first();
    }
}
