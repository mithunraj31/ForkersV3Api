<?php

namespace App\Http\Requests;

use App\AuthValidators\ManufacturerValidator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DeleteManufacturer extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (!Auth::check()) {
            return false;
        }

        ManufacturerValidator::deleteValidator();

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
