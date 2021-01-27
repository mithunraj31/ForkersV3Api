<?php

namespace App\Http\Controllers\API;

use App\AuthValidators\AuthValidator;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteManufacturer;
use App\Http\Requests\IndexManufacturer;
use App\Http\Requests\StoreManufacturer;
use App\Http\Requests\UpdateManufacturer;
use App\Http\Resources\ManufacturerResourceCollection;
use App\Models\DTOs\ManufacturerDto;
use App\Services\Interfaces\ManufacturerServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class ManufacturerController extends Controller
{
    private ManufacturerServiceInterface $manufacturerService;

    public function __construct(ManufacturerServiceInterface $manufacturerService)
    {
        $this->manufacturerService = $manufacturerService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(IndexManufacturer $request)
    {

        $model = new ManufacturerDto;
        $model->page = $request->query('page') ? $request->query('page') : 1;
        $model->perPage = $request->query('perPage') ? $request->query('perPage') : 15;
        $data = $this->manufacturerService->getAll($model);
        return new ManufacturerResourceCollection($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreManufacturer $request)
    {
        $model = new ManufacturerDto;
        $model->name = $request->name;
        $model->description = $request->description;

        $model->ownerId = Auth::user()->id;

        $this->manufacturerService->create($model);

        return response()->json([], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(IndexManufacturer $request, $id)
    {
        $data = $this->manufacturerService->findById($id);
        return response()->json(['data' => $data], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateManufacturer $request, $id)
    {

        $model = new ManufacturerDto;
        $model->id = $id;
        $model->name = $request->name;
        $model->description = $request->description;
        $model->ownerId = Auth::user()->id;

        $this->manufacturerService->update($model);

        return response()->json([], 204);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteManufacturer $request, $id)
    {
        $manufacturer = $this->manufacturerService->findById($id);
        $this->manufacturerService->delete($id);
        return response()->json([], 204);
    }
}
