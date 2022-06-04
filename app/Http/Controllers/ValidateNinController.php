<?php

namespace App\Http\Controllers;

use App\Http\Requests\NinRequest;
use App\Models\Nin;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ValidateNinController extends Controller
{
    private $baseUrl;
    public function __construct()
    {
        $this->baseUrl = env('BASE_URL');
    }
    public function store(NinRequest $request)
    {
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
                $response['responseCode'] = '0';
                $response['message'] = "Verification Successful";
                $response['isSuccesful'] = true;
                $response['data'] = $checker;
                Log::info('response gotten ' .json_encode($response));
                return response()->json($response, 200);
                // return  response([
                //     'isSuccesful' => true,
                //     'message' => "Verification Successful",
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
            $url = "{$this->baseUrl}/api/v1/biometrics/merchant/data/verification/nin_wo_face";
    
            $number = $request->number;
    
            $response2 = $client->request('POST', $url, [
                'form_params' => [
                    'number' => $number,
                ]
            ]);
    
            $decodedJson = json_decode($response2->getBody(), TRUE);
            // Log::info($decodedJson);
    
            if ($decodedJson['response_code'] === "00") {
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
    
                $newNin->save();
                $response['responseCode'] = '0';
                $response['message'] = $decodedJson['detail'];
                $response['isSuccesful'] = true;
                $response['data'] = $decodedJson['nin_data'];
                Log::info('response gotten ' .json_encode($response));
                return response()->json($response, 200);
                // return response([
                //     'isSuccesful' => true,
                //     'message' => $decodedJson['detail'],
                //     'data' => $decodedJson['nin_data']
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
                'error' => $e
            
            ], 500);
        }
        
    }

    public static function checkIfNinExists($nin)
    {
        return Nin::where('nin', $nin)->first();
    }
}
