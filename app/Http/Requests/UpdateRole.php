<?php

namespace App\Http\Requests;

use App\AuthValidators\AuthValidator;
use App\Enum\AccessType;
use App\Enum\ResourceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateRole extends FormRequest
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
        if (AuthValidator::isAdmin()) return true;

        //check whether user has relevent privileges

        return AuthValidator::isPrivileged(ResourceType::Role, AccessType::Update);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'privileges.*.resource' => 'required|max:100',
            'privileges.*.add' => 'required|boolean',
            'privileges.*.edit' => 'required|boolean',
            'privileges.*.delete' => 'required|boolean',
            'privileges.*.view' => 'required|boolean',
            'customer_id' => 'exists:App\Models\Customer,id',
            'description' => 'nullable'
        ];
    }
}
