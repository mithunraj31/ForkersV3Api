<?php

namespace App\Services\Interfaces;

use App\Models\DTOs\RoleDto;
use App\Models\Role;

interface RoleServiceInterface
{
    public function create(RoleDto $request);

    public function update(RoleDto $request,Role $role);

    public function findById(Role $role);

    public function getAll();

    public function delete(Role $role);

}
