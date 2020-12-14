<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\AuthValidators\CustomerValidator;
use App\Http\Requests\StoreCustomer;
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

    public function show(Customer $customer):CustomerResource
    {
        CustomerValidator::validate();
        return new CustomerResource($customer->load('owner'));
    }
    public function index(Request $request):CustomerResourceCollection
    {
        CustomerValidator::validate();
        $perPage = $request->query('perPage')?(int)$request->query('perPage'):15;
        return new CustomerResourceCollection(Customer::with('owner')->paginate($perPage));
    }
    public function store(StoreCustomer $request)
    {
        $customer = new CustomerDto();
        $customer->stk_user = $request->stk_user;
        $customer->name = $request->name;
        $customer->description = $request->description;
        return $this->customerService->create($customer);
    }
}
