<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddUsersToGroup;
use App\Http\Requests\IndexGroup;
use App\Http\Requests\IndexUser;
use App\Http\Requests\IndexVehicle;
use App\Http\Requests\StoreGroup;
use App\Http\Requests\UpdateGroup;
use App\Http\Resources\GroupResource;
use App\Http\Resources\GroupResourceCollection;
use App\Http\Resources\UserResourceCollection;
use App\Models\DTOs\GroupDto;
use App\Models\Group;
use App\Services\Interfaces\GroupServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
     * @param IndexGroup $request
     * @return GroupResourceCollection Group
     */
    public function index(IndexGroup $request): GroupResourceCollection
    {
        return $this->groupService->getAll($request->query('perPage'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreGroup $request
     * @return Response
     */
    public function store(StoreGroup $request)
    {
        $group = new GroupDto();
        $group->parent_id = $request->parent_id;
        $group->customer_id = $request->customer_id;
        $group->description = $request->description;
        $group->name = $request->name;

        return response($this->groupService->create($group), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Group  $group
     * @return GroupResource Group
     */
    public function show(Group $group): GroupResource
    {
        return $this->groupService->findById($group);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param UpdateGroup $request
     * @param Group $group
     * @return Response
     */
    public function update(UpdateGroup $request, Group $group): Response
    {
        $groupRequest = new GroupDto();
        $groupRequest->name = $request->name;
        $groupRequest->description = $request->description;
        $groupRequest->customer_id = $request->customer_id;
        $groupRequest->parent_id = $request->parent_id;

        return response($this->groupService->update($groupRequest, $group), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Group  $group
     * @return Response
     */
    public function destroy(Group $group): Response
    {

        return response($this->groupService->delete($group),204);
    }

    /**
     * Add Users to a single group
     *
     * @param AddUsersToGroup $request users: bigInteger or array of bigIntegers
     * @param Group $group
     * @return Response
     */

    public function addUsers(AddUsersToGroup $request, Group $group): Response
    {

        return response($this->groupService->addUsers($request->users,$group));
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexUser $request
     * @param Group $group
     * @return UserResourceCollection Group
     */
    public function getUsers(IndexUser $request, Group $group): UserResourceCollection
    {
        return $this->groupService->getAllUsers($group, $request->query('perPage'));
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexVehicle $request
     * @param Group $group
     * @return GroupResourceCollection Group
     */
    public function getVehicles(IndexVehicle $request, Group $group): GroupResourceCollection
    {
        return $this->groupService->getAllVehicles($group, $request->query('perPage'));
    }
}
