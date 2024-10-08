<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HeightTraffic;
use App\Models\HeightCamManagement;
use Faker\Factory as Faker;

class HeightTrafficSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $cameras = HeightCamManagement::all();
        foreach ($cameras as $camera) {
            // Generate 10 to 20 traffic records for each camera
            foreach (range(1, rand(100, 200)) as $index) {
                HeightTraffic::create([
                    'cam_name' => $camera->cam_name,
                    'cam_ip' => $camera->cam_ip,
                    'detect_time' => $faker->dateTimeBetween('-1 year', 'now')->getTimestamp(),  // Random timestamp
                    'height' => $faker->randomFloat(2, 1.0, 3.0),  // Random height between 1.0 and 3.0 meters
                    'plate' => $faker->text(10),  // Random plate number
                    'plate_size' => $faker->numberBetween(100, 200),  // Random plate size
                    'plate_sharp' => $faker->randomFloat(2, 0, 1),  // Random sharpness value
                    'photo' => $faker->imageUrl(640, 480, 'vehicles'),  // Random photo URL
                    'photo_size' => $faker->numberBetween(1000, 5000),  // Random photo size
                    'photo_sharp' => $faker->randomFloat(2, 0, 1),  // Random sharpness value for photo
                ]);
            }
        }
    }
}
