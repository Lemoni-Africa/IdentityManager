<?php

namespace App\Http\Controllers;

use App\Http\Requests\VotersCardRequest;
use App\Models\VotersCard;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ValidateVotersCardController extends Controller
{
    private $baseUrl;
    public function __construct()
    {
        $this->baseUrl = env('BASE_URL');
    }
    public function store(VotersCardRequest $request)
    {
        try {
            $checker = $this->checkIfVotersCardExists($request->number);
            if(!empty($checker)){
                // Log::info('datbase');
                return  response([
                    'isSuccesful' => true,
                    'message' => "VIN Verification Successful",
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
        $url = "{$this->baseUrl}/api/v1/biometrics/merchant/data/verification/voters_card";

        $number = $request->number;
        $last_name = $request->last_name;
        $state = $request->state;

        $response = $client->request('POST', $url, [
            'form_params' => [
                'number' => $number,
                'last_name' => $last_name,
                'state' => $state
            ]
        ]);

        $decodedJson = json_decode($response->getBody(), TRUE);
        // Log::info($decodedJson);
        if ($decodedJson['response_code'] === "00") {
            $newVotersCard = new VotersCard;
            $newVotersCard->gender = $decodedJson['vc_data']['gender'];
            $newVotersCard->vin = $decodedJson['vc_data']['vin'];
            $newVotersCard->first_name = $decodedJson['vc_data']['first_name'];
            $newVotersCard->last_name = $decodedJson['vc_data']['last_name'];
            $newVotersCard->date_of_birth = $decodedJson['vc_data']['date_of_birth'];
            $newVotersCard->fullName = $decodedJson['vc_data']['fullName'];
            $newVotersCard->occupation = $decodedJson['vc_data']['occupation'];
            $newVotersCard->timeOfRegistration = $decodedJson['vc_data']['timeOfRegistration'];
            $newVotersCard->lga = $decodedJson['vc_data']['lga'];
            $newVotersCard->state = $decodedJson['vc_data']['state'];
            $newVotersCard->registrationAreaWard = $decodedJson['vc_data']['registrationAreaWard'];
            $newVotersCard->pollingUnit = $decodedJson['vc_data']['pollingUnit'];
            $newVotersCard->pollingUnitCode = $decodedJson['vc_data']['pollingUnitCode'];

            $newVotersCard->save();

            return response([
                'isSuccesful' => true,
                'message' => $decodedJson['detail'],
                'data' => $decodedJson['vc_data']
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

    public static function checkIfVotersCardExists($card)
    {
        return VotersCard::where('vin', $card)->first();
    }
}
