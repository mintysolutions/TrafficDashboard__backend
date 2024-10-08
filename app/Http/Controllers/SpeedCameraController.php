<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SpeedCamManagement;
use App\Models\SpeedTraffic;
use Illuminate\Http\Request;

class SpeedCameraController extends Controller
{
    public function listCameras()
    {
        // Retrieve all cameras from the table
        $cameras = SpeedCamManagement::all();

        // Return as JSON response
        return response()->json($cameras, 200);
    }

    public function getTrafficDataByCamera(Request $request)
    {
        // Validate the request to ensure cam_id is provided
        $request->validate([
            'cam_id' => 'required|exists:speed_cam_management,id',
        ]);

        // Find the camera by cam_id
        $camera = SpeedCamManagement::find($request->cam_id);
        $trafficData = SpeedTraffic::where('cam_name', operator: $camera->cam_name)->where('cam_ip', $camera->cam_ip)
            ->get();

        // Return traffic data as JSON response
        return response()->json($trafficData, 200);
    }
}
