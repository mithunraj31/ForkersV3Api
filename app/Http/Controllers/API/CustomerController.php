<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteCustomer;
use App\Http\Requests\IndexCustomer;
use App\Http\Requests\IndexCustomerGroup;
use App\Http\Requests\IndexCustomerRole;
use App\Http\Requests\IndexCustomerUser;
use App\Http\Requests\StoreCustomer;
use App\Http\Requests\UpdateCustomer;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\CustomerResourceCollection;
use App\Models\Customer;
use App\Models\DTOs\CustomerDto;
use App\Services\Interfaces\CustomerServiceInterface;
use App\Services\Interfaces\GroupServiceInterface;

class CustomerController extends Controller
{

    private CustomerServiceInterface $customerService;
    private GroupServiceInterface $groupService;

    public function __construct(CustomerServiceInterface $customerService, GroupServiceInterface $groupService)
    {
        $this->customerService = $customerService;
        $this->groupService = $groupService;
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
        } else {
            return response(['message' => 'something went wrong!'], 500);
        }
    }

    public function update(UpdateCustomer $request, Customer $customer)
    {
        $customerR = new CustomerDto();
        $customerR->name = $request->name;
        $customerR->description = $request->description;
        return $this->customerService->update($customerR, $customer);
    }

    public function delete(DeleteCustomer $request, Customer $customer)
    {
        return $this->customerService->delete($customer);
    }

    public function indexUsers(IndexCustomerUser $request, Customer $customer)
    {
        return $this->customerService->getAllUsers($customer, $request->query('perPage'));
    }

    public function indexRoles(IndexCustomerRole $request, Customer $customer)
    {
        return $this->customerService->getAllRoles($customer, $request->query('perPage'));
    }

    public function indexGroups(IndexCustomerGroup $request, Customer $customer)
    {
        return $this->groupService->getAllByCustomer($customer, $request->query('perPage'));
    }
}
