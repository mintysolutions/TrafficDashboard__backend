<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CountCameraController;
use App\Http\Controllers\SpeedCameraController;
use App\Http\Controllers\HeightCameraController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/count_cam', [CountCameraController::class, 'listCameras']);
Route::get('/count_cam/{id}/stats', [CountCameraController::class, 'getCameraStats']);
Route::get('/cameratraffic', [CountCameraController::class, 'getTrafficDataByCamera']);

Route::get('/speed_cam', [SpeedCameraController::class, 'listCameras']);
Route::get('/speedtraffic', [SpeedCameraController::class, 'getTrafficDataByCamera']);

Route::get('/height_cam', [HeightCameraController::class, 'listCameras']);
Route::get('/heighttraffic', [HeightCameraController::class, 'getTrafficDataByCamera']);