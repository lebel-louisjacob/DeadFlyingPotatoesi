<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SensorReadingRequest extends FormRequest
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
            'value'      => 'required|numeric',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'type' => 'required|string|exists:reading_types,type',
        ];
    }
}
