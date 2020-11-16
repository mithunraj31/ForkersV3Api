<?php

namespace App\Http\Controllers;

use App\Models\Camera;
use App\Services\Interfaces\CameraServiceInterface;
use Illuminate\Http\Request;

class CameraController extends Controller
{
    private CameraServiceInterface $cameraService;

    public function __construct(CameraServiceInterface $cameraService)
    {
        $this->cameraService = $cameraService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validateVideoData = $request->validate([
            'rotation' => 'required',
            'ch' => 'required',
            'device_id' => 'required|exists:App\Models\Device,device_id',
        ]);
        $camera = new Camera($request->all());

        $this->cameraService->create($camera);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Camera $camera)
    {
        return  $this->cameraService->findById($camera);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Camera $camera)
    {

        $validateVideoData = $request->validate([
            'rotation' => 'required',
            'ch' => 'required',
            'device_id' => 'required|exists:App\Models\Device,device_id',
        ]);
        $camera = new Camera($request->all());
        return  $this->cameraService->update($request, $camera);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Camera $camera)
    {
        return  $this->cameraService->delete($camera);
    }

    public function getCameraByDeviceId($deviceId)
    {
        $cameras = $this->cameraService->findByDeviceId($deviceId);

        return response()->json([
            'data' => $cameras
        ]);
    }
}
