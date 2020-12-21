<?php


namespace App\Http\Requests;


use App\AuthValidators\AuthValidator;
use App\Enum\AccessType;
use App\Enum\ResourceType;
use Illuminate\Support\Facades\Auth;

class StoreGroup
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        //check whether the user is logged in
        if (!Auth::check()) return false;

        //check whether user is admin
        if (AuthValidator::isAdmin()) return true;

        //check whether user has relevent privileges

        return AuthValidator::isPrivileged(ResourceType::Group, AccessType::Add);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'description' => 'nullable|max:255',
            'parent_id' => 'nullable|exists:App\Models\Group,id',
            'customer_id' => 'exists:App\Models\Customer,id',
        ];
    }
}
