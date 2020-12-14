<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\AuthValidators\GroupValidator;
use App\Http\Resources\GroupResource;
use App\Http\Resources\GroupResourceCollection;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return paginated Group
     */
    public function index(Request $request)
    {   //Authorise
        GroupValidator::view();

        $perPage = $request->query('perPage') ? (int)$request->query('perPage') : 15;
        $groups = Group::with('owner');
        $groupsPaginate = new GroupResourceCollection($groups->paginate($perPage));
        return $groupsPaginate;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Authorization need to be implemented.

        //validating
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable|max:255',
            'parent_id' => 'nullable|exists:App\Group,id',
            'customer_id' => 'required|exists:App\Customer,id',
        ]);
        $group = new Group($validatedData);

        // Checking the parent_group customer is same as child
        if ($request->parent_id) {

            $parentGroup = Group::find($request->parent_id);
            if (!$parentGroup->customer_id == $group->customer_id) {
                return response(['message' => 'Parent id is invalid!'], 400);
            }
        }

        $group->owner_id = Auth::user()->id;
        $group->save();

        return response($group, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Group  $group
     * @return Resource Group
     */
    public function show(Group $group)
    {
        $groupResource = new GroupResource($group->load('owner', 'parent', 'children', 'customer', 'users'));
        return $groupResource;
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Group  $group
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Group $group)
    {
        $request->validate([
            'name' => 'max:255',
            'description' => 'nullable|max:255',
            'parent_id' => 'nullable|exists:App\Group,id',
            'customer_id' => 'exists:App\Customer,id',
        ]);

        // Checking the parent_group customer is same as child
        if ($request->parent_id) {
            // Checking parent and child is same
            if ($request->parent_id == $group->id) {
                return response(['message' => 'Parent id is invalid!'], 400);
            }
            $parentGroup = Group::find($request->parent_id);
            if ($parentGroup->customer_id != $group->customer_id) {
                return response(['message' => 'Parent id is invalid!'], 400);
            }
        }


        $request->owner_id = Auth::user()->id;
        $group->update($request->all());

        return response($group);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Group  $group
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group)
    {
        $group->delete();
        return response(['message' => 'Success!'], 200);
    }

    /**
     * Add Users to a single group
     *
     * @param Group $group
     * @param Request $request users: bigInteger or array of bigIntegers
     * @return \Illuminate\Http\Response
     */

    public function addUsers(Request $request, Group $group)
    {   //Authorization needed to be implemented.
        // validating users are exsists
        $request->validate([
            'users' => ['required', 'exists:App\User,id']
        ]);

        //Add owner id for the relation
        $ownerForRelation = Auth::user()->id;
        $usersArray = (array)$request->users;
        $sync_data = [];
        for ($i = 0; $i < count($usersArray); $i++) {
            $sync_data[$usersArray[$i]] = ['owner_id' => $ownerForRelation];
        }
        // Add users to group
        $group->users()->syncWithoutDetaching($sync_data);
        return $group->load('users');
    }
}
