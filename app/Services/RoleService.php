
<?php

namespace App\Services;

use App\AuthValidators\AuthValidator;
use App\AuthValidators\RoleValidator;
use App\Http\Resources\RoleResource;
use App\Http\Resources\RoleResourceCollection;
use App\Models\Customer;
use App\Models\DTOs\RoleDto;
use App\Models\Role;
use App\Services\Interfaces\RoleServiceInterface;
use Illuminate\Support\Facades\Auth;

class RoleService extends ServiceBase implements RoleServiceInterface
{
    public function create(RoleDto $validatedRole)
    {
        $newPrivileges = array();
        for ($i = 0; count($validatedRole->privileges) > $i; $i++) {
            $p = new RoleResource([
                'view' => $validatedRole->privileges[$i]["view"],
                'add' => $validatedRole->privileges[$i]["add"],
                'edit' => $validatedRole->privileges[$i]["edit"],
                'delete' => $validatedRole->privileges[$i]["delete"],
                'resource' => $validatedRole->privileges[$i]["resource"]
            ]);
            $p->owner_id = Auth::user()->id;
            array_push($newPrivileges, $p);
        }
        $role = new Role([
            'name' => $validatedRole->name,
            'description' => $validatedRole->description,
            'customer_id' => $validatedRole->customer_id,
        ]);
        $role->owner_id = Auth::user()->id;
        $role->save();

        $role->privileges()->saveMany($newPrivileges);

        return  $role->load('privileges');
    }

    public function update(RoleDto $request, Role $role)
    {

        RoleValidator::updateRoleValidator($request);

        $role->owner_id = Auth::user()->id;

        if($request->name){
            $role->name = $request->name;
        }
        if($request->description){
            $role->description = $request->description;
        }
        if($request->customer_id){
            $role->descripton = $request->customer_id;
        }
        $role->save();

        $newPrivileges = array();
        if ($request->privileges) {
            $role->privileges()->delete();
            for ($i = 0; count($request->privileges) > $i; $i++) {
                $p = new RoleResource([
                    'view' => $request->privileges[$i]["view"],
                    'add' => $request->privileges[$i]["add"],
                    'edit' => $request->privileges[$i]["edit"],
                    'delete' => $request->privileges[$i]["delete"],
                    'resource' => $request->privileges[$i]["resource"],

                ]);
                $p->owner_id =Auth::user()->id;
                array_push($newPrivileges, $p);
            }
        }
        $role->privileges()->saveMany($newPrivileges);
        return $role->load('privileges');
    }

    public function findById(Role $role)
    {
        RoleValidator::getRoleByIdValidator($role);
        $roleResource = new RoleResource($role->load('owner', 'customer', 'privileges'));
        return $roleResource;
    }

    public function getAll($perPage = 15)
    {
        if (AuthValidator::isAdmin()) {
            $roles = Role::with('owner', 'privileges','customer');
            $rolesPaginate = new RoleResourceCollection($roles->paginate($perPage));
            return $rolesPaginate;
        } else {
            $customer = Customer::where('stk_user', AuthValidator::getStkUser())->first();
            $roles = Role::where('customer_id', $customer->id)->with('owner', 'privileges');
            $rolesPaginate = new RoleResourceCollection($roles->paginate($perPage));
            return $rolesPaginate;
        }
    }

    public function delete(Role $role)
    {
        RoleValidator::deleteRoleValidator($role);
        return $role->delete();
    }
}
