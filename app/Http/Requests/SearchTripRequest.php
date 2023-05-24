<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchTripRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'departure_datetime'    => 'required|date|after:today',
            'departure_coord'       => 'required|array',
            'departure_coord.lat'   => 'required|numeric|between:-90,90',
            'departure_coord.lon'   => 'required|numeric|between:-180,180',
            'arrival_coord.lat'     => 'required|numeric|between:-90,90',
            'arrival_coord.lon'     => 'required|numeric|between:-180,180',
            'vehicle_info.max_seats'        => 'integer|min:1',
        ];
    }
}
