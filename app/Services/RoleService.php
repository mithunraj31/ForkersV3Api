<?php

namespace App\Services;

use App\AuthValidators\AuthValidator;
use App\AuthValidators\RoleValidator;
use App\Http\Resources\RoleResource;
use App\Http\Resources\RoleResourceCollection;
use App\Models\Customer;
use App\Models\RoleResource as RoleResourceModel;
use App\Models\DTOs\RoleDto;
use App\Models\Role;
use App\Services\Interfaces\RoleServiceInterface;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Support\Facades\Auth;

class RoleService extends ServiceBase implements RoleServiceInterface
{
    private UserServiceInterface $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function create(RoleDto $request): Role
    {
        RoleValidator::storeRoleValidator($request);
        $newPrivileges = $this->makeNewRoleResources($request->privileges);
        if(!$request->customer_id){
            $request->customer_id = Customer::where('stk_user',Auth::user()->stk_user)->first();
        }
        $role = new Role([
            'name' => $request->name,
            'description' => $request->description,
            'customer_id' => $request->customer_id,
        ]);
        $role->owner_id = Auth::user()->id;
        $role->save();

        $role->privileges()->saveMany($newPrivileges);

        return $role->load('privileges');
    }

    public function update(RoleDto $request, Role $role): Role
    {

        RoleValidator::updateRoleValidator($request);

        $role->owner_id = Auth::user()->id;

        if ($request->name) {
            $role->name = $request->name;
        }
        if ($request->description) {
            $role->description = $request->description;
        }
        if ($request->customer_id) {
            $role->customer_id = $request->customer_id;
        }
        $role->save();

        if ($request->privileges) {
            $role->Privileges()->delete();
            $newPrivileges = $this->makeNewRoleResources($request->privileges);
            $role->privileges()->saveMany($newPrivileges);
            // update relevant users
            $currentUsers = $role->users;
            $this->userService->updateKeycloakPrivileges($currentUsers, $role->id);
        }
        return $role->load('privileges');
    }

    public function findById(Role $role): RoleResource
    {
        RoleValidator::getRoleByIdValidator($role);
        return new RoleResource($role->load('owner', 'customer', 'privileges'));
    }

    public function getAll($perPage = 15): RoleResourceCollection
    {
        if (AuthValidator::isAdmin()) {
            $roles = Role::with('owner', 'privileges', 'customer');
            return new RoleResourceCollection($roles->paginate($perPage));
        } else {
            $customer = Customer::where('stk_user', AuthValidator::getStkUser())->first();
            $roles = Role::where('customer_id', $customer->id)->with('owner', 'privileges');
            return new RoleResourceCollection($roles->paginate($perPage));
        }
    }

    public function delete(Role $role): ?bool
    {
        RoleValidator::deleteRoleValidator($role);
        return $role->delete();
    }

    private function makeNewRoleResources($privileges): array
    {
        $newPrivileges = array();
        for ($i = 0; count($privileges) > $i; $i++) {
            $p = new RoleResourceModel([
                'view' => $privileges[$i]["view"],
                'add' => $privileges[$i]["add"],
                'edit' => $privileges[$i]["edit"],
                'delete' => $privileges[$i]["delete"],
                'resource' => $privileges[$i]["resource"],
                'owner_id' => Auth::user()->id
            ]);
            array_push($newPrivileges, $p);
        }
        return $newPrivileges;
    }

}
