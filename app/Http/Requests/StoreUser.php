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
        if (UserValidator::isAdmin()) return true;

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
            'username' => 'required|max:255|unique:users',
            'customer_id' => 'required|exists:App\Customer,id',
            'role_id' => 'required|exists:App\Role,id',
            'sys_roles' => 'required',
            'privileges' => 'required',
            'password' => 'required',
            'groups' => ['exists:App\Group,id']
        ];
    }
}
