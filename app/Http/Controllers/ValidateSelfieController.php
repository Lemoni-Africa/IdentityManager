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
            $response = [
                'isSuccesful' =>  false,
                'responseCode' => null,
                'data'=> null,
                'message' => null,
            ];
            // Log::info('********** National Passport Verification from Dojah Service *************');
            Log::info($request->all());
            $bvn = $request->number;
            $selfie_image = $request->selfie_image;
            $decodedJson = bvnSelfieDojah($bvn, $selfie_image, $this->baseUrl2);
            Log::info('data gotten from dojah ' . $decodedJson);
            Log::info("*****************BVN SELFIE******************************");
            Log::info("Request Image => " . $selfie_image);
            Log::info("Response Image => " . $decodedJson['entity']['image']);

            if ($decodedJson->successful()) {
                if ($decodedJson['entity']['selfie_verification']['match']) {
                    $response['responseCode'] = '0';
                    $response['message'] = "Verification Successful";
                    $response['isSuccesful'] = true;
                    $response['data'] = $decodedJson['entity']['selfie_verification'];
                    Log::info('response gotten ' .json_encode($response));
                    return response()->json($response, 200);
                }
                $response['responseCode'] = '1';
                $response['message'] = "Verification Failed";
                $response['isSuccesful'] = false;
                $response['data'] = $decodedJson['entity']['selfie_verification'];
                Log::info('response gotten ' .json_encode($response));
                return response()->json($response, 400);
            }
            $response['responseCode'] = '1';
            $response['message'] = "Verification Failed";
            $response['isSuccesful'] = false;
            $response['data'] = $decodedJson['entity']['selfie_verification'] ?? 'Failed';
            Log::info('response gotten ' .json_encode($response));
            return response()->json($response, 400);
            // return response([
            //     'isSuccesful' => true,
            //     'message' => "Verification Successful",
            //     'data' => $decodedJson['entity']['selfie_verification']

            // ],200);
        } catch (\Exception $e) {
            Log::info(json_encode($e));
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
            $response = [
                'isSuccesful' =>  false,
                'responseCode' => null,
                'data'=> null,
                'message' => null,
            ];
            // Log::info('********** National Passport Verification from Dojah Service *************');
            Log::info($request->all());
            // $headers = [
            //     'Content-Type' => 'application/json',
            //     'Authorization' => env('AUTHKEY'),
            //     'AppId' => env('APPID')
            // ];
            // $client = new Client([
            //     'headers' => $headers
            // ]);
            $nin = $request->number;
            $selfie_image = $request->selfie_image;

            // $url = "{$this->baseUrl2}/api/v1/kyc/nin/verify";


            // $response2 = $client->request('POST', $url, [
            //     'form_params' => [
            //         'nin' => $nin,
            //         'selfie_image' => $selfie_image
            //     ]
            // ]);

            // $statusCode = $response2->getStatusCode();
            // $decodedJson = json_decode($response2->getBody(), TRUE);
            $decodedJson = NinSelfieDojah($nin, $selfie_image, $this->baseUrl2);
            Log::info("*****************NIN SELFIE******************************");
            Log::info("Request Image => " . $selfie_image);
            Log::info("Response Image => " . $decodedJson['entity']['picture']);
            if ($decodedJson->successful()) {
                if ($decodedJson['entity']['selfie_verification']['match']) {
                    $response['responseCode'] = '0';
                    $response['message'] = "Verification Successful";
                    $response['isSuccesful'] = true;
                    $response['data'] = $decodedJson['entity']['selfie_verification'];
                    Log::info('response gotten ' .json_encode($response));
                    return response()->json($response, 200);
                }
                $response['responseCode'] = '1';
                $response['message'] = "Verification Failed";
                $response['isSuccesful'] = false;
                $response['data'] = $decodedJson['entity']['selfie_verification'];
                Log::info('response gotten ' .json_encode($response));
                return response()->json($response, 400);

                // $response['responseCode'] = '0';
                // $response['message'] = "Verification Successful";
                // $response['isSuccesful'] = true;
                // $response['data'] = $decodedJson['entity']['selfie_verification'];
                // Log::info('response gotten ' .json_encode($response));
                // return response()->json($response, 200);
                // return response([
                //     'isSuccesful' => true,
                //     'message' => "Verification Successful",
                //     'data' => $decodedJson['entity']['selfie_verification']

                // ],200);
            }
            $response['responseCode'] = '1';
            $response['message'] = "Verification Failed";
            $response['isSuccesful'] = false;
            $response['data'] = $decodedJson['entity']['selfie_verification'] ?? 'Failed';
            Log::info('response gotten ' .json_encode($response));
            return response()->json($response, 400);

            // $response['responseCode'] = '0';
            // $response['message'] = "Verification Successful";
            // $response['isSuccesful'] = true;
            // $response['data'] = $decodedJson['entity']['selfie_verification'];
            // Log::info('response gotten ' .json_encode($response));
            // return response()->json($response, 200);
            // return response([
            //     'isSuccesful' => true,
            //     'message' => "Verification Successful",
            //     'data' => $decodedJson['entity']['selfie_verification']

            // ],200);
        } catch (\Exception $e) {
            Log::info(json_encode($e));
            return response([
                'isSuccesful' => false,
                'message' => 'Processing Failed, Contact Support',
                'error' => $e->getMessage()

            ], 500);
        }

    }
}
