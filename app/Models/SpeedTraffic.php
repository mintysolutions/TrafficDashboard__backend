<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpeedTraffic extends Model
{
    use HasFactory;

    protected $table = 'speedtraffic';

    protected $fillable = [
        'cam_ip',
        'cam_name',
        'start_time',
        'end_time',
        'speed',
        'photo',
    ];

    public $timestamps = true;
}
