<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NINResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'firstName' => $this->firstname,
            'lastName' => $this->surname,
            'dateOfBirth' => $this->birthdate,
            'gender' => $this->gender,
            'nin' => $this->nin,
        ];
    }
}
