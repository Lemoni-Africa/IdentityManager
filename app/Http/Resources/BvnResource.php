<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BvnResource extends JsonResource
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
            'firstName' => $this->firstName,
            'middleName' => $this->middleName,
            'lastName' => $this->lastName,
            'dateOfBirth' => $this->dateOfBirth,
            'bvn' => $this->bvn,
        ];
    }
}
