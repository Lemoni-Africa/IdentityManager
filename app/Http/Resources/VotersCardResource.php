<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VotersCardResource extends JsonResource
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
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'dateOfBirth' => $this->date_of_birth,
            'fullName' => $this->fullName,
            'vin' => $this->vin,
        ];
    }
}
