<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeightTraffic extends Model
{
    use HasFactory;

    protected $table = 'heighttraffic';

    protected $fillable = [
        'cam_ip',
        'cam_name',
        'detect_time',
        'height',
        'plate',
        'plate_size',
        'plate_sharp',
        'photo',
        'photo_size',
        'photo_sharp',
    ];

    public $timestamps = true;
}
