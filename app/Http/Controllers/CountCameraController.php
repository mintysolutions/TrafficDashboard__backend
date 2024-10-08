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
    public function getCameraStats(Request $request, $cameraId)
    {
        // Validate that the camera exists
        $camera = CountCamManagement::find($cameraId);
        if (!$camera) {
            return response()->json(['error' => 'Camera not found'], 404);
        }

        // Get optional start and end time from the request
        $startTime = $request->query('start');
        $endTime = $request->query('end');

        // Initialize query for camera traffic data filtering by camera name, IP, and optional date range
        $query = CameraTraffic::where('cam_name', $camera->cam_name)
            ->where('cam_ip', operator: $camera->cam_ip);

        // Apply date range filter if start and end times are provided
        if ($startTime && $endTime) {
            $query->whereBetween('time', [$startTime, $endTime]);
        }

        // Sum of all counts (car, bus, truck, human, bike, total) for the selected camera and date range
        $totals = $query->select(
            DB::raw('SUM(car_count) as total_cars'),
            DB::raw('SUM(bus_count) as total_buses'),
            DB::raw('SUM(truck_count) as total_trucks'),
            DB::raw('SUM(human_count) as total_humans'),
            DB::raw('SUM(bike_count) as total_bikes'),
            DB::raw('SUM(total_count) as total_objects')
        )->first();

        // Get the peak record (max total_count) for each scenario in the selected camera and date range
        $scenarioPeaks = CameraTraffic::where('cam_name', $camera->cam_name)
            ->where('cam_ip', $camera->cam_ip)
            ->select('scenario_name', DB::raw('MAX(total_count) as peak_count'))
            ->when($startTime && $endTime, function ($q) use ($startTime, $endTime) {
                $q->whereBetween('time', [$startTime, $endTime]);
            })
            ->groupBy('scenario_name')
            ->get();

        // Find the corresponding times for peak counts for each scenario
        $scenarioPeakTimes = CameraTraffic::where('cam_name', $camera->cam_name)
            ->where('cam_ip', $camera->cam_ip)
            ->when($startTime && $endTime, function ($q) use ($startTime, $endTime) {
                $q->whereBetween('time', [$startTime, $endTime]);
            })
            ->whereIn('scenario_name', $scenarioPeaks->pluck('scenario_name'))
            ->get();

        // Map the scenario peaks to include the time for the peak count
        $formattedScenarioPeaks = $scenarioPeaks->map(function ($peak) use ($scenarioPeakTimes) {
            $peakTime = $scenarioPeakTimes->where('scenario_name', $peak->scenario_name)
                ->where('total_count', $peak->peak_count)
                ->first();

            return [
                'scenario' => $peak->scenario_name,
                'time' => $peakTime->time ?? null,
                'count' => $peak->peak_count
            ];
        });

        // Find the global peak (max count from all scenario peaks)
        $totalPeak = $formattedScenarioPeaks->sortByDesc('count')->first();

        // Analyze hourly peaks for each scenario
        $timePeaks = [];
        $scenarios = CameraTraffic::distinct()->pluck('scenario_name');

        foreach ($scenarios as $scenario) {
            $peaks = [];

            // Loop through each hour (0:00 - 23:00)
            for ($hour = 0; $hour < 24; $hour++) {
                // Get the time range for the current hour
                $startHour = sprintf('%02d:00', $hour);
                $endHour = sprintf('%02d:00', ($hour + 1) % 24);

                // Filter records for the current scenario, camera, and time range
                $hourlyTraffic = CameraTraffic::where('cam_name', $camera->cam_name)
                    ->where('cam_ip', $camera->cam_ip)
                    ->where('scenario_name', $scenario)
                    ->whereTime('time', '>=', $startHour)
                    ->whereTime('time', '<', $endHour)
                    ->when($startTime && $endTime, function ($q) use ($startTime, $endTime) {
                        $q->whereBetween('time', [$startTime, $endTime]);
                    })
                    ->orderBy('total_count', 'desc') // Order by total_count to get the peak within the hour
                    ->first();

                // If there are records in this time range, add the maximum count
                if ($hourlyTraffic) {
                    $peaks["$startHour~$endHour"] = $hourlyTraffic->total_count;
                } else {
                    // If no data, use 0
                    $peaks["$startHour~$endHour"] = 0;
                }
            }

            // Add the hourly peaks to the timePeaks array for the current scenario
            $timePeaks[] = [
                'scenario' => $scenario,
                'peaks' => $peaks
            ];
        }

        // Structure the final response with time peaks included
        $response = [
            'camera_name' => $camera->cam_name,
            'camera_ip' => $camera->cam_ip,
            'total_cars' => $totals->total_cars ?? 0,
            'total_buses' => $totals->total_buses ?? 0,
            'total_trucks' => $totals->total_trucks ?? 0,
            'total_humans' => $totals->total_humans ?? 0,
            'total_bikes' => $totals->total_bikes ?? 0,
            'total_objects' => $totals->total_objects ?? 0,
            'peak' => [
                'scenario' => $totalPeak['scenario'] ?? 'N/A',
                'time' => $totalPeak['time'] ?? 'N/A',
                'count' => $totalPeak['count'] ?? 'N/A',
            ],
            'scenario_peaks' => $formattedScenarioPeaks,
            'time_peaks' => $timePeaks
        ];

        // Return the response in JSON format
        return response()->json($response, 200);
    }

}
