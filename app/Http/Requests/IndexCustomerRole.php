<?php

namespace App\Http\Requests;

use App\AuthValidators\AuthValidator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class IndexCustomerRole extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        //check whether the user is logged in
        if (!Auth::check()) return false;

        //check whether user is admin
        if (AuthValidator::isAdmin()){
            return true;
        } else  {
            return false;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
        ];
    }
}
