<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DTOs\CameraDto;
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
        $validateCameraData = $request->validate([
            'rotation' => 'required',
            'ch' => 'required',
            'device_id' => 'required|exists:App\Models\Device,device_id',
        ]);
        $camera = new CameraDto();
        $camera->deviceId = $validateCameraData['device_id'];
        $camera->ch = $validateCameraData['ch'];
        $camera->rotation = $validateCameraData['rotation'];

        $this->cameraService->create($camera);
        return response(['message' => 'Success!'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($cameraId)
    {
        $camera = $this->cameraService->findById($cameraId);
        return response([
            'data' => $camera
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $cameraId)
    {

        $validateCameraData = $request->validate([
            'rotation' => 'required',
            'ch' => 'required',
            'device_id' => 'required|exists:App\Models\Device,device_id',
        ]);
        $camera = new CameraDto();
        $camera->id = $cameraId;
        $camera->deviceId = $validateCameraData['device_id'];
        $camera->ch = $validateCameraData['ch'];
        $camera->rotation = $validateCameraData['rotation'];
        $this->cameraService->update($camera);
        return response(['message' => 'Success!'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($cameraId)
    {
        $this->cameraService->delete($cameraId);
        return response(['message' => 'Success!'], 200);
    }

    public function getCameraByDeviceId($deviceId)
    {
        $cameras = $this->cameraService->findByDeviceId($deviceId);

        return response()->json([
            'data' => $cameras
        ], 200);
    }
}
