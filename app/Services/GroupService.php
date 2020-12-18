<?php


namespace App\Services;


use App\AuthValidators\AuthValidator;
use App\Http\Resources\GroupResourceCollection;
use App\Models\Customer;
use App\Models\DTOs\GroupDto;
use App\Models\Group;
use App\Services\Interfaces\GroupServiceInterface;

class GroupService extends ServiceBase implements GroupServiceInterface
{
    public function create(GroupDto $request)
    {
        // TODO: Implement create() method.
    }


    public function update(GroupDto $request, Group $group)
    {
        // TODO: Implement update() method.
    }

    public function findById(Group $group)
    {
        // TODO: Implement findById() method.
    }

    public function getAll($perPage=15): GroupResourceCollection
    {
            $customer = Customer::where('stk_user', AuthValidator::getStkUser())->first();
            return $this->getAllByCustomer($customer);

    }
     public function getAllByCustomer(Customer $customer, $perPage=15): GroupResourceCollection
     {
         $groups = Group::where('customer_id', $customer->id)->with('owner', 'customer','children');
         return new GroupResourceCollection($groups->paginate($perPage));
     }

    public function delete(Group $group)
    {
        // TODO: Implement delete() method.
    }

    public function getAllUsers(Group $group, $perPage)
    {
        // TODO: Implement getAllUsers() method.
    }

    public function getAllDevices(Group $group, $perPage)
    {
        // TODO: Implement getAllDevices() method.
    }
}
