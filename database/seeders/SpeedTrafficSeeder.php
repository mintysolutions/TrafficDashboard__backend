<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SpeedTraffic;
use App\Models\SpeedCamManagement;
use Faker\Factory as Faker;
use Carbon\Carbon;

class SpeedTrafficSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $cameras = SpeedCamManagement::all();
        // Loop through each camera and generate random traffic data
        foreach ($cameras as $camera) {
            // Define the range
            $oneYearAgo = Carbon::now()->subYear(); // Get the date one year ago
            $now = Carbon::now(); // Current date and time

            // Generate a random timestamp in milliseconds between now and one year ago
            $randomTimestamp = mt_rand($oneYearAgo->timestamp * 1000, $now->timestamp * 1000);

            foreach (range(1, rand(100, 200)) as $index) {
                SpeedTraffic::create([
                    'cam_name' => $camera->cam_name,
                    'cam_ip' => $camera->cam_ip,
                    'start_time' => $randomTimestamp,
                    'end_time' => $randomTimestamp + 1000,
                    'speed' => $faker->randomFloat(2, 0.0, 80.0),
                    'photo' => $faker->imageUrl(640, 480, 'vehicles')
                ]);
            }
        }
    }
}
