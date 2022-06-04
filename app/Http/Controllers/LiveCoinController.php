<?php

namespace App\Http\Controllers;

use App\Http\Resources\CoinCollection;
use App\Http\Resources\CoinResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LiveCoinController extends Controller
{
    private $coinUrl;
    private $usdRate;
    public function __construct()
    {
        $this->coinUrl = env("COIN_GECKO");
        $this->usdRate = env("USD_RATE");
    }

    public function getCoin()
    {
        try {
            $response = [
                'isSuccessful' =>  false,
                'responseCode' => null,
                'data'=> null,
                'message' => null,
            ];
            // getPrices($baseUrl, $currency, $page, $perPage)
            $data = getPrices($this->coinUrl, 'usd',1 , 10);
            // $filtered = array_filter(json_decode($data) , function($value) { 
            //     return $value->id == "bitcoin" || $value->id == "ethereum" || $value->id == "tether";
            // }); 
            
            $encodedJson = json_decode($data); 
            $coins = [];
            foreach($encodedJson as $service){
                $id  = $service->id;
                $name = $service->name;
                $image = $service->image;
                $current_price_usd = $service->current_price;
                $current_price_ngn = $service->current_price * (int) $this->usdRate;
                $last_updated = $service->last_updated;
                $data = array('id' => $id, 'name' => $name, 'image' => $image, 'current_price_usd' => $current_price_usd, 'current_price_ngn' => $current_price_ngn, 'last_updated' => $last_updated);
                array_push($coins,$data);
            }
            // Log::info($encodedJson);
            $response['responseCode'] = '0';
            $response['message'] = 'Successful';
            $response['isSuccesful'] = true;
            $response['data'] = $coins;
            Log::info('response gotten ' .json_encode($response));
            return response()->json($response, 200);
            // return response([
            //     'isSuccessful' => true,
            //     'message' => 'Successful',
            //     'data' => $coins
            // ],200);

        } catch (\Exception $e) {
            Log::info(json_encode($e));
            return response([
                'isSuccessful' => false,
                'message' => 'Processing Failed, Contact Support',
                'error' => $e->getMessage(),
            ]);
        }
    }
}
