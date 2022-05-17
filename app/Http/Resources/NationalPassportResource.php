<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NationalPassportResource extends JsonResource
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
            'middleName' => $this->middle_name,
            'lastName' => $this->last_name,
            'dateOfBirth' => $this->dob,
            'expiryDate' => $this->expiry_date,
            'gender' => $this->gender,
            'number' => $this->request_number,
        ];
    }
}
