<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTripRequest extends FormRequest
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
            'departure_datetime'            => 'required|date|after:today',
            'departure_coord.display_name'  => 'required|string',
            'departure_coord.lat'           => 'required|numeric|between:-90,90',
            'departure_coord.lon'           => 'required|numeric|between:-180,180',
            'arrival_coord.display_name'    => 'required|string',
            'arrival_coord.lat'             => 'required|numeric|between:-90,90',
            'arrival_coord.lon'             => 'required|numeric|between:-180,180',
            'instant_booking'               => 'boolean',
            'price'                         => 'required|numeric|min:1',
            'vehicle_info.model_id'         => 'required|exists:vehicle_models,id',
            'vehicle_info.category_id'      => 'required|exists:vehicle_categories,id',
            'vehicle_info.color'            => [
                'required',
                'regex:/^#([a-f0-9]{6}|[a-f0-9]{3})$/i'
            ],
            'vehicle_info.license_plate'    => 'required|alpha_num:ascii',
            'vehicle_info.max_seats'        => 'required|integer|min:1',
        ];
    }
}
