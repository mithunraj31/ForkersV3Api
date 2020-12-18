<?php


namespace App\Services;


use App\AuthValidators\AuthValidator;
use App\AuthValidators\GroupValidator;
use App\Http\Resources\GroupResourceCollection;
use App\Models\Customer;
use App\Models\DTOs\GroupDto;
use App\Models\Group;
use App\Services\Interfaces\GroupServiceInterface;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
use InvalidArgumentException as GlobalInvalidArgumentException;

class GroupService extends ServiceBase implements GroupServiceInterface
{
    public function create(GroupDto $request): bool
    {
        GroupValidator::storeGroupValidator($request);

        if(!$request->customer_id){
            $request->customer_id = Auth::user()->customer_id;
        }
        $group = new Group([
            'name' => $request->name,
            'description' => $request->description,
            'customer_id' => $request->customer_id,
            'parent_id' => $request->parent_id
        ]);

        // Checking the parent_group customer is same as child
        if ($request->parent_id) {
            $parentGroup = Group::find($request->parent_id);
            if (!$parentGroup->customer_id == $group->customer_id) {
                throw new InvalidArgumentException("Parent group should be in same customer");
            }
        }

        $group->owner_id = Auth::user()->id;
        return $group->save();
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


    public function getAllUsers(Group $group)
    {
        // TODO: Implement getAllUsers() method.
    }

    public function getAllDevices(Group $group)
    {
        // TODO: Implement getAllDevices() method.
    }
}
