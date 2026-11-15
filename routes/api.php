<?php

use App\Http\Controllers\Api\SmartEcoController;
use Illuminate\Support\Facades\Route;

Route::get('/live-status', [SmartEcoController::class, 'liveStatus']);
Route::post('/claim-otp', [SmartEcoController::class, 'claimOtp']);
Route::post('/bins/{category}/empty', [SmartEcoController::class, 'emptyBin']);
Route::post('/notifications/read-all', [SmartEcoController::class, 'readAllNotifications']);
Route::post('/device/servo', [SmartEcoController::class, 'servo']);
Route::post('/device/call-officer', [SmartEcoController::class, 'callOfficer']);
