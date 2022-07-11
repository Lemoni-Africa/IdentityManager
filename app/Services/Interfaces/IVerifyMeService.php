<?php

namespace App\Services\Interfaces;

use App\Contract\Responses\DefaultApiResponse;
use App\Http\Requests\BvnRequest;
use App\Http\Requests\CardsRequest;
use App\Http\Requests\SelfieRequest;

interface IVerifyMeService 
{
    public function validateBvn(BvnRequest $request): DefaultApiResponse;
    public function BvnSelfie(SelfieRequest $request): DefaultApiResponse;
    public function Nin(CardsRequest $request): DefaultApiResponse;
    public function votersCard(CardsRequest $request): DefaultApiResponse;
    public function driversLicense(CardsRequest $request): DefaultApiResponse;
}