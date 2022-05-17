<?php

use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class TwilioSmsHelper
{
    private $client;
    private $token;
    private $twilio_number;
    private $sid;
    public function __construct()
    {
        $this->sid = env('TWILIO_SID');
        // logger("TWILIO_SID ".$this->sid );
        $this->token  = env('TWILIO_TOKEN');
        $this->twilio_number = env("TWILIO_NUMBER");
        $this->client = new Client($this->sid, $this->token);
    }

    /**
     * @throws \Twilio\Exceptions\TwilioException
     */
    public function sendSms($message, $recipients): \Twilio\Rest\Api\V2010\Account\MessageInstance
    {

        $message = $this->client->messages->create($recipients,
            ['from' => $this->twilio_number, 'body' => $message] );
        Log::info($message);
            return $message;
    }

}

// TWILIO_SID=AC3ba2003a9086db52c609454e4258dfb3
// TWILIO_TOKEN=611a9a73ed543824de1c7437db69835b
// TWILIO_NUMBER=2348038866351