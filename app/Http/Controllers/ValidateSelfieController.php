<?php

namespace App\Http\Controllers;

use App\Http\Requests\SelfieRequest;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ValidateSelfieController extends Controller
{
    private $baseUrl;
    private $baseUrl2;
    public function __construct()
    {
        $this->baseUrl = env('BASE_URL');
        $this->baseUrl2 = env('BASE_URL2');
    }
    //
    public function BvnSelfie(SelfieRequest $request)
    {
        try {
            $headers = [
                'Content-Type' => 'application/json',
                'Authorization' => env('AUTHKEY'),
                'AppId' => env('APPID')
            ];
            $client = new Client([
                'headers' => $headers
            ]);
            $bvn = $request->number;
            $selfie_image = $request->selfie_image;
    
            $url = "{$this->baseUrl2}/api/v1/kyc/bvn/verify";
    
            
            $response = $client->request('POST', $url, [
                'form_params' => [
                    'bvn' => $bvn,
                    'selfie_image' => $selfie_image
                ]
            ]);
          
            $statusCode = $response->getStatusCode();
            $decodedJson = json_decode($response->getBody(), TRUE);
            Log::info("*****************BVN SELFIE******************************");
            Log::info("Request Image => " . $selfie_image);
            Log::info("Response Image => " . $decodedJson['entity']['image']);
            if ($statusCode === 200) {
                return response([
                    'isSuccesful' => true,
                    'message' => "Verification Successful",
                    'data' => $decodedJson['entity']['selfie_verification']
                    
                ],200);
            }
            return response([
                'isSuccesful' => true,
                'message' => "Verification Successful",
                'data' => $decodedJson['entity']['selfie_verification']
                
            ],200);
        } catch (\Exception $e) {
            return response([
                'isSuccesful' => false,
                'message' => 'Processing Failed, Contact Support',
                'error' => $e->getMessage()
            
            ], 500);
        }
       
    }



    public function NinSelfie(SelfieRequest $request)
    {
        try {
            $headers = [
                'Content-Type' => 'application/json',
                'Authorization' => env('AUTHKEY'),
                'AppId' => env('APPID')
            ];
            $client = new Client([
                'headers' => $headers
            ]);
            $nin = $request->number;
            $selfie_image = $request->selfie_image;
    
            $url = "{$this->baseUrl2}/api/v1/kyc/nin/verify";
    
            
            $response = $client->request('POST', $url, [
                'form_params' => [
                    'nin' => $nin,
                    'selfie_image' => $selfie_image
                ]
            ]);
          
            $statusCode = $response->getStatusCode();
            $decodedJson = json_decode($response->getBody(), TRUE);
            Log::info("*****************NIN SELFIE******************************");
            Log::info("Request Image => " . $selfie_image);
            Log::info("Response Image => " . $decodedJson['entity']['picture']);
            if ($statusCode === 200) {
                return response([
                    'isSuccesful' => true,
                    'message' => "Verification Successful",
                    'data' => $decodedJson['entity']['selfie_verification']
                    
                ],200);
            }
            return response([
                'isSuccesful' => true,
                'message' => "Verification Successful",
                'data' => $decodedJson['entity']['selfie_verification']
                
            ],200);
        } catch (\Exception $e) {
            return response([
                'isSuccesful' => false,
                'message' => 'Processing Failed, Contact Support',
                'error' => $e->getMessage()
            
            ], 500);
        }
       
    }
}
