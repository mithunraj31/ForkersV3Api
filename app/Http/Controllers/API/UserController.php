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
        $keycloakUser = (object)null;
        if ($request->first_name) {
            $user->first_name = $request->first_name;
            $keycloakUser->firstName = $request->first_name;
        }
        if ($request->last_name) {
            $user->last_name = $request->last_name;
            $keycloakUser->lastName = $request->last_name;
        }
        if(AuthValidator::isAdmin())
        if ($request->customer_id) $user->customer_id = $request->customer_id;

        if(AuthValidator::isPrivileged(ResourceType::Role,AccessType::Add))
        if ($request->role_id) $user->role_id = $request->role_id;

        if($request->password){
            $keycloakUser->credentials = array(['value'=>$request->password]);
        }
        return $user->updateUser($user,$request);


    }
}

