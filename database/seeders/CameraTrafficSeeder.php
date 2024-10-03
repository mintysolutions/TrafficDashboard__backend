<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CameraTraffic;
use App\Models\CountCamManagement;
use Faker\Factory as Faker;

class CameraTrafficSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Define the three specific scenario names
        $scenarioNames = ['Scenario 1', 'Scenario 2', 'Scenario 3'];

        // Get all cameras from the count_cam_management table
        $cameras = CountCamManagement::all();

        // Loop through each camera and generate random traffic data
        foreach ($cameras as $camera) {
            // Generate 10 to 20 traffic records for each camera
            foreach (range(1, rand(10, 20)) as $index) {
                CameraTraffic::create([
                    'cam_name' => $camera->cam_name,
                    'cam_ip' => $camera->cam_ip,
                    'scenario_name' => $faker->randomElement($scenarioNames), // Select from predefined scenarios
                    'bus_count' => $faker->numberBetween(0, 50),
                    'car_count' => $faker->numberBetween(0, 100),
                    'human_count' => $faker->numberBetween(0, 200),
                    'bike_count' => $faker->numberBetween(0, 50),
                    'truck_count' => $faker->numberBetween(0, 30),
                    'total_count' => $faker->numberBetween(100, 500),
                    'time' => $faker->dateTimeBetween('-1 year', 'now'),
                    'last_status' => $faker->randomElement(['online', 'offline', 'maintenance']),
                    'last_update' => $faker->dateTimeBetween('-1 day', 'now')->format('Y-m-d H:i:s'),
                    'last_offline_ping' => $faker->dateTimeBetween('-1 week', 'now')->format('Y-m-d H:i:s'),
                    'offline_since' => $faker->dateTimeBetween('-1 week', 'now')->format('Y-m-d H:i:s'),
                    'last_offline_timestamp' => $faker->dateTimeBetween('-1 week', 'now'),
                    'last_online_timestamp' => $faker->dateTimeBetween('-1 day', 'now'),
                ]);
            }
        }
    }
}
