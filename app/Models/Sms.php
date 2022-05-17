<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sms extends Model
{
    use HasFactory;

    public function AddSms($data, $from, $fromNumber, $body, $to) 
    {
        
        $this->message_from_number = $fromNumber;
        $this->message_from = $from;
        $this->message_body = $body;
        $this->message_to = $to;
        $this->status = $data->status;
        // $this->message = $data['data']['message'];
        $this->cost = $data->price;
        $this->message_id = $data->sid;
        $this->currency = $data->price_unit;
        $this->provider = "Twilio";
        $this->save();

        return $this;
    }
    // public function AddSms($data, $from, $body, $to) 
    // {
    //     $this->message_from = $from;
    //     $this->message_body = $body;
    //     $this->message_to = json_encode($to);
    //     $this->status = $data['data']['status'];
    //     $this->message = $data['data']['message'];
    //     $this->cost = $data['data']['cost'];
    //     $this->message_id = $data['data']['message_id'];
    //     $this->currency = $data['data']['currency'];
    //     $this->gateway_used = $data['data']['gateway_used'];
    //     $this->save();

    //     return $this;
    // }
}