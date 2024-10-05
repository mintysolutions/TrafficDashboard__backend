<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CountCamManagement;
use App\Models\CameraTraffic;
use Illuminate\Support\Facades\DB;

class CountCameraController extends Controller
{
    // List all cameras from count_cam_management table
    public function listCameras()
    {
        // Retrieve all cameras from the table
        $cameras = CountCamManagement::all();

        // Return as JSON response
        return response()->json($cameras, 200);
    }

    // List traffic data for a selected camera
    public function getTrafficDataByCamera(Request $request)
    {
        // Validate the request to ensure cam_id is provided
        $request->validate([
            'cam_id' => 'required|exists:count_cam_management,id',
        ]);

        // Find the camera by cam_id
        $camera = CountCamManagement::find($request->cam_id);
        // Retrieve traffic data by filtering cam_name and cam_ip
        $trafficData = CameraTraffic::where('cam_name', $camera->cam_name)->where('cam_ip', $camera->cam_ip)
            ->where('cam_ip', $camera->cam_ip)
            ->get();

        // Return traffic data as JSON response
        return response()->json($trafficData, 200);
    }

    // Fetch stats for the selected camera
    public function getCameraStats($id, Request $request)
    {
        // Validate if the camera exists
        $camera = CountCamManagement::find($id);
        if (!$camera) {
            return response()->json(['message' => 'Camera not found'], 404);
        }

        // Get optional start and end time from the request
        $startTime = $request->query('start');
        $endTime = $request->query('end');

        // Query for traffic data filtered by camera's cam_name and cam_ip
        $query = CameraTraffic::where('cam_name', $camera->cam_name)
            ->where('cam_ip', $camera->cam_ip);

        // Apply date range filter if provided
        if ($startTime) {
            $query->where('time', '>=', $startTime);
        }
        if ($endTime) {
            $query->where('time', '<=', $endTime);
        }

        // Sum the relevant fields
        $stats = $query->select(
            DB::raw('SUM(car_count) as total_cars'),
            DB::raw('SUM(bus_count) as total_buses'),
            DB::raw('SUM(truck_count) as total_trucks'),
            DB::raw('SUM(human_count) as total_humans'),
            DB::raw('SUM(bike_count) as total_bikes'),
            DB::raw('SUM(total_count) as total_objects')
        )->first();

        // Return the summarized statistics as a JSON response
        return response()->json([
            'camera' => $camera->cam_name,
            'total_cars' => $stats->total_cars ?? 0,
            'total_buses' => $stats->total_buses ?? 0,
            'total_trucks' => $stats->total_trucks ?? 0,
            'total_humans' => $stats->total_humans ?? 0,
            'total_bikes' => $stats->total_bikes ?? 0,
            'total_objects' => $stats->total_objects ?? 0,
        ], 200);
    }


    public function getAllCameraStats(Request $request)
    {
        // Get optional start and end time from the request
        $startTime = $request->query('start');
        $endTime = $request->query('end');
        $cameras = CountCamManagement::all();
        $allCameraStats = [];
        foreach ($cameras as $camera) {
            $query = CameraTraffic::where('cam_ip', $camera->cam_ip);
            if ($startTime) {
                $query->where('time', '>=', $startTime);
            }
            if ($endTime) {
                $query->where('time', '<=', $endTime);
            }
            $stats = $query->select(
                DB::raw('SUM(car_count) as total_cars'),
                DB::raw('SUM(bus_count) as total_buses'),
                DB::raw('SUM(truck_count) as total_trucks'),
                DB::raw('SUM(human_count) as total_humans'),
                DB::raw('SUM(bike_count) as total_bikes'),
                DB::raw('SUM(total_count) as total_objects')
            )->first();
            $allCameraStats[] = [
                'camera_name' => $camera->cam_name,
                'camera_ip' => $camera->cam_ip,
                'total_cars' => $stats->total_cars ?? 0,
                'total_buses' => $stats->total_buses ?? 0,
                'total_trucks' => $stats->total_trucks ?? 0,
                'total_humans' => $stats->total_humans ?? 0,
                'total_bikes' => $stats->total_bikes ?? 0,
                'total_objects' => $stats->total_objects ?? 0,
            ];
        }

        // Return the summarized statistics as a JSON response
        return response()->json($allCameraStats, 200);
    }

    public function getPeakHourAndCameraStats(Request $request)
    {
        // Fetch all unique scenarios
        $scenarios = CameraTraffic::select('scenario_name')
            ->distinct()
            ->pluck('scenario_name');

        // Prepare the array to store peak hours data
        $peakHoursData = [];

        // For each scenario, calculate the hour with the highest total_objects
        foreach ($scenarios as $scenario) {

            $scenarioPeakTraffic = CameraTraffic::where('scenario_name', $scenario)
                ->orderBy('total_count', 'desc') // Order by total_objects to get the peak hour
                ->first(); // Get the record with the highest total_objects


            // Find the traffic record with the maximum total_count for each scenario
            // $scenarioTraffic = CameraTraffic::where('scenario_name', $scenario)
            //     ->select(
            //         'time',  // We need the time field for the datetime
            //         DB::raw('HOUR(time) as hour'),
            //         DB::raw('SUM(total_count) as total_objects')
            //     )
            //     ->groupBy(DB::raw('HOUR(time)'))
            //     ->orderBy('total_objects', 'desc')  // Order by total_objects to get the peak hour
            //     ->get();

            $peakHoursData[] = [
                'scenario' => $scenario,
                'total_count' => $scenarioPeakTraffic->total_count,
                'datetime' => $scenarioPeakTraffic->time, // Include the time field as datetime
            ];
        }

        // Total objects grouped by camera (cam_ip)
        $cameraObjects = CameraTraffic::select(
            'cam_ip',
            DB::raw('SUM(total_count) as total_objects')
        )
            ->groupBy('cam_ip')
            ->get();

        // Prepare camera objects data in the format [{"cam_ip", "total_objects"}]
        $formattedCameraObjects = $cameraObjects->map(function ($data) {
            return [
                'cam_ip' => $data->cam_ip,
                'total_objects' => $data->total_objects,
            ];
        });

        // Return the peak hour data and total objects for each camera
        return response()->json([
            'peak_hours' => $peakHoursData,
            'cameras' => $formattedCameraObjects
        ], 200);
    }

}
