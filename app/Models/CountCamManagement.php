<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountCamManagement extends Model
{
    use HasFactory;

    protected $table = 'count_cam_management';

    protected $fillable = [
        'cam_ip',
        'cam_name',
        'code',
    ];
}
