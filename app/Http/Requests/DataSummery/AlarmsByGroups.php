<?php

namespace App\Http\Requests\DataSummery;

use App\AuthValidators\AuthValidator;
use App\Enum\AccessType;
use App\Enum\ResourceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AlarmsByGroups extends FormRequest
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

        // return AuthValidator::isPrivileged(ResourceType::Customer, AccessType::View);
        return true; // this is a temp need to decide resource
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'start' => 'required | date',
            'end' => 'required | date',
            'group_ids' => [ 'exists:App\Models\Group,id'],
            'customer_id' => 'exists:App\Models\Customer,id'
        ];
    }
}
