<?php


namespace App\Http\Requests;

use App\AuthValidators\AuthValidator;
use App\Enum\AccessType;
use App\Enum\ResourceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AddVehicle extends FormRequest
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

        //check whether user has relevant privileges

        return AuthValidator::isPrivileged(ResourceType::Vehicle, AccessType::Add);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required| max:255',
            'customer_id' => 'exists:App\Models\Vehicle,id',
            'group_id' => 'required| exists:App\Models\Group,id',
            'device_id' => 'exists:App\Models\Device,id'
        ];
    }
}
