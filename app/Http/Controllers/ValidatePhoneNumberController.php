<?php

namespace App\Http\Controllers;

use App\Http\Requests\PhoneNumberLastNameRequest;
use App\Http\Requests\PhoneNumberRequest;
use App\Models\PhoneNumber;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ValidatePhoneNumberController extends Controller
{
    private $baseUrl2;
    public function __construct()
    {
        $this->baseUrl = env('BASE_URL');
        $this->baseUrl2 = env('BASE_URL2');
    }
    public function store(PhoneNumberRequest $request)
    {
        try {
            $checker = $this->checkIfPhoneExists($request->number);
        if(!empty($checker)){
            return  response([
                'isSuccesful' => true,
                'message' => "Verification Successful",
                'data' => $checker
            
            ], 200);
        }
        $headers = [
            'Content-Type' => 'application/json',
            'x-api-key' => env('API_KEY'),
        ];
        $client = new Client([
            'headers' => $headers
        ]);
        $baseUrl = env('BASE_URL');
        $url = "{$baseUrl}/api/v1/biometrics/merchant/data/verification/phone_number/advance";

        $body = $request->number;

        $response = $client->request('POST', $url, [
            'form_params' => [
                'number' => $body
            ]
        ]);
        $decodedJson = json_decode($response->getBody(), TRUE);
        if ($decodedJson['response_code'] === "00") {

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
            $newPhoneNumber->provider = "identityPass";

            $newPhoneNumber->save();

  

            return response([
                'isSuccesful' => true,
                'message' => $decodedJson['detail'],
                'data' => $decodedJson['data']
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
                'error' => $e
            
            ], 500);
        }
        
      
    }


    public function number(PhoneNumberLastNameRequest $request)
    {
        try {
            $checker = $this->checkIfPhoneExists($request->number);
        if(!empty($checker)){
            $isLastNameMatching = compareText($request->lastName, $checker['surname']);
            $isFirstNameMatching = compareText($request->lastName, $checker['firstname']);
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
                'data' => $checker
            
            ], 200);
        }
        $headers = [
            'Content-Type' => 'application/json',
            'x-api-key' => env('API_KEY'),
        ];
        $client = new Client([
            'headers' => $headers
        ]);
        $baseUrl = env('BASE_URL');
        $url = "{$baseUrl}/api/v1/biometrics/merchant/data/verification/phone_number/advance";

        $number = $request->number;
        $lastName = $request->lastName;

        $response = $client->request('POST', $url, [
            'form_params' => [
                'number' => $number,
                'lastName' => $lastName
            ]
        ]);
        $decodedJson = json_decode($response->getBody(), TRUE);
        if ($decodedJson['response_code'] === "00") {
            savePhoneNumber($decodedJson);
            $isLastNameMatching = compareText($lastName, $decodedJson['data']['surname']);
            $isFirstNameMatching = compareText($lastName, $decodedJson['data']['firstname']);
            if (!($isLastNameMatching || $isFirstNameMatching)) {
                return response([
                    'isSuccesful' => true,
                    'message' => "Name doesn't Match",
                ]);
            }
            return response([
                'isSuccesful' => true,
                'message' => $decodedJson['detail'],
                'data' => $decodedJson['data']
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
                'error' => $e
            
            ], 500);
        }
        
    }


    public function DojaStore(PhoneNumberRequest $request)
    {
        try {
            $checker = $this->checkIfPhoneExists($request->number);
            if(!empty($checker)){
                return  response([
                    'isSuccesful' => true,
                    'message' => "Verification Successful",
                    'data' => $checker
                
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
            $url = "{$this->baseUrl2}/api/v1/kyc/phone_number?phone_number={$request->number}";
            // $number = $request->number;
            $response = $client->request('GET', $url, [
               
            ]);

            $statusCode = $response->getStatusCode();
            $decodedJson = json_decode($response->getBody(), TRUE);
            
            if ($statusCode === 200) {
                $newPhoneNumber = savePhoneNumber2($decodedJson);
                return response([
                    'isSuccesful' => true,
                    'message' => "Verification Successful",
                    'data' => $newPhoneNumber
                ]);
            }
            return response([
                'isSuccesful' => true,
                'message' => "Verification failed",
                'data' => "Record not Found"
            
            ], 200);
        } catch (\Exception $e) {
            return response([
                'isSuccesful' => false,
                'message' => 'Processing Failed, Contact Support',
                'error' => $e->getMessage()
            
            ], 500);
        }
    }


    public function DojaNumber(PhoneNumberLastNameRequest $request)
    {
        try {
            $checker = $this->checkIfPhoneExists($request->number);
            if(!empty($checker)){
                $isLastNameMatching = compareText($request->lastName, $checker['surname']);
                $isFirstNameMatching = compareText($request->lastName, $checker['firstname']);
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
                    'data' => $checker
                
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
            $url = "{$this->baseUrl2}/api/v1/kyc/phone_number?phone_number={$request->number}";
            $number = $request->number;
            $lastName = $request->lastName;

            $response = $client->request('GET', $url, [
               
            ]);

            $statusCode = $response->getStatusCode();
            $decodedJson = json_decode($response->getBody(), TRUE);

            
            if ($statusCode === 200) {
                $newPhoneNumber = savePhoneNumber2($decodedJson);
                $isLastNameMatching = compareText($lastName, $decodedJson['entity']['lastName']);
                $isFirstNameMatching = compareText($lastName, $decodedJson['entity']['firstName']);
                if (!($isLastNameMatching || $isFirstNameMatching)) {
                    return response([
                        'isSuccesful' => true,
                        'message' => "Name doesn't Match",
                    ]);
                }
                // return response([
                //     'isSuccesful' => true,
                //     'message' => $decodedJson['detail'],
                //     'data' => $decodedJson['data']
                // ],200);
                return response([
                    'isSuccesful' => true,
                    'message' => "Verification Successful",
                    'data' => $newPhoneNumber
                ]);
            }
            return response([
                'isSuccesful' => true,
                'message' => "Verification failed",
                'data' => "Record not Found"
            
            ], 200);
        } catch (\Exception $e) {
            return response([
                'isSuccesful' => false,
                'message' => 'Processing Failed, Contact Support',
                'error' => $e->getMessage()
            
            ], 500);
        }
    }




    public static function checkIfPhoneExists($phoneNumber)
    {
        return PhoneNumber::where('telephoneno', $phoneNumber)->first();
    }
}
