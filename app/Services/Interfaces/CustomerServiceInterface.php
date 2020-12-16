<?php

namespace App\Services\Interfaces;

use App\Models\Customer;
use App\Models\DTOs\CustomerDto;

interface CustomerServiceInterface
{
    public function create(CustomerDto $model);

    public function update(CustomerDto $request,Customer $customer);

    public function findById(Customer $customer);

    public function getAll();

    public function delete(Customer $customer);

    public function getAllUsers(Customer $customer);

    public function getAllRoles(Customer $customer);
}
