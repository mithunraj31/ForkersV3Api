<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\AuthValidators\AuthValidator;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserResourceCollection;
use App\Enum\AccessType;
use App\Enum\ResourceType;
use App\Http\Requests\IndexUser;
use App\Http\Requests\StoreUser;
use App\Http\Requests\UpdateUser;
use App\Models\DTOs\UserDto;
use App\Models\User;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    private UserServiceInterface $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function show(User $user): UserResource
    {
        return new UserResource($user->load('owner', 'role', 'customer', 'sysRoles', 'userGroups'));
    }

    public function index(IndexUser $request): UserResourceCollection
    {
        $perPage = $request->query('perPage') ? (int)$request->query('perPage') : 15;
        $users = new UserResourceCollection(User::with('owner')->paginate($perPage));
        $users->withQueryString()->links();
        return $users;
    }

    public function store(StoreUser $request)
    {   $user = new UserDto();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->username = $request->username;
        $user->customer_id = $request->customer_id;
        $user->role_id = $request->role_id;
        $user->sys_role = $request->sys_role;
        $user->password = $request->password;
        $user->groups = $request->groups;

        return $this->userService->create($user);

    }
    public function logs(Request $request)
    {
        return Auth::check();
    }
    public function update(UpdateUser $request, User $user)
    {
        $newUser = new UserDto();

        $newUser->first_name = $request->first_name;
        $newUser->last_name = $request->last_name;
        $newUser->groups = $request->groups;
        $newUser->customer_id = $request->customer_id;
        $newUser->role_id = $request->role_id;
        $newUser->password =$request->password;
        $newUser->username =$request->username;


        return $this->userService->update($newUser, $user);
    }
}

