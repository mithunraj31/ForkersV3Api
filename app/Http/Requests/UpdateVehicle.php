<?php

namespace App\Http\Requests;

use App\AuthValidators\AuthValidator;
use App\Enum\AccessType;
use App\Enum\ResourceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateVehicle extends FormRequest
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

        //check whether user has relevant privileges

        return AuthValidator::isPrivileged(ResourceType::Vehicle, AccessType::Update);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'max:255',
            'group_id' => 'exists:App\Models\Group,id',
            'customer_id' => 'exists:App\Models\Customer,id',
            'device_id' => 'exists:App\Models\Device,id',
            'introduction_date' => 'date',
            'vehicle_number' => 'max:255',
            'structural_method' => 'max:255',
            'power_type' => 'max:255',
            'rated_load' => 'numeric',
            'fork_length' => 'numeric',
            'standard_lift' => 'numeric',
            'maximum_lift' => 'numeric',
            'battery_voltage' => 'numeric',
            'battery_capacity' => 'numeric',
            'hour_meter_initial_value' => 'numeric',
            'operating_time' => 'numeric',
            'introduction_date' => 'numeric',
            'contract' => 'integer',
            'key_number' => 'max:255',
            'model_id' => 'integer'
        ];
    }
}
