<?php

namespace App\Http\Controllers;

use App\Contract\Responses\DefaultApiResponse;
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
    private $environment;
    private $response;
    public function __construct()
    {
        $this->baseUrl = env('BASE_URL');
        $this->baseUrl2 = env('BASE_URL2');
        $this->environment = env('VERIFICATION_ENV');
        $this->response = new DefaultApiResponse();
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
            $response = [
                'isSuccesful' =>  false,
                'responseCode' => null,
                'data'=> null,
                'message' => null,
            ];
            Log::info('********** BVN Verification from IdentityPass Service *************');
            Log::info($request->all());
            if ($this->environment === "TEST") {
                $request->number = "54651333608";
                $request->lastName = "testing";
                $request->dob = "1999-12-21";
            }
            // Log::info($request->lastName);
            $checker = $this->checkIfBvnExists($request->number);
        if(!empty($checker)){
            // compare text
            $isLastNameMatching = compareText($request->lastName, $checker['lastName']);
            $isFirstNameMatching = compareText($request->lastName, $checker['firstName']);
            if (!($isLastNameMatching || $isFirstNameMatching)) {
                $response['responseCode'] = '1';
                $response['message'] = "Name doesn't Match";
                $response['isSuccesful'] = false;
                Log::info('response gotten ' .json_encode($response));
                return response()->json($response, 400);
                // return response([
                //     'isSuccesful' => true,
                //     'message' => "Name doesn't Match",
                //     // 'data' => $decodedJson['bvn_data']
                // ]);
            }
            //check dob
            if ($request->dob != $checker['dateOfBirth']) {
                $response['responseCode'] = '1';
                $response['message'] = "Invalid Date of Birth";
                $response['isSuccesful'] = false;
                Log::info('response gotten ' .json_encode($response));
                return response()->json($response, 400);
                // return response([
                //     'isSuccesful' => true,
                //     'message' => "Invalid Date of Birth",
                // ]);
            }
            $response['responseCode'] = '0';
            $response['message'] = "Verification Successful";
            $response['isSuccesful'] = true;
            $response['data'] = new BvnResource($checker) ;
            Log::info('response gotten ' .json_encode($response));
            return response()->json($response, 200);
            // return  response([
            //     'isSuccesful' => true,
            //     'message' => "Verification Successful",
            //     'data' => new BvnResource($checker)

            // ], 200);
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

        $response2 = $client->request('POST', $url, [
            'form_params' => [
                'number' => $number,
                'lastName' => $lastName,
                'dob' => $dob
            ]
        ]);
        $decodedJson = json_decode($response2->getBody(), TRUE);
        if ($decodedJson['response_code'] === "00") {
            $newBvn = saveBvn($decodedJson);
            $isLastNameMatching = compareText($lastName, $decodedJson['bvn_data']['lastName']);
            $isFirstNameMatching = compareText($lastName, $decodedJson['bvn_data']['firstName']);
            if (!($isLastNameMatching || $isFirstNameMatching)) {
                $response['responseCode'] = '1';
                $response['message'] = "Name doesn't Match";
                $response['isSuccesful'] = false;
                Log::info('response gotten ' .json_encode($response));
                return response()->json($response, 400);
                // return response([
                //     'isSuccesful' => true,
                //     'message' => "Name doesn't Match",
                // ]);
            }
            //check dob
            if ($request->dob !== $decodedJson['bvn_data']['dateOfBirth']) {
                $response['responseCode'] = '1';
                $response['message'] = "Invalid Date of Birth";
                $response['isSuccesful'] = false;
                Log::info('response gotten ' .json_encode($response));
                return response()->json($response, 400);
                // return response([
                //     'isSuccesful' => true,
                //     'message' => "Invalid Date of Birth",
                // ]);
            }
            $response['responseCode'] = '0';
            $response['message'] = $decodedJson['detail'];
            $response['isSuccesful'] = true;
            $response['data'] = new BvnResource($newBvn) ;
            Log::info('response gotten ' .json_encode($response));
            return response()->json($response, 200);
            // return response([
            //     'isSuccesful' => true,
            //     'message' => $decodedJson['detail'],
            //     'data' => new BvnResource($newBvn)
            // ]);
        }
        $response['responseCode'] = '1';
        $response['message'] = $decodedJson['detail'];
        $response['isSuccesful'] = false;
        $response['data'] = $decodedJson['message'] ;
        Log::info('response gotten ' .json_encode($response));
        return response()->json($response, 200);
        // return response([
        //     'isSuccesful' => true,
        //     'message' => $decodedJson['detail'],
        //     'data' => $decodedJson['message']

        // ], 200);
        // Log::info('response gotten ' .json_encode($response));
        } catch (\Exception $e) {
            Log::info(json_encode($e));
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
            Log::info('********** BVN Verification from Dojah Service *************');
            Log::info($request->all());
            // if ($this->environment === "TEST") {
            //     $request->number = "54651333608";
            //     $request->lastName = "testing";
            //     $request->dob = "1999-12-21";
            // }
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
                    return response()->json($this->response, 400);
                }
                //check dob
                if ($request->dob != $checker['dateOfBirth']) {
                    $this->response->responseCode = '1';
                    $this->response->message = "Invalid Date of Birth";
                    $this->response->isSuccessful = false;
                    Log::info('response gotten ' .json_encode($this->response));
                    return response()->json($this->response, 400);
                }
                $this->response->responseCode = '0';
                $this->response->message = "Verification Successful";
                $this->response->isSuccessful = true;
                $this->response->data = new BvnResource($checker);
                Log::info('response gotten ' .json_encode($this->response));
                return response()->json($this->response, 200);
            }
            $number = $request->number;
            $lastName = $request->lastName;
            $dob = $request->dob;
            $decodedJson = dojahBvn($request, $this->baseUrl2);
            Log::info('data gotten  from dojah' .$decodedJson);
            if ($decodedJson->successful()) {
                $newBvn = saveBvn2($decodedJson);
                $isLastNameMatching = compareText($lastName, $decodedJson['entity']['last_name']);
                $isFirstNameMatching = compareText($lastName, $decodedJson['entity']['first_name']);
                if (!($isLastNameMatching || $isFirstNameMatching)) {
                    $this->response->responseCode = '1';
                    $this->response->message = "Name doesn't Match";
                    $this->response->isSuccessful = false;
                    Log::info('response gotten ' .json_encode($this->response));
                    return response()->json($this->response, 400);
                }
                //check dob
                if ($request->dob !== $newBvn['dateOfBirth']) {
                    $this->response->responseCode = '1';
                    $this->response->message = "Invalid Date of Birth";
                    $this->response->isSuccessful = false;
                    Log::info('response gotten ' .json_encode($this->response));
                    return response()->json($this->response, 400);
                }
                $this->response->responseCode = '0';
                $this->response->message = "Verification Successful";
                $this->response->isSuccessful = true;
                $this->response->data = new BvnResource($newBvn);
                Log::info('response gotten ' .json_encode($this->response));
                return response()->json($this->response, 200);
            }
            $this->response->responseCode = '1';
            $this->response->message = "Bank Verification failed";
            $this->response->isSuccessful = false;
            $this->response->data = "BVN not Found";
            Log::info('response gotten ' .json_encode($this->response));
            return response()->json($this->response, 400);

        } catch (\Exception $e) {
            Log::info(json_encode($e));
            $this->response->responseCode = '1';
            $this->response->message = "Processing Failed, Contact Support";
            $this->response->isSuccessful = false;
            $this->response->error = $e->getMessage();
            Log::info('response gotten ' .json_encode($this->response));
            return response()->json($this->response, 500);
        }

    }





    public static function checkIfBvnExists($bvn)
    {
        return Bvn::where('bvn', $bvn)->first();
    }


}
