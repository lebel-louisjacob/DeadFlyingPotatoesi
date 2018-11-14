<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StationRequest extends FormRequest
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
            'name'      => 'required|string',
            'city'      => 'required|string|exists:cities,name',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'is_private'=> 'required|boolean'
        ];
    }
}
