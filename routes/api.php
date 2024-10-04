<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CountCameraController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/count_cam', [CountCameraController::class, 'listCameras']);
Route::get('/cameratraffic', [CountCameraController::class, 'getTrafficDataByCamera']);
Route::get('/count_cam/{id}/stats', [CountCameraController::class, 'getCameraStats']);
Route::get('/count_cam/stats/peak', [CountCameraController::class, 'getPeakHourAndCameraStats']);