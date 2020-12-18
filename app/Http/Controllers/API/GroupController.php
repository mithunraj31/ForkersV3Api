<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\GroupResource;
use App\Http\Resources\GroupResourceCollection;
use App\Models\Group;
use App\Services\Interfaces\GroupServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    private GroupServiceInterface $groupService;

    public function __construct(GroupServiceInterface $groupService)
    {
        $this->groupService = $groupService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return GroupResourceCollection Group
     */
    public function index(Request $request)
    {
        return $this->groupService->getAll($request->query('perPage'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
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
     * @return GroupResource Group
     */
    public function show(Group $group): GroupResource
    {
        return new GroupResource($group->load('owner', 'parent', 'children', 'customer', 'users'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  Group  $group
     * @return Response
     */
    public function update(Request $request, Group $group): Response
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
     * @return Response
     */
    public function destroy(Group $group): Response
    {
        $group->delete();
        return response(['message' => 'Success!'], 200);
    }

    /**
     * Add Users to a single group
     *
     * @param Group $group
     * @param Request $request users: bigInteger or array of bigIntegers
     * @return Response
     */

    public function addUsers(Request $request, Group $group): Response
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
        return response($group->load('users'),200);
    }
}
