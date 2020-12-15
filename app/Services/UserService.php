<?php

namespace App\Services;

use App\AuthValidators\AuthValidator;
use App\AuthValidators\UserValidator;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserResourceCollection;
use App\Models\Customer;
use App\Models\DTOs\UserDto;
use App\Models\Role;
use App\Models\User;
use App\Services\Interfaces\UserServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class UserService implements UserServiceInterface
{
    public function create(UserDto $validatedUser)
    {
        // validate user
        UserValidator::storeUserValidator($validatedUser);
        // get correct customer and stk_user
        if (!$validatedUser->customer_id) {
            $validatedUser->customer_id = Auth::user()->customer_id;
            $validatedUser->stk_user = AuthValidator::getStkUser();
        } else {
            $customer = Customer::findOrFail($validatedUser->customer_id);
            $validatedUser->stk_user = $customer->stk_user;
        }
        // generate privileges
        $validatedUser->privileges = $this->generatePrivileges($validatedUser->role_id);

        // check request
        // create keycloak user
        $this->createKeycloakUser($validatedUser);

        //create user in database
        $user = new User([
            'first_name' => $validatedUser->first_name,
            'last_name' => $validatedUser->last_name,
            'username' => $validatedUser->username,
            'customer_id' => $validatedUser->customer_id,
            'owner_id' => Auth::user()->id,
            'role_id' => $validatedUser->role_id,
            'sys_role' => $validatedUser->sys_role
        ]);
        $user->save();

        //Add owner id for the relation
        $ownerForRelation = Auth::user()->id;
        $usersArray = (array)$validatedUser->groups;
        $sync_data = [];
        for ($i = 0; $i < count($usersArray); $i++) {
            $sync_data[$usersArray[$i]] = ['owner_id' => $ownerForRelation];
        }

        //add groups to user
        $user->groups()->syncWithoutDetaching($sync_data);
        return $user->load('groups');
    }

    public function update(UserDto $request, User $user)
    {
        UserValidator::updateUserValidator($request);

        if ($request->role_id) {
            $request->privileges = $this->generatePrivileges($user->role_id);
        }
        if ($request->customer_id) {
            $customer = Customer::findOrFail($request->customer_id);
            $request->stk_user = $customer->stk_user;
        }
        // update keyclaok
        $this->updateKeyCloakUser($request, $user->username);

        // update Forkers db

        if ($request->first_name) {
            $user->first_name = $request->first_name;
        }
        if ($request->last_name) {
            $user->last_name = $request->last_name;
        }
        if ($request->role_id) {
            $user->role_id = $request->role_id;
        }
        if ($request->customer_id) {
            $user->customer_id = $request->customer_id;
        }
        if ($request->sys_role) {
            $user->sys_role = $request->sys_role;
        }
        if ($request->username) {
            $user->username = $request->username;
        }


        if ($user->groups) {
            //Add owner id for the relation
            $ownerForRelation = Auth::user()->id;
            $usersArray = (array)$request->groups;
            $sync_data = [];
            for ($i = 0; $i < count($usersArray); $i++) {
                $sync_data[$usersArray[$i]] = ['owner_id' => $ownerForRelation];
            }

            //add groups to user
            $user->groups()->sync($sync_data);
        }
        $user->save();
        return $user->load('groups');
    }

    public function findById(User $user)
    {
        $userWithFieds = $user->load('owner', 'role', 'customer', 'userGroups');
        UserValidator::getUserByIdValidator($userWithFieds);
        return new UserResource($userWithFieds);
    }

    public function getAll($perPage=15)
    {
        if(AuthValidator::isAdmin()){
            $users = new UserResourceCollection(User::with('owner')->paginate($perPage));
            $users->withQueryString()->links();
            return $users;
        }else{
            $customer = Customer::where('stk_user',AuthValidator::getStkUser())->first();
            $users = new UserResourceCollection(User::where('customer_id',$customer->id)->with('owner')->paginate($perPage));
            $users->withQueryString()->links();
            return $users;
        }
    }

    public function delete(User $user)
    {
        UserValidator::deleteUserValidator($user);
        return $user->delete();
    }
    public function generatePrivileges($role_id)
    {
        $role = Role::with('privileges')->findOrFail($role_id);
        $privileges = $role->privileges;
        $privilegeArray = [];
        foreach ($privileges as $p) {
            if ($p->add) {
                $string = $p->resource . ':add';
                array_push($privilegeArray, $string);
            }
            if ($p->edit) {
                $string = $p->resource . ':edit';
                array_push($privilegeArray, $string);
            }
            if ($p->view) {
                $string = $p->resource . ':view';
                array_push($privilegeArray, $string);
            }
            if ($p->delete) {
                $string = $p->resource . ':delete';
                array_push($privilegeArray, $string);
            }
        }
        return $privilegeArray;
    }
    private function createKeycloakUser(UserDto $validatedUser)
    {
        $keycloak = $this->getKeyclaokToken();
        //create user in Keycloak
        $createdResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $keycloak->access_token
        ])->post(config("keycloak.host") . '/auth/admin/realms/' . config("keycloak.realm") . '/users', [
            'firstName' => $validatedUser->first_name,
            'lastName' => $validatedUser->last_name,
            'email' => $validatedUser->username,
            'username' => $validatedUser->username,
            'enabled' => true,
            'credentials' => array(['value' => $validatedUser->password]),
            'attributes' => [
                'privileges' => json_encode($validatedUser->privileges),
                'groups' => json_encode($validatedUser->groups),
                'sys_role' => $validatedUser->sys_role,
                'stk_user' => $validatedUser->stk_user
            ]
        ]);
        // return $createdResponse;
        if ($createdResponse->status() != 201) throw new InvalidArgumentException($createdResponse->body());
    }
    private function getKeyclaokToken()
    {
        //login as admin
        $keycloak = Http::asForm()->post(config("keycloak.host") . '/auth/realms/master/protocol/openid-connect/token', [
            'client_id' => 'admin-cli',
            'grant_type' => 'password',
            'username' => config("keycloak.username"),
            'password' => config("keycloak.password")
        ]);
        return json_decode($keycloak);
    }

    private function updateKeyCloakUser(UserDto $user, $username)
    {
        //login ass admin

        $keycloak = $this->getKeyclaokToken();

        //get user in Keycloak
        $getUser = Http::withHeaders([
            'Authorization' => 'Bearer ' . $keycloak->access_token
        ])->get(config("keycloak.host") . '/auth/admin/realms/' . config("keycloak.realm") . '/users?username=' . $username);
        if($getUser->status()!=200) throw new BadRequestException([$getUser->body()]);

        $getUserId = $getUser[0]["id"];

        $newUser = [];
        if ($user->first_name) {
            $newUser['firstName'] = $user->first_name;
        }
        if ($user->last_name) {
            $newUser['lastName'] = $user->last_name;
        }
        if ($user->groups) {
            $newUser['attributes']['groups'] = json_encode($user->groups);
        }
        if ($user->role_id) {
            $newUser['attributes']['privileges'] = json_encode($user->privileges);
        }
        if ($user->customer_id) {
            $newUser['attributes']['stk_user'] = $user->stk_user;
        }
        if ($user->sys_role) {
            $newUser['attributes']['sys_role'] = $user->sys_role;
        }
        if ($user->password) {
            $newUser['credentials'] = array(['value' => $user->password]);
        }
        if ($user->username) {
            $newUser['username'] = $user->username;
        }
        if ($user->username) {
            $newUser['email'] = $user->username;
        }

        $updateResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $keycloak->access_token
        ])->put(config("keycloak.host") . '/auth/admin/realms/' . config("keycloak.realm") . '/users/' . $getUserId, $newUser);
        if ($updateResponse->status() != 204) throw new InvalidArgumentException($updateResponse->body());
    }
}
