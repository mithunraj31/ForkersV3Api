<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'customer_id',
        'role_id',
        'owner_id',
        'sys_role'
    ];
    public function owner()
    {
        return $this->belongsTo('App\Models\User', 'owner_id');
    }

    public function role()
    {
        return $this->belongsTo('App\Models\Role', 'role_id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id');
    }

    public function userGroups()
    {
        return $this->hasMany('App\Models\UserGroup');
    }
    public function groups()
    {
        return $this->belongsToMany('App\Models\Group', 'user_group', 'user_id', 'group_id')->withPivot('owner_id');;
    }
    static function createUser($validatedUser)
    {

    }

    public function updateUser(User $user, $keycloakUser)
    {
        $user->save();

        if ($keycloakUser->groups) {
            //Add owner id for the relation
            $ownerForRelation = Auth::user()->id;
            $usersArray = (array)$keycloakUser->groups;
            $sync_data = [];
            for ($i = 0; $i < count($usersArray); $i++) {
                $sync_data[$usersArray[$i]] = ['owner_id' => $ownerForRelation];
            }

            $user->groups()->sync($sync_data);
        }
        //login ass admin
        $keycloak = Http::asForm()->post(env("KEYCLOAK_HOST") . '/auth/realms/master/protocol/openid-connect/token', [
            'client_id' => 'admin-cli',
            'grant_type' => 'password',
            'username' => 'admin',
            'password' => 'Test@2020'
        ]);
        $keycloak = json_decode($keycloak);

        //get user in Keycloak
        $getUser = Http::withHeaders([
            'Authorization' => 'Bearer ' . $keycloak->access_token
        ])->get(env("KEYCLOAK_HOST") . '/auth/admin/realms/' . env("KEYCLOAK_REALM") . '/users?username=' . $user->username);
        // if($createdResponse->status()!=201) throw new BadRequestException([$createdResponse->body()]);

        $getUserId = $getUser[0]["id"];
        $groups = json_encode($keycloakUser->groups);
        if (!$keycloakUser->groups) $groups = $getUser[0]["attributes"]["groups"];

        $privileges = json_encode($keycloakUser->privileges);
        if (!$keycloakUser->privileges) $privileges = $getUser[0]["attributes"]["privileges"];
        $updateResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $keycloak->access_token
        ])->put(env("KEYCLOAK_HOST") . '/auth/admin/realms/' . env("KEYCLOAK_REALM") . '/users/' . $getUserId, [
            'firstName' => $keycloakUser->first_name,
            'lastName' => $keycloakUser->last_name,
            'credentials' => array(['value' => $keycloakUser->password]),
            'attributes' => [
                'privileges' => $privileges,
                'groups' => $groups
            ]
        ]);
        return $user->load('groups');
    }
}
