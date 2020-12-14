<?php

namespace App\Services;

use App\Exceptions\StonkamInvalidRequestException;
use App\Models\Customer;
use App\Models\DTOs\CustomerDto;
use App\Models\Group;
use App\Services\Interfaces\CustomerServiceInterface;
use App\Services\Interfaces\StonkamServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CustomerService implements CustomerServiceInterface{
    private StonkamServiceInterface $stonkamService;

    public function __construct(StonkamServiceInterface $stonkamService)
    {
        $this->stonkamService = $stonkamService;
    }

    public function create(CustomerDto $customer) {
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
        $groupModel->description = 'default '.$customer->name;
        $groupModel->customer_id = $customerModel->id;
        $groupModel->owner_id = Auth::user()->id;

        $groupModel->save();

        return $customerModel;

    }

    public function update(Customer $customer) {

    }

    public function findById(Customer $customer) {

    }

    public function getAll(){

    }

    public function delete(Customer $customer){

    }

    private function createUserInStonkam(CustomerDto $customer) {

        $sessionId = $this->stonkamService->refreshAccessToken();
        $endpoint = env('STONKAM_HOSTNAME') . "/AddUser/100";

        $data = [
            'UserName' => $customer->stk_user,
            'Password' => env('STONKAM_CUSTOMER_PASSWORD'),
            'ParentUserName' => env('STONKAM_AUTH_ADMIN_USERNAME'),
            'SessionId' => $sessionId
        ];
        Log::info("Requesting stonkam server for creating new user- $customer->stk_user");
        $response = Http::post($endpoint, $data);
        if(!$response->ok()){
            $content = $response->json();
            Log::warning('Invalid input or stonkam faild. '.$content['Reason']);
            throw new StonkamInvalidRequestException($content['Reason']);
        }

        Log::info('Create stonkam user success!!');
    }
    private function createGroupInStonkam(CustomerDto $customer) { // this group doesnt have any connection with Forkers groups

        $sessionId = $this->stonkamService->refreshAccessToken();
        $endpoint = env('STONKAM_HOSTNAME') . "/AddGroup/100";

        $data = [
            'UserName' => env('STONKAM_AUTH_ADMIN_USERNAME'),
            'GroupName' => $customer->stk_user,
            'SessionId' => $sessionId
        ];
        Log::info("Requesting stonkam server for creating new group- $customer->stk_user");
        $response = Http::post($endpoint, $data);
        if(!$response->ok()){
            $content = $response->json();
            Log::warning('Invalid input or stonkam faild. '.$content['Reason']);
            throw new StonkamInvalidRequestException($content['Reason']);
        }

        Log::info('Create stonkam group success!!');
    }

}

