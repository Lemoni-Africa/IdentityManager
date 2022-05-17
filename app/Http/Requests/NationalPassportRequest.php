<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NationalPassportRequest extends FormRequest
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
            'number' => 'required',
            'dob' => 'required|date_format:Y-m-d',
            "first_name" => "required",
            "last_name" => "required",
        ];
    }
}  
