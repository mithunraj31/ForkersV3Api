<?php

namespace App\Services;

use App\AuthValidators\AuthValidator;
use App\AuthValidators\UserValidator;
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
    }

    public function findById(User $user)
    {
    }

    public function getAll()
    {
    }

    public function delete(User $user)
    {
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
        ])->post(env("KEYCLOAK_HOST") . '/auth/admin/realms/' . env("KEYCLOAK_REALM") . '/users', [
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
        $keycloak = Http::asForm()->post(env("KEYCLOAK_HOST") . '/auth/realms/master/protocol/openid-connect/token', [
            'client_id' => 'admin-cli',
            'grant_type' => 'password',
            'username' => 'admin',
            'password' => 'Test@2020'
        ]);
        return json_decode($keycloak);
    }
}
