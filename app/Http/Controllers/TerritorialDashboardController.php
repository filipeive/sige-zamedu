<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\ClassRoom;
use App\Models\Grade;
use App\Models\Province;
use App\Models\District;
use App\Models\Zip;
use App\Services\HierarchyScopeResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TerritorialDashboardController extends Controller
{
    protected HierarchyScopeResolver $scopeResolver;

    public function __construct(HierarchyScopeResolver $scopeResolver)
    {
        $this->scopeResolver = $scopeResolver;
    }

    /**
     * Main Territorial Inspection & Analytics Dashboard
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Ensure user has territorial inspection permission or role
        if (!$user->can('view_territorial_dashboard') && 
            !$user->hasAnyRole(['super_admin', 'province_admin', 'province_inspector', 'district_admin', 'district_inspector', 'zip_coordinator', 'zip_admin'])) {
            abort(403, 'Acesso não autorizado ao Painel Territorial de Inspeção.');
        }

        $territorialScope = $this->scopeResolver->resolveTerritorialScope($user);
        $allowedSchoolIds = $this->scopeResolver->resolveVisibleSchoolIds($user);

        // Filters
        $selectedProvinceId = $request->input('province_id');
        $selectedDistrictId = $request->input('district_id');
        $selectedZipId = $request->input('zip_id');

        // Build Schools Query based on Scope and Filters
        $schoolsQuery = School::query();

        if (!is_null($allowedSchoolIds)) {
            $schoolsQuery->whereIn('id', $allowedSchoolIds);
        }

        if ($selectedProvinceId) {
            $schoolsQuery->where('province_id', $selectedProvinceId);
        }
        if ($selectedDistrictId) {
            $schoolsQuery->where('district_id', $selectedDistrictId);
        }
        if ($selectedZipId) {
            $schoolsQuery->where('zip_id', $selectedZipId);
        }

        $schools = $schoolsQuery->with(['province', 'district', 'zip'])->get();
        $targetSchoolIds = $schools->pluck('id')->toArray();

        // 1. KPI Counts
        $schoolsCount = $schools->count();

        // Students metrics (HM & M)
        $studentsQuery = Student::withoutGlobalScopes()->whereIn('school_id', $targetSchoolIds);
        $studentsHM = (clone $studentsQuery)->count();
        $studentsM = (clone $studentsQuery)->where('gender', 'F')->count();

        // Teachers metrics
        $teachersCount = Teacher::withoutGlobalScopes()->whereIn('school_id', $targetSchoolIds)->count();

        // Classes metrics
        $classesCount = ClassRoom::withoutGlobalScopes()->whereIn('school_id', $targetSchoolIds)->count();

        // 2. Pedagogical Evaluation Stats (MINEDH Standard: HM & M)
        $gradesQuery = Grade::withoutGlobalScopes()
            ->join('students', 'grades.student_id', '=', 'students.id')
            ->whereIn('grades.school_id', $targetSchoolIds);

        $gradesHM = (clone $gradesQuery)->whereNotNull('grades.grade')->select('grades.grade', 'students.gender')->get();

        $evaluatedHM = $gradesHM->count();
        $evaluatedM = $gradesHM->where('gender', 'F')->count();

        $positiveHM = $gradesHM->where('grade', '>=', 9.5)->count();
        $positiveM = $gradesHM->where('gender', 'F')->where('grade', '>=', 9.5)->count();

        $negativeHM = $evaluatedHM - $positiveHM;
        $negativeM = $evaluatedM - $positiveM;

        $approvalRateHM = $evaluatedHM > 0 ? round(($positiveHM / $evaluatedHM) * 100, 1) : 0.0;
        $approvalRateM = $evaluatedM > 0 ? round(($positiveM / $evaluatedM) * 100, 1) : 0.0;

        // 3. Sub-Entity Comparative Breakdown
        $subEntityBreakdown = [];

        if (empty($targetSchoolIds)) {
            $subEntityBreakdown = collect([]);
        } else if ($territorialScope['level'] === 'super' || $territorialScope['level'] === 'province') {
            // Group by District
            $subEntityBreakdown = District::whereIn('id', $schools->pluck('district_id')->unique())
                ->with('province')
                ->get()
                ->map(function ($district) use ($schools) {
                    $districtSchools = $schools->where('district_id', $district->id);
                    $districtSchoolIds = $districtSchools->pluck('id')->toArray();

                    $studentsHM = Student::withoutGlobalScopes()->whereIn('school_id', $districtSchoolIds)->count();
                    $studentsM = Student::withoutGlobalScopes()->whereIn('school_id', $districtSchoolIds)->where('gender', 'F')->count();

                    $grades = Grade::withoutGlobalScopes()
                        ->join('students', 'grades.student_id', '=', 'students.id')
                        ->whereIn('grades.school_id', $districtSchoolIds)
                        ->whereNotNull('grades.grade')
                        ->select('grades.grade', 'students.gender')
                        ->get();

                    $evalHM = $grades->count();
                    $posHM = $grades->where('grade', '>=', 9.5)->count();
                    $rateHM = $evalHM > 0 ? round(($posHM / $evalHM) * 100, 1) : 0.0;

                    return [
                        'type' => 'district',
                        'id' => $district->id,
                        'name' => $district->name,
                        'parent_name' => $district->province->name ?? '',
                        'schools_count' => count($districtSchoolIds),
                        'students_hm' => $studentsHM,
                        'students_m' => $studentsM,
                        'evaluated_hm' => $evalHM,
                        'positive_hm' => $posHM,
                        'approval_rate' => $rateHM,
                    ];
                });
        } else if ($territorialScope['level'] === 'district') {
            // Group by ZIP
            $subEntityBreakdown = Zip::whereIn('id', $schools->pluck('zip_id')->unique())
                ->with('district')
                ->get()
                ->map(function ($zip) use ($schools) {
                    $zipSchools = $schools->where('zip_id', $zip->id);
                    $zipSchoolIds = $zipSchools->pluck('id')->toArray();

                    $studentsHM = Student::withoutGlobalScopes()->whereIn('school_id', $zipSchoolIds)->count();
                    $studentsM = Student::withoutGlobalScopes()->whereIn('school_id', $zipSchoolIds)->where('gender', 'F')->count();

                    $grades = Grade::withoutGlobalScopes()
                        ->join('students', 'grades.student_id', '=', 'students.id')
                        ->whereIn('grades.school_id', $zipSchoolIds)
                        ->whereNotNull('grades.grade')
                        ->select('grades.grade', 'students.gender')
                        ->get();

                    $evalHM = $grades->count();
                    $posHM = $grades->where('grade', '>=', 9.5)->count();
                    $rateHM = $evalHM > 0 ? round(($posHM / $evalHM) * 100, 1) : 0.0;

                    return [
                        'type' => 'zip',
                        'id' => $zip->id,
                        'name' => $zip->name,
                        'parent_name' => $zip->district->name ?? '',
                        'schools_count' => count($zipSchoolIds),
                        'students_hm' => $studentsHM,
                        'students_m' => $studentsM,
                        'evaluated_hm' => $evalHM,
                        'positive_hm' => $posHM,
                        'approval_rate' => $rateHM,
                    ];
                });
        } else {
            // Group by School
            $subEntityBreakdown = $schools->map(function ($school) {
                $studentsHM = Student::withoutGlobalScopes()->where('school_id', $school->id)->count();
                $studentsM = Student::withoutGlobalScopes()->where('school_id', $school->id)->where('gender', 'F')->count();

                $grades = Grade::withoutGlobalScopes()
                    ->join('students', 'grades.student_id', '=', 'students.id')
                    ->where('grades.school_id', $school->id)
                    ->whereNotNull('grades.grade')
                    ->select('grades.grade', 'students.gender')
                    ->get();

                $evalHM = $grades->count();
                $posHM = $grades->where('grade', '>=', 9.5)->count();
                $rateHM = $evalHM > 0 ? round(($posHM / $evalHM) * 100, 1) : 0.0;

                return [
                    'type' => 'school',
                    'id' => $school->id,
                    'name' => $school->name,
                    'parent_name' => $school->zip->name ?? $school->district->name ?? '',
                    'schools_count' => 1,
                    'students_hm' => $studentsHM,
                    'students_m' => $studentsM,
                    'evaluated_hm' => $evalHM,
                    'positive_hm' => $posHM,
                    'approval_rate' => $rateHM,
                ];
            });
        }

        // Filter Options for Dropdowns
        $provinces = Province::where('is_active', true)->get();
        $districts = District::where('is_active', true)->get();
        $zips = Zip::where('is_active', true)->get();

        return view('territorial.index', compact(
            'territorialScope',
            'schoolsCount',
            'studentsHM',
            'studentsM',
            'teachersCount',
            'classesCount',
            'evaluatedHM',
            'evaluatedM',
            'positiveHM',
            'positiveM',
            'negativeHM',
            'negativeM',
            'approvalRateHM',
            'approvalRateM',
            'subEntityBreakdown',
            'provinces',
            'districts',
            'zips',
            'schools',
            'selectedProvinceId',
            'selectedDistrictId',
            'selectedZipId'
        ));
    }

    /**
     * Specific School Drill-Down for Inspections
     */
    public function schoolDrillDown(School $school)
    {
        $user = Auth::user();
        $allowedSchoolIds = $this->scopeResolver->resolveVisibleSchoolIds($user);

        if (!is_null($allowedSchoolIds) && !in_array($school->id, $allowedSchoolIds)) {
            abort(403, 'Acesso não autorizado aos dados desta escola.');
        }

        $school->load(['province', 'district', 'zip']);

        $studentsHM = Student::withoutGlobalScopes()->where('school_id', $school->id)->count();
        $studentsM = Student::withoutGlobalScopes()->where('school_id', $school->id)->where('gender', 'F')->count();
        $teachersCount = Teacher::withoutGlobalScopes()->where('school_id', $school->id)->count();
        $classesCount = ClassRoom::withoutGlobalScopes()->where('school_id', $school->id)->count();

        $grades = Grade::withoutGlobalScopes()
            ->join('students', 'grades.student_id', '=', 'students.id')
            ->where('grades.school_id', $school->id)
            ->whereNotNull('grades.grade')
            ->select('grades.grade', 'students.gender')
            ->get();

        $evaluatedHM = $grades->count();
        $evaluatedM = $grades->where('gender', 'F')->count();
        $positiveHM = $grades->where('grade', '>=', 9.5)->count();
        $positiveM = $grades->where('gender', 'F')->where('grade', '>=', 9.5)->count();

        $approvalRateHM = $evaluatedHM > 0 ? round(($positiveHM / $evaluatedHM) * 100, 1) : 0.0;
        $approvalRateM = $evaluatedM > 0 ? round(($positiveM / $evaluatedM) * 100, 1) : 0.0;

        return view('territorial.school_show', compact(
            'school',
            'studentsHM',
            'studentsM',
            'teachersCount',
            'classesCount',
            'evaluatedHM',
            'evaluatedM',
            'positiveHM',
            'positiveM',
            'approvalRateHM',
            'approvalRateM'
        ));
    }
}
