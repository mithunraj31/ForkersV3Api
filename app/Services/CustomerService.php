<?php

namespace App\Services;

use App\Exceptions\StonkamInvalidRequestException;
use App\Models\Customer;
use App\Models\DTOs\CustomerDto;
use App\Services\Interfaces\CustomerServiceInterface;
use App\Services\Interfaces\StonkamServiceInterface;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CustomerService implements CustomerServiceInterface{
    private StonkamServiceInterface $stonkamService;

    public function __construct(StonkamServiceInterface $stonkamService)
    {
        $this->stonkamService = $stonkamService;
    }

    public function create(CustomerDto $customer) {
       return  $this->createUserInStonkam($customer);
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
            Log::alert('Invalid input or stonkam faild');
            $content = $response->json();
            throw new StonkamInvalidRequestException($content['Reason']);
        }

        Log::info('Create stonkam user success!!');
    }

}

