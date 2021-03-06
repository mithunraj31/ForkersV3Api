<?php

namespace App\Services\Interfaces;

use App\Models\DTOs\UserDto;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserServiceInterface
{
    public function create(UserDto $model);

    public function update(UserDto $request,User $user);

    public function findById(User $user);

    public function getAll();

    public function delete(User $user);

    public function generatePrivileges($role_id);
    public function updateKeycloakPrivileges(Collection $users, $role_id);
}
