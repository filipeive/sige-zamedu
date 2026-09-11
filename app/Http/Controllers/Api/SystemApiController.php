<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SystemCacheService;
use App\Models\Province;
use App\Models\District;
use App\Models\Zip;
use App\Models\School;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SystemApiController extends Controller
{
    protected SystemCacheService $cacheService;

    public function __construct(SystemCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * GET /api/v1/territory/provinces
     */
    public function provinces(): JsonResponse
    {
        $provinces = $this->cacheService->getProvinces();
        return response()->json([
            'status' => 'success',
            'data' => $provinces,
        ]);
    }

    /**
     * GET /api/v1/territory/districts?province_id=
     */
    public function districts(Request $request): JsonResponse
    {
        $provinceId = $request->query('province_id') ? (int)$request->query('province_id') : null;
        $districts = $this->cacheService->getDistricts($provinceId);

        return response()->json([
            'status' => 'success',
            'data' => $districts,
        ]);
    }

    /**
     * GET /api/v1/territory/zips?district_id=
     */
    public function zips(Request $request): JsonResponse
    {
        $districtId = $request->query('district_id') ? (int)$request->query('district_id') : null;
        $zips = $this->cacheService->getZips($districtId);

        return response()->json([
            'status' => 'success',
            'data' => $zips,
        ]);
    }

    /**
     * GET /api/v1/territory/schools?zip_id=&district_id=&province_id=
     */
    public function schools(Request $request): JsonResponse
    {
        $zipId = $request->query('zip_id') ? (int)$request->query('zip_id') : null;
        $districtId = $request->query('district_id') ? (int)$request->query('district_id') : null;
        $provinceId = $request->query('province_id') ? (int)$request->query('province_id') : null;

        $schools = $this->cacheService->getSchools($zipId, $districtId, $provinceId);

        return response()->json([
            'status' => 'success',
            'data' => $schools,
        ]);
    }

    /**
     * GET /api/v1/schools/{school}/stats
     */
    public function schoolStats(School $school): JsonResponse
    {
        $details = $this->cacheService->getSchoolDetails($school->id);

        return response()->json([
            'status' => 'success',
            'data' => $details,
        ]);
    }

    /**
     * GET /api/v1/classes/{class}/students
     */
    public function classStudents(ClassRoom $class): JsonResponse
    {
        $students = $class->students()
            ->select('students.id', 'first_name', 'last_name', 'student_number', 'gender', 'passport_photo')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $students,
        ]);
    }
}
