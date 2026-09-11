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

        // 4. Detailed Data for Tabs (Schools, ZIPs, Districts, Provinces)
        $schoolsDetailed = $schools->map(function ($school) {
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

            $evalHM = $grades->count();
            $posHM = $grades->where('grade', '>=', 9.5)->count();
            $rateHM = $evalHM > 0 ? round(($posHM / $evalHM) * 100, 1) : 0.0;

            $directorName = $school->principal_name ?: 'Diretor Geral Não Atribuído';
            $directorPhone = $school->phone ?: '+258 84 000 0000';
            $directorEmail = $school->email ?: 'escola@minedh.gov.mz';

            $coords = static::getSchoolCoordinates($school);

            return [
                'id' => $school->id,
                'name' => $school->name,
                'code' => $school->code ?: 'ESC-'.$school->id,
                'type' => $school->type ?: 'Pública',
                'address' => $school->address ?: 'Bairro Central',
                'principal_name' => $directorName,
                'phone' => $directorPhone,
                'email' => $directorEmail,
                'province_name' => $school->province->name ?? 'Província',
                'district_name' => $school->district->name ?? 'Distrito',
                'zip_name' => $school->zip->name ?? 'Sem ZIP',
                'students_hm' => $studentsHM,
                'students_m' => $studentsM,
                'teachers_count' => $teachersCount,
                'classes_count' => $classesCount,
                'approval_rate' => $rateHM,
                'lat' => $coords['lat'],
                'lng' => $coords['lng'],
            ];
        });

        // Detailed ZIPs
        $zipsDetailed = Zip::whereIn('id', $schools->pluck('zip_id')->filter()->unique())
            ->with(['district.province', 'schools'])
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
                    ->select('grades.grade')
                    ->get();

                $evalHM = $grades->count();
                $posHM = $grades->where('grade', '>=', 9.5)->count();
                $rateHM = $evalHM > 0 ? round(($posHM / $evalHM) * 100, 1) : 0.0;

                return [
                    'id' => $zip->id,
                    'name' => $zip->name,
                    'code' => $zip->code ?: 'ZIP-'.$zip->id,
                    'headquarters' => $zip->headquarters ?: 'Escola Sede ZIP',
                    'district_name' => $zip->district->name ?? 'N/D',
                    'province_name' => $zip->district->province->name ?? 'N/D',
                    'schools_count' => count($zipSchoolIds),
                    'students_hm' => $studentsHM,
                    'students_m' => $studentsM,
                    'approval_rate' => $rateHM,
                ];
            });

        // Detailed Districts (SDEJT)
        $districtsDetailed = District::whereIn('id', $schools->pluck('district_id')->filter()->unique())
            ->with(['province', 'zips', 'schools'])
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
                    ->select('grades.grade')
                    ->get();

                $evalHM = $grades->count();
                $posHM = $grades->where('grade', '>=', 9.5)->count();
                $rateHM = $evalHM > 0 ? round(($posHM / $evalHM) * 100, 1) : 0.0;

                return [
                    'id' => $district->id,
                    'name' => $district->name,
                    'code' => $district->code ?: 'DIST-'.$district->id,
                    'province_name' => $district->province->name ?? 'N/D',
                    'zips_count' => $district->zips->count(),
                    'schools_count' => count($districtSchoolIds),
                    'students_hm' => $studentsHM,
                    'students_m' => $studentsM,
                    'approval_rate' => $rateHM,
                ];
            });

        // Detailed Provinces (DPEDH)
        $provincesDetailed = Province::whereIn('id', $schools->pluck('province_id')->filter()->unique())
            ->with(['districts', 'schools'])
            ->get()
            ->map(function ($province) use ($schools) {
                $provSchools = $schools->where('province_id', $province->id);
                $provSchoolIds = $provSchools->pluck('id')->toArray();

                $studentsHM = Student::withoutGlobalScopes()->whereIn('school_id', $provSchoolIds)->count();
                $studentsM = Student::withoutGlobalScopes()->whereIn('school_id', $provSchoolIds)->where('gender', 'F')->count();

                $grades = Grade::withoutGlobalScopes()
                    ->join('students', 'grades.student_id', '=', 'students.id')
                    ->whereIn('grades.school_id', $provSchoolIds)
                    ->whereNotNull('grades.grade')
                    ->select('grades.grade')
                    ->get();

                $evalHM = $grades->count();
                $posHM = $grades->where('grade', '>=', 9.5)->count();
                $rateHM = $evalHM > 0 ? round(($posHM / $evalHM) * 100, 1) : 0.0;

                return [
                    'id' => $province->id,
                    'name' => $province->name,
                    'code' => $province->code ?: 'PROV-'.$province->id,
                    'districts_count' => $province->districts->count(),
                    'schools_count' => count($provSchoolIds),
                    'students_hm' => $studentsHM,
                    'students_m' => $studentsM,
                    'approval_rate' => $rateHM,
                ];
            });

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
            'schoolsDetailed',
            'zipsDetailed',
            'districtsDetailed',
            'provincesDetailed',
            'selectedProvinceId',
            'selectedDistrictId',
            'selectedZipId'
        ));
    }

    /**
     * Helper to get approximate coordinates for schools across Mozambique
     */
    protected static function getSchoolCoordinates(School $school): array
    {
        if (!is_null($school->latitude) && !is_null($school->longitude) && (float)$school->latitude != 0.0 && (float)$school->longitude != 0.0) {
            return ['lat' => (float)$school->latitude, 'lng' => (float)$school->longitude];
        }

        $prov = strtolower($school->province->name ?? '');
        $dist = strtolower($school->district->name ?? '');
        $name = strtolower($school->name);

        if (str_contains($prov, 'maputo') || str_contains($dist, 'maputo')) {
            if (str_contains($name, 'paz') || str_contains($dist, 'matola')) {
                return ['lat' => -25.9622 + ((($school->id * 17) % 30) - 15) / 1000, 'lng' => 32.4589 + ((($school->id * 13) % 30) - 15) / 1000];
            }
            return ['lat' => -25.9692 + ((($school->id * 19) % 40) - 20) / 1000, 'lng' => 32.5732 + ((($school->id * 11) % 40) - 20) / 1000];
        }
        if (str_contains($prov, 'zambézia') || str_contains($prov, 'zambezia') || str_contains($dist, 'marracua') || str_contains($dist, 'quelimane')) {
            return ['lat' => -17.8786 + ((($school->id * 23) % 40) - 20) / 1000, 'lng' => 36.8883 + ((($school->id * 29) % 40) - 20) / 1000];
        }
        if (str_contains($prov, 'nampula')) {
            return ['lat' => -15.1165 + ((($school->id * 31) % 40) - 20) / 1000, 'lng' => 39.2666 + ((($school->id * 37) % 40) - 20) / 1000];
        }
        if (str_contains($prov, 'sofala') || str_contains($dist, 'beira')) {
            return ['lat' => -19.8436 + ((($school->id * 41) % 40) - 20) / 1000, 'lng' => 34.8389 + ((($school->id * 43) % 40) - 20) / 1000];
        }
        if (str_contains($prov, 'cabo delgado') || str_contains($dist, 'pemba')) {
            return ['lat' => -12.9740 + ((($school->id * 47) % 40) - 20) / 1000, 'lng' => 40.5178 + ((($school->id * 53) % 40) - 20) / 1000];
        }
        if (str_contains($prov, 'tete')) {
            return ['lat' => -16.1564 + ((($school->id * 59) % 40) - 20) / 1000, 'lng' => 33.5867 + ((($school->id * 61) % 40) - 20) / 1000];
        }

        $offsetLat = ((($school->id * 17) % 60) - 30) / 1000;
        $offsetLng = ((($school->id * 23) % 60) - 30) / 1000;

        return ['lat' => -25.9692 + $offsetLat, 'lng' => 32.5732 + $offsetLng];
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

        $coordinates = $this->getSchoolCoordinates($school);

        $classes = ClassRoom::withoutGlobalScopes()
            ->where('school_id', $school->id)
            ->with('teacher')
            ->withCount('students')
            ->orderBy('grade_level')
            ->get();

        $teachers = Teacher::withoutGlobalScopes()
            ->where('school_id', $school->id)
            ->get();

        $femaleStudentRatio = $studentsHM > 0 ? round(($studentsM / $studentsHM) * 100, 1) : 0.0;
        $studentTeacherRatio = $teachersCount > 0 ? round($studentsHM / $teachersCount, 1) : 0;
        $studentClassRatio = $classesCount > 0 ? round($studentsHM / $classesCount, 1) : 0;

        return view('territorial.school_show', compact(
            'school',
            'coordinates',
            'studentsHM',
            'studentsM',
            'femaleStudentRatio',
            'teachersCount',
            'classesCount',
            'studentTeacherRatio',
            'studentClassRatio',
            'evaluatedHM',
            'evaluatedM',
            'positiveHM',
            'positiveM',
            'approvalRateHM',
            'approvalRateM',
            'classes',
            'teachers'
        ));
    }
}
