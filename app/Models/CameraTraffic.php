<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CameraTraffic extends Model
{
    use HasFactory;

    protected $table = 'cameratraffic';

    protected $fillable = [
        'cam_ip',
        'cam_name',
        'scenario_name',
        'bus_count',
        'car_count',
        'human_count',
        'bike_count',
        'truck_count',
        'total_count',
        'time',
        'last_status',
        'last_update',
        'last_offline_ping',
        'offline_since',
        'last_offline_timestamp',
        'last_online_timestamp',
    ];

    public $timestamps = true;
}
