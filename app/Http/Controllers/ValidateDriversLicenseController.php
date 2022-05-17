<?php

namespace App\Http\Controllers;

use App\Http\Requests\DriversLicenseRequest;
use App\Models\DriversLicense;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ValidateDriversLicenseController extends Controller
{
    private $baseUrl;
    public function __construct()
    {
        $this->baseUrl = env('BASE_URL');
    }
    public function store(DriversLicenseRequest $request)
    {
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
                return  response([
                    'isSuccesful' => true,
                    'message' => "DL Verification Successful",
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
            $url = "{$this->baseUrl}/api/v1/biometrics/merchant/data/verification/drivers_license";
    
            $number = $request->number;
            $dob = $request->dob;
           
    
            $response = $client->request('POST', $url, [
                'form_params' => [
                    'number' => $number,
                    'dob' => $dob,
                    
                ]
            ]);
    
            $decodedJson = json_decode($response->getBody(), TRUE);
            if ($decodedJson['response_code'] === "00") {
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

                $isExpired = checkExpiryDate($newLicense->expiryDate);
                if ($isExpired) {
                    $newLicense->save();
                    return  response([
                        'isSuccesful' => true,
                        'message' => "DL Verification Successful",
                        'data' => "License Expired at " . $newLicense->expiryDate
                    
                    ], 200);
                }
                $newLicense->save();
    
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
                'error' => $e->getMessage()
            
            ], 500);
        }
        
    }

    public static function checkIfLicenseExists($license)
    {
        return DriversLicense::orderBy('created_at', 'DESC')->where('licenseNo', $license)->first();
    }
}
