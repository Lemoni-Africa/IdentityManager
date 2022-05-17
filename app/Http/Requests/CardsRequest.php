<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CardsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "number" => "required",
            "first_name" => "string|regex:/^[a-zA-Z]+$/u",
            "last_name" => "string|regex:/^[a-zA-Z]+$/u",
            "dob" => "date_format:Y-m-d",
            "type" => "required|in:NIN,VTRCARD,PASSPORT,DRIVLICE",
            "state" => "string"
        ];
    }
}
