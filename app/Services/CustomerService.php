<?php

namespace App\Services;

use App\AuthValidators\CustomerValidator;
use App\Exceptions\StonkamInvalidRequestException;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\CustomerResourceCollection;
use App\Http\Resources\RoleResourceCollection;
use App\Http\Resources\UserResourceCollection;
use App\Models\Customer;
use App\Models\DTOs\CustomerDto;
use App\Models\Group;
use App\Models\Role;
use App\Models\User;
use App\Services\Interfaces\CustomerServiceInterface;
use App\Services\Interfaces\StonkamServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CustomerService implements CustomerServiceInterface
{
    private StonkamServiceInterface $stonkamService;

    public function __construct(StonkamServiceInterface $stonkamService)
    {
        $this->stonkamService = $stonkamService;
    }

    public function create(CustomerDto $customer)
    {
        // create stonkam user
        $this->createUserInStonkam($customer);

        // create stonkam group
        $this->createGroupInStonkam($customer);

        // Create user in Forkers
        $customerModel = new Customer($customer->toArray());
        $customerModel->owner_id = Auth::user()->id;
        $customerModel->save();

        // Create default group in Forkers
        $groupModel = new Group();
        $groupModel->name = 'default';
        $groupModel->description = 'default ' . $customer->name;
        $groupModel->customer_id = $customerModel->id;
        $groupModel->owner_id = Auth::user()->id;

        $groupModel->save();

        return $customerModel;
    }

    public function update(CustomerDto $request, Customer $customer)
    {
        $customer->update([
            'name' => $request->name,
            'description' => $request->description,
            'owner_id' => Auth::user()->id
        ]);
        return $customer;
    }

    public function findById(Customer $customer)
    {
        // Validate Customer
        CustomerValidator::getByIdValidate($customer);
        return new CustomerResource($customer->load('owner'));
    }

    public function getAll($perPage = 15)
    {
        return new CustomerResourceCollection(Customer::with('owner')->paginate($perPage));
    }

    public function delete(Customer $customer)
    {
        $customer->owner_id = Auth::user()->id;
        $customer->save();
        return $customer->delete();
    }

    public function getAllUsers(Customer $customer,$perPage=15)
    {
        return new UserResourceCollection(User::where('customer_id',$customer->id)->with('owner')->paginate($perPage));
    }

    public function getAllRoles(Customer $customer,$perPage=15)
    {
        return new RoleResourceCollection(Role::where('customer_id',$customer->id)->with('owner', 'privileges')->paginate($perPage));
    }

    private function createUserInStonkam(CustomerDto $customer)
    {

        $sessionId = $this->stonkamService->refreshAccessToken();
        $endpoint = config('stonkam.hostname') . "/AddUser/100";

        $data = [
            'UserName' => $customer->stk_user,
            'Password' => config('stonkam.auth.customer.password'),
            'ParentUserName' => config('stonkam.auth.admin.username'),
            'SessionId' => $sessionId
        ];
        Log::info("Requesting stonkam server for creating new user- $customer->stk_user");
        $response = Http::post($endpoint, $data);
        if (!$response->ok()) {
            $content = $response->json();
            Log::warning('Invalid input or stonkam faild. ' . $content['Reason']);
            throw new StonkamInvalidRequestException($content['Reason']);
        }

        Log::info('Create stonkam user success!!');
    }
    private function createGroupInStonkam(CustomerDto $customer)
    { // this group doesnt have any connection with Forkers groups

        $sessionId = $this->stonkamService->refreshAccessToken();
        $endpoint = config('stonkam.hostname') . "/AddGroup/100";

        $data = [
            'UserName' => config('stonkam.auth.admin.username'),
            'GroupName' => $customer->stk_user,
            'SessionId' => $sessionId
        ];
        Log::info("Requesting stonkam server for creating new group- $customer->stk_user");
        $response = Http::post($endpoint, $data);
        if (!$response->ok()) {
            $content = $response->json();
            Log::warning('Invalid input or stonkam faild. ' . $content['Reason']);
            throw new StonkamInvalidRequestException($content['Reason']);
        }

        Log::info('Create stonkam group success!!');
    }
}
