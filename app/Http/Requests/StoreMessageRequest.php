<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Trip;

class StoreMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // is the user a passenger of that trip ?
        $userId = Auth::user()->id;
        // $trip = Trip::find(intval($this->trip));
        
        foreach($this->trip->passengers as $passenger) {
            if ($userId === $passenger->id) {
                return true;
            }
        }

        if($this->trip->driver_id === $userId) { return true; }
        
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'content' => 'required|string'
        ];
    }
}
