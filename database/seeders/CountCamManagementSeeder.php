<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CountCamManagement;
use Faker\Factory as Faker;

class CountCamManagementSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Define 3 unique camera names and IPs
        $cameraNames = ['Cam-A', 'Cam-B', 'Cam-C'];
        $cameraIPs = ['192.168.1.1', '192.168.1.2', '192.168.1.3'];

        // Create 9 cameras (3 combinations of name and IP)
        foreach (range(1, end: 3) as $index) {
            CountCamManagement::create([
                'cam_name' => $cameraNames[$index - 1],
                'cam_ip' => $cameraIPs[$index - 1],
                'code' => strtoupper($faker->bothify('CAM###')),
            ]);
        }
    }
}
