<?php

namespace App\Http\Controllers;

use App\Http\Requests\NationalPassportRequest;
use App\Models\NationalPassport;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ValidateNationalPassportController extends Controller
{
    private $baseUrl;
    public function __construct()
    {
        $this->baseUrl = env('BASE_URL');
    }
    public function store(NationalPassportRequest $request)
    {
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
                }
                $response['responseCode'] = '0';
                $response['message'] = "DL Verification Successful";
                $response['isSuccesful'] = true;
                $response['data'] = $checker;
                Log::info('response gotten ' .json_encode($response));
                return response()->json($response, 200);
                // return  response([
                //     'isSuccesful' => true,
                //     'message' => "DL Verification Successful",
                //     'data' => $checker
                
                // ], 200);
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
            // Log::info($decodedJson);
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


                $isExpired = checkExpiryDate($newPassport->expiry_date);
                if ($isExpired) {
                    $newPassport->save();
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
                }
                
                $newPassport->save();
                $response['responseCode'] = '1';
                $response['message'] = $decodedJson['detail'];
                $response['isSuccesful'] = true;
                $response['data'] = $decodedJson['data'];
                Log::info('response gotten ' .json_encode($response));
                return response()->json($response, 200);
                // return response([
                //     'isSuccesful' => true,
                //     'message' => $decodedJson['detail'],
                //     'data' => $decodedJson['data']
                // ],200);

            }
            $response['responseCode'] = '0';
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
    }

    public static function checkIfNationalPassportsExists($passport)
    {
        return NationalPassport::orderBy('created_at', 'DESC')->where('request_number', $passport)->first();
    }
}
