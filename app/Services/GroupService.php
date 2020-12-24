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

    public function getAll()
    {
        if(AuthValidator::isAdmin()) {
            $customer = Customer::where('stk_user', AuthValidator::getStkUser())->first();
            return $this->getAllByCustomer($customer);
        }else{
            $availableGroups = AuthValidator::getGroups();
            $groups = Group::whereIn('id',$availableGroups)->get();
            $groupTree = $this->generateGroupTree($groups);
            return new GroupResourceCollection($groupTree);
        }

    }
     public function getAllByCustomer(Customer $customer): GroupResourceCollection
     {
         $groups = Group::where('customer_id', $customer->id)->where('parent_id', null)->with('owner', 'customer','children');
         return new GroupResourceCollection($groups->get());
     }

    public function delete(Group $group)
    {
        GroupValidator::deleteGroupValidator($group);
        $children = $group->children;
        if($children && count($children)>0){
            throw new InvalidArgumentException("Cannot delete when child groups are available");
        }
        $users = $group->users;
        if($users && count($users)>0){
            throw new InvalidArgumentException("Cannot delete when group has users");
        }
        return $group->delete();
    }


    public function getAllUsers(Group $group, $perPage=15)
    {
        GroupValidator::getAllByGroup($group);
        return new UserResourceCollection($group->users()->with('customer')->paginate($perPage));
    }

    public function getAllVehicles(Group $group, $perPage=15)
    {
        GroupValidator::getAllByGroup($group);
        $paginator= $group->vehicles()->paginate($perPage);
        $paginator->getCollection()->transform(function($value){
           return [
               'id' =>$value->id,
               'name' =>$value->name,
               'owner_id'=> $value->owner_id,
               'group_id'=> $value->group_id,
               'customer_id'=> $value->customer_id,
               'created_at'=>$value->created_at,
               'updated_at'=>$value->updated_at,
               'device' =>$value->device?$value->device->device:null,

           ];
        });
        return new GroupResourceCollection($paginator);
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

    public function getAllDevices(Group $group, $perPage=15)
    {
        // TODO: Implement getAllDevices() method.
    }

    private function generateGroupTree($groups)
    {
        $groupArray = $groups->toArray();
        $children = [];
        $parents = [];

        foreach ($groupArray as &$item) $children[$item['parent_id']][] = &$item;
        unset($item);

        foreach ($groupArray as &$item) if (isset($children[$item['id']]))
            $item['children'] = $children[$item['id']];

        foreach ($children as $key => $child){
            if($key!=""){
                $keyCount = $this->getKeyCount($parents,$key);
                print($keyCount);
                if($keyCount==0){
                    $parents = array_merge($parents,$child);
                }
            }else{
                $parents = array_merge($parents,$child);
            }
        }
        return collect($parents);
    }

    private function getKeyCount(array $main, int $key): int
    {
        $count =0;
        foreach($main as $sub){
            if($sub['id']==$key){
                $count++;
            }
            if(array_key_exists('children',$sub)){
                $count += $this->getKeyCount($sub['children'],$key);
            }
        }
        return $count;
    }
}
