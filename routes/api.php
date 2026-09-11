<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\ClassRoom;

use App\Http\Controllers\Api\SystemApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->middleware(['throttle:60,1'])->group(function () {
    Route::get('/territory/provinces', [SystemApiController::class, 'provinces'])->name('api.v1.territory.provinces');
    Route::get('/territory/districts', [SystemApiController::class, 'districts'])->name('api.v1.territory.districts');
    Route::get('/territory/zips', [SystemApiController::class, 'zips'])->name('api.v1.territory.zips');
    Route::get('/territory/schools', [SystemApiController::class, 'schools'])->name('api.v1.territory.schools');
    Route::get('/schools/{school}/stats', [SystemApiController::class, 'schoolStats'])->name('api.v1.school.stats');
    Route::get('/classes/{class}/students', [SystemApiController::class, 'classStudents'])->name('api.v1.class.students');
});

Route::get('/classes/{class}/students', function($classId) {
    return ClassRoom::findOrFail($classId)
        ->students()
        ->select('id', 'first_name', 'last_name', 'student_number', 'passport_photo')
        ->get();
});