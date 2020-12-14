<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Http\Requests\DestroyRole;
use App\Http\Requests\IndexRole;
use App\Http\Requests\StoreRole;
use App\Http\Requests\UpdateRole;
use App\Http\Resources\RoleResource as ResourcesRoleResource;
use App\Http\Resources\RoleResourceCollection;
use App\Models\Role;
use App\Models\RoleResource;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(IndexRole $request)
    {
        $perPage = $request->query('perPage') ? (int)$request->query('perPage') : 15;
        $roles = Role::with('owner', 'privileges');
        $groupsPaginate = new RoleResourceCollection($roles->paginate($perPage));
        return $groupsPaginate;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRole $request)
    {
        $newPrivileges = array();
        for ($i = 0; count($request->privileges) > $i; $i++) {
            $p = new RoleResource([
                'view' => $request->privileges[$i]["view"],
                'add' => $request->privileges[$i]["add"],
                'edit' => $request->privileges[$i]["update"],
                'delete' => $request->privileges[$i]["delete"],
                'resource' => $request->privileges[$i]["resource"]
            ]);
            $p->owner_id = Auth::user()->id;
            array_push($newPrivileges, $p);
        }
        $role = new Role([
            'name' => $request->name,
            'description' => $request->description,
            'customer_id' => $request->customer_id,
        ]);
        $role->owner_id = Auth::user()->id;
        $role->save();

        $role->privileges()->saveMany($newPrivileges);

        return  $role->load('privileges');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(IndexRole $request, Role $role)
    {
        $roleResource = new ResourcesRoleResource($role->load('owner', 'customer', 'privileges'));
        return $roleResource;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRole $request, Role $role)
    {
        $role->owner_id = Auth::user()->id;
        $role->update($request->all());
        $newPrivileges = array();
        if ($request->privileges) {
            $role->privileges()->delete();
            for ($i = 0; count($request->privileges) > $i; $i++) {
                $p = new RoleResource([
                    'view' => $request->privileges[$i]["view"],
                    'add' => $request->privileges[$i]["add"],
                    'edit' => $request->privileges[$i]["update"],
                    'delete' => $request->privileges[$i]["delete"],
                    'resource' => $request->privileges[$i]["resource"],
                    'owner_id' => Auth::user()->id
                ]);
                array_push($newPrivileges, $p);
            }
        }
        $role->privileges()->saveMany($newPrivileges);
        return $role->load('privileges');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRole $request, Role $role)
    {
        $role->privileges()->delete();
        $role->delete();
    }
}
