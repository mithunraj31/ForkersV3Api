<?php

namespace App\Http\Requests;

use App\AuthValidators\AuthValidator;
use App\AuthValidators\UserValidator;
use App\Enum\AccessType;
use App\Enum\ResourceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreUser extends FormRequest
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

        return AuthValidator::isPrivileged(ResourceType::User, AccessType::Add);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'username' => 'required|email|max:255|unique:users',
            'customer_id' => 'exists:App\Models\Customer,id',
            'role_id' => 'required|exists:App\Models\Role,id',
            'sys_role' => 'in:admin,user',
            'password' => 'required',
            'groups' => ['exists:App\Models\Group,id']
        ];
    }
}
