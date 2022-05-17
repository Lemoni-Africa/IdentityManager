<?php

namespace App\Http\Controllers;

use App\Http\Requests\BvnRequest;
use App\Http\Resources\BvnResource;
use App\Models\Bvn;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\PseudoTypes\LowercaseString;

class ValidateBvnController extends Controller
{
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
     * @param  \App\Http\Requests\BvnRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BvnRequest $request)
    {
        try {
            $checker = $this->checkIfBvnExists($request->number);
        if(!empty($checker)){
            // compare text
            $isLastNameMatching = compareText($request->lastName, $checker['lastName']);
            $isFirstNameMatching = compareText($request->lastName, $checker['firstName']);
            if (!($isLastNameMatching || $isFirstNameMatching)) {

                return response([
                    'isSuccesful' => true,
                    'message' => "Name doesn't Match",
                    // 'data' => $decodedJson['bvn_data']
                ]);
            }
            //check dob
            if ($request->dob != $checker['dateOfBirth']) {
                return response([
                    'isSuccesful' => true,
                    'message' => "Invalid Date of Birth",
                ]);
            }
            return  response([
                'isSuccesful' => true,
                'message' => "Verification Successful",
                'data' => new BvnResource($checker) 
                
            ], 200);
        }
        $headers = [
            'Content-Type' => 'application/json',
            'x-api-key' => env('API_KEY'),
        ];
        $client = new Client([
            'headers' => $headers
        ]);
        $url = "{$this->baseUrl}/api/v1/biometrics/merchant/data/verification/bvn";

        $number = $request->number;
        $lastName = $request->lastName;
        $dob = $request->dob;

        $response = $client->request('POST', $url, [
            'form_params' => [
                'number' => $number,
                'lastName' => $lastName,
                'dob' => $dob
            ]
        ]);
        $decodedJson = json_decode($response->getBody(), TRUE);
        if ($decodedJson['response_code'] === "00") {
            $newBvn = saveBvn($decodedJson);
            $isLastNameMatching = compareText($lastName, $decodedJson['bvn_data']['lastName']);
            $isFirstNameMatching = compareText($lastName, $decodedJson['bvn_data']['firstName']);
            if (!($isLastNameMatching || $isFirstNameMatching)) {
                return response([
                    'isSuccesful' => true,
                    'message' => "Name doesn't Match",
                ]);
            }
            //check dob
            if ($request->dob !== $decodedJson['bvn_data']['dateOfBirth']) {
                return response([
                    'isSuccesful' => true,
                    'message' => "Invalid Date of Birth",
                ]);
            }
     
            return response([
                'isSuccesful' => true,
                'message' => $decodedJson['detail'],
                'data' => new BvnResource($newBvn) 
            ]);
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
        
      
    }

    
    public function storeDojah(BvnRequest $request)
    {
        try {
            $checker = $this->checkIfBvnExists($request->number);
            if(!empty($checker)){
                // compare text
                $isLastNameMatching = compareText($request->lastName, $checker['lastName']);
                $isFirstNameMatching = compareText($request->lastName, $checker['firstName']);
                if (!($isLastNameMatching || $isFirstNameMatching)) {
    
                    return response([
                        'isSuccesful' => true,
                        'message' => "Name doesn't Match",
                        // 'data' => $decodedJson['bvn_data']
                    ]);
                }
                //check dob
                if ($request->dob != $checker['dateOfBirth']) {
                    return response([
                        'isSuccesful' => true,
                        'message' => "Invalid Date of Birth",
                    ]);
                }
                return  response([
                    'isSuccesful' => true,
                    'message' => "Verification Successful",
                    'data' => new BvnResource($checker) 
                    
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
            $url = "{$this->baseUrl2}/api/v1/kyc/bvn/full?bvn={$request->number}";
            $number = $request->number;
            $lastName = $request->lastName;
            $dob = $request->dob;
            $response = $client->request('GET', $url, [
               
            ]);
            $statusCode = $response->getStatusCode();
            $decodedJson = json_decode($response->getBody(), TRUE);

            if ($statusCode === 200) {
                $newBvn = saveBvn2($decodedJson);
                $isLastNameMatching = compareText($lastName, $decodedJson['entity']['last_name']);
                $isFirstNameMatching = compareText($lastName, $decodedJson['entity']['first_name']);
                if (!($isLastNameMatching || $isFirstNameMatching)) {
                    return response([
                        'isSuccesful' => true,
                        'message' => "Name doesn't Match",
                    ]);
                }
                //check dob
                if ($request->dob !== $newBvn['dateOfBirth']) {
                    return response([
                        'isSuccesful' => true,
                        'message' => "Invalid Date of Birth",
                    ]);
                }
                return response([
                    'isSuccesful' => true,
                    'message' => "Verification Successful",
                    'data' => new BvnResource($newBvn)
                ]);
            }
            return response([
                'isSuccesful' => true,
                'message' => "Bank Verification failed",
                'data' => "BVN not Found"
            
            ], 200);

        } catch (\Exception $e) {
            return response([
                'isSuccesful' => false,
                'message' => 'Processing Failed, Contact Support',
                'error' => $e->getMessage()
            ], 500);
        }
        
    }





    public static function checkIfBvnExists($bvn)
    {
        return Bvn::where('bvn', $bvn)->first();
    }

   
}
