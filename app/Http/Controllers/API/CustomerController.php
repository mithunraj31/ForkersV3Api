<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\AuthValidators\CustomerValidator;
use App\Http\Requests\DeleteCustomer;
use App\Http\Requests\IndexCustomer;
use App\Http\Requests\StoreCustomer;
use App\Http\Requests\UpdateCustomer;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\CustomerResourceCollection;
use App\Models\Customer;
use App\Models\DTOs\CustomerDto;
use App\Services\Interfaces\CustomerServiceInterface;
use Illuminate\Http\Request;

class CustomerController extends Controller
{

    private CustomerServiceInterface $customerService;

    public function __construct(CustomerServiceInterface $customerService)
    {
        $this->customerService = $customerService;
    }

    public function show(Customer $customer): CustomerResource
    {
        return $this->customerService->findById($customer);
    }
    public function index(IndexCustomer $request): CustomerResourceCollection
    {
        return $this->customerService->getAll($request->query('perPage'));
    }
    public function store(StoreCustomer $request)
    {
        $customer = new CustomerDto();
        $customer->stk_user = $request->stk_user;
        $customer->name = $request->name;
        $customer->description = $request->description;
        if ($this->customerService->create($customer)) {
            return response(['message' => 'success!'], 201);
        }
    }


    public function update(UpdateCustomer $request, Customer $customer)
    {   $customerR = new CustomerDto();
        $customerR->name = $request->name;
        $customerR->description = $request->description;
        return $this->customerService->update($customerR, $customer);
    }
    public function delete(DeleteCustomer $request, Customer $customer)
    {
        return $this->customerService->delete($customer);
    }
}
