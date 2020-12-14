<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\AuthValidators\CustomerValidator;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\CustomerResourceCollection;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
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
}
