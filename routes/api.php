<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



use App\Http\Controllers\CountCameraController;

Route::get('/count_cam', [CountCameraController::class, 'listCameras']);
Route::get('/cameratraffic', [CountCameraController::class, 'getTrafficDataByCamera']);
Route::get('/count_cam/{id}/stats', [CountCameraController::class, 'getCameraStats']);
Route::get('/count_cam/stats/peak', [CountCameraController::class, 'getPeakHourAndCameraStats']);
