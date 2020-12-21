<?php


namespace App\Services;


use App\AuthValidators\AuthValidator;
use App\AuthValidators\GroupValidator;
use App\Http\Resources\GroupResource;
use App\Http\Resources\GroupResourceCollection;
use App\Http\Resources\UserResourceCollection;
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
        GroupValidator::updateGroupValidator($request);

        if($request->name){
            $group->name = $request->name;
        }
        if($request->description){
            $group->description = $request->description;
        }
        if($request->customer_id){
            $group->customer_id = $request->customer_id;
        }
        if($request->parent_id){
            $group->parent_id = $request->parent_id;
        }
        // assign relevant customer
        if(!$request->customer_id){
            $request->customer_id = Auth::user()->customer_id;
        }
        // Checking the parent_group customer is same as child
        if ($request->parent_id) {
            $parentGroup = Group::find($request->parent_id);
            if (!$parentGroup->customer_id == $group->customer_id) {
                throw new InvalidArgumentException("Parent group should be in same customer");
            }
        }

        $request->owner_id = Auth::user()->id;
        return $group->save();
    }

    public function findById(Group $group): GroupResource
    {
        GroupValidator::getGroupByIdValidator($group);
        return new GroupResource($group->load('owner', 'children', 'customer'));
    }

    public function getAll($perPage=15): GroupResourceCollection
    {
            $customer = Customer::where('stk_user', AuthValidator::getStkUser())->first();
            return $this->getAllByCustomer($customer);

    }
     public function getAllByCustomer(Customer $customer, $perPage=15): GroupResourceCollection
     {
         $groups = Group::where('customer_id', $customer->id)->where('parent_id', null)->with('owner', 'customer','children');
         return new GroupResourceCollection($groups->paginate($perPage));
     }

    public function delete(Group $group)
    {
        GroupValidator::deleteGroupValidator($group);
        return $group->delete();
    }


    public function getAllUsers(Group $group, $perPage=15)
    {
        return new UserResourceCollection($group->users()->with('customer')->paginate($perPage));
    }

    public function getAllDevices(Group $group)
    {
        // TODO: Implement getAllDevices() method.
    }

    public function addUsers($users, Group $group): bool
    {
        GroupValidator::addUserValidation($group);

        //Add owner id for the relation
        $ownerForRelation = Auth::user()->id;
        $usersArray = (array)$users;
        $sync_data = [];
        for ($i = 0; $i < count($usersArray); $i++) {
            $sync_data[$usersArray[$i]] = ['owner_id' => $ownerForRelation];
        }
        // Add users to group
        $group->users()->syncWithoutDetaching($sync_data);
        return true;
    }
}
