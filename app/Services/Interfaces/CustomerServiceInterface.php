<?php

namespace App\Services\Interfaces;

use App\Models\Customer;
use App\Models\DTOs\CustomerDto;

interface CustomerServiceInterface
{
    public function create(CustomerDto $model);

    public function update(CustomerDto $request,Customer $customer);

    public function findById(Customer $customer);

    public function getAll($perPage);

    public function delete(Customer $customer, $perPage);

    public function getAllUsers(Customer $customer, $perPage);

    public function getAllRoles(Customer $customer, $perPage);
}
