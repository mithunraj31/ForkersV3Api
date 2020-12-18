<?php


namespace App\Services\Interfaces;


use App\Models\Customer;
use App\Models\DTOs\GroupDto;
use App\Models\Group;

interface GroupServiceInterface
{
    public function create(GroupDto $request);

    public function update(GroupDto $request,Group $group);

    public function findById(Group $group);

    public function getAll();

    public function delete(Group $group);

    public function getAllUsers(Group $group);

    public function getAllDevices(Group $group);

    function getAllByCustomer(Customer $customer);
}
