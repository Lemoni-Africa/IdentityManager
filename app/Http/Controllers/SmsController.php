<?php

namespace App\Http\Controllers;

use App\Http\Requests\SmsRequest;
use App\Http\Resources\SmsResource;
use App\Models\Sms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;
use TwilioSmsHelper;

class SmsController extends Controller
{
    private $baseUrl;
    private $apiKey;
    private $token;
    private $twilio_number;
    private $sid;
    private $client;
    public function __construct()
    {
        $this->baseUrl = env('SMS_URL');
        $this->apiKey = env('SMS_API_KEY');
        $this->sid = env('TWILIO_SID');
        $this->token  = env('TWILIO_TOKEN');
        $this->twilio_number = env("TWILIO_NUMBER");
        $this->client = new Client($this->sid, $this->token);
    }


    public function createSms(SmsRequest $request)
    {
        try {
            $response = [
                'isSuccess' =>  false,
                'responseCode' => null,
                'data'=> null,
                'message' => null,
            ];
            $body = $request->body;
            $to = $request->to;
            $from = $request->from;
            foreach ($to as $value) {
                $message = $this->send_message($value, $body);
                // Log::info($message);
           
                $decodeMesaage = json_decode($message);
                $sms = new Sms();
                $sms->AddSms($decodeMesaage, $from , $this->twilio_number, $body, $value);
            }
            

            return response([
                'isSuccesful' => true,
                'responseCode' => "0",
                'message' => "Message Sent",
                'data' => [
                    'status' => "success"
                ]
            ], 200);
            // $message = $this->sendSMS($to, $body);
            
         
        } catch (\Exception $e) {
            return response([
                'isSuccesful' => false,
                'message' => 'Processing Failed, Contact Support',
                'error' => $e->getMessage(),
                'error' => $decodeMesaage->message
            ]);
        }
    }

    public function sendSMS(string $toPhoneNumber, string $message): \Twilio\Rest\Api\V2010\Account\MessageInstance
    {
        return $this->client->messages->create($toPhoneNumber, [
            "body" => $message,
            "from" => $this->twilio_number
        ]);
    }
    
    function send_message($to, $body) {
        $res = false;
    
        $url = 'https://api.twilio.com/2010-04-01/Accounts/' . $this->sid . '/Messages.json';
    
        $msg = "From=".urlencode($this->twilio_number)."&To=".urlencode($to)."&Body=".urlencode($body);
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $msg); 
        curl_setopt($ch, CURLOPT_USERPWD, $this->sid . ':' . $this->token);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $res = curl_exec($ch);
        curl_close($ch);
        
        
        return $res;
    
    }
}
