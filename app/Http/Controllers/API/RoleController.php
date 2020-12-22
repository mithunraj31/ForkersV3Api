<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteRole;
use App\Http\Requests\IndexRole;
use App\Http\Requests\StoreRole;
use App\Http\Requests\UpdateRole;
use App\Http\Resources\RoleResource as ResourcesRoleResource;
use App\Http\Resources\RoleResourceCollection;
use App\Models\DTOs\RoleDto;
use App\Models\Role;
use App\Models\RoleResource;
use App\Services\Interfaces\RoleServiceInterface;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{

    private RoleServiceInterface $roleService;

    public function __construct(RoleServiceInterface $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(IndexRole $request)
    {
        return $this->roleService->getAll($request->query('perPage'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRole $request)
    {
        $role = new RoleDto();
        $role->name = $request->name;
        $role->description = $request->description;
        $role->customer_id = $request->customer_id;
        $role->privileges = $request->privileges;

        return  $this->roleService->create($role);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(IndexRole $request, Role $role)
    {
        return $this->roleService->findById($role);
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
        $validatedRole = new RoleDto();
        $validatedRole->name = $request->name;
        $validatedRole->description = $request->description;
        $validatedRole->customer_id = $request->customer_id;
        $validatedRole->privileges = $request->privileges;
        return $this->roleService->update($validatedRole, $role);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(DeleteRole $request, Role $role)
    {
        return $this->roleService->delete($role);
    }
}
