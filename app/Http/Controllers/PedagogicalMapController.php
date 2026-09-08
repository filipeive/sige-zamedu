<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\School;
use App\Models\Province;
use App\Models\District;
use App\Models\Zip;
use App\Services\HierarchyScopeResolver;
use Illuminate\Http\Request;

class PedagogicalMapController extends Controller
{
    protected HierarchyScopeResolver $scopeResolver;

    public function __construct(HierarchyScopeResolver $scopeResolver)
    {
        $this->scopeResolver = $scopeResolver;
    }

    public function index(Request $request)
    {
        $this->authorize('view_grades');

        $user = auth()->user();
        $year = (int) $request->get('year', current_school_year());
        $term = (int) $request->get('term', 1);
        
        $selectedProvinceId = $request->get('province_id');
        $selectedDistrictId = $request->get('district_id');
        $selectedZipId = $request->get('zip_id');
        $schoolId = $request->get('school_id');
        $classId = $request->get('class_id');

        // 1. Resolve Territorial Scope for Schools
        $allowedSchoolIds = $this->scopeResolver->resolveVisibleSchoolIds($user);

        $schoolsQuery = School::with(['province', 'district', 'zip']);

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

        $schools = $schoolsQuery->get();

        // Selected school fallback
        if ($schoolId) {
            $selectedSchool = $schools->firstWhere('id', $schoolId);
        } else {
            $selectedSchool = $schools->firstWhere('id', $user->school_id) ?? $schools->first();
        }

        if (!$selectedSchool && $schools->isNotEmpty()) {
            $selectedSchool = $schools->first();
        }

        // Territory dropdowns
        $provinces = Province::where('is_active', true)->get();

        $districtsQuery = District::where('is_active', true);
        if ($selectedProvinceId) {
            $districtsQuery->where('province_id', $selectedProvinceId);
        }
        $districts = $districtsQuery->get();

        $zipsQuery = Zip::where('is_active', true);
        if ($selectedDistrictId) {
            $zipsQuery->where('district_id', $selectedDistrictId);
        }
        $zips = $zipsQuery->get();

        // Classes for selected school
        $allClasses = collect([]);
        if ($selectedSchool) {
            $allClasses = ClassRoom::withoutGlobalScopes()
                ->where('school_id', $selectedSchool->id)
                ->orderBy('grade_level')
                ->orderBy('name')
                ->get();
        }

        $selectedClass = $classId ? $allClasses->firstWhere('id', $classId) : $allClasses->first();

        $mapData = [];
        $classMatrix = [];
        $passingThreshold = (float) setting('passing_grade', 9.5);

        // 2. QUADRO 1: Group BY CLASSE (Grade Level) instead of Turma
        if ($selectedSchool && $allClasses->isNotEmpty()) {
            $groupedByGrade = $allClasses->groupBy('grade_level');

            foreach ($groupedByGrade as $gradeLevel => $classGroup) {
                $classIds = $classGroup->pluck('id')->toArray();
                $gradeLevelName = $classGroup->first()->grade_level_name;

                // Students in this grade level
                $students = Student::withoutGlobalScopes()
                    ->where('school_id', $selectedSchool->id)
                    ->whereHas('enrollments', function ($q) use ($classIds) {
                        $q->whereIn('class_id', $classIds);
                    })
                    ->get();

                if ($students->isEmpty()) {
                    $students = Student::withoutGlobalScopes()
                        ->where('school_id', $selectedSchool->id)
                        ->whereIn('class_id', $classIds)
                        ->get();
                }

                $totalHM = $students->count();
                $totalM = $students->where('gender', 'F')->count();

                // Grades for all classes of this grade level
                $classGrades = Grade::withoutGlobalScopes()
                    ->where('school_id', $selectedSchool->id)
                    ->whereIn('class_id', $classIds)
                    ->where('term', $term)
                    ->where('year', $year)
                    ->whereNotNull('grade')
                    ->get();

                $evaluatedStudents = $students->filter(function ($s) use ($classGrades) {
                    return $classGrades->where('student_id', $s->id)->isNotEmpty();
                });

                $evalHM = $evaluatedStudents->count();
                $evalM = $evaluatedStudents->where('gender', 'F')->count();

                $passedHM = 0;
                $passedM = 0;

                foreach ($evaluatedStudents as $student) {
                    $stGrades = $classGrades->where('student_id', $student->id);
                    $avgGrade = $stGrades->avg('grade') ?? 0;
                    if ($avgGrade >= $passingThreshold) {
                        $passedHM++;
                        if (strtolower($student->gender) === 'f' || strtolower($student->gender) === 'female') {
                            $passedM++;
                        }
                    }
                }

                $failedHM = $evalHM - $passedHM;
                $failedM = $evalM - $passedM;

                $rateHM = $evalHM > 0 ? round(($passedHM / $evalHM) * 100, 1) : 0.0;
                $rateM = $evalM > 0 ? round(($passedM / $evalM) * 100, 1) : 0.0;

                $classMatrix[] = [
                    'grade_level' => $gradeLevel,
                    'grade_level_name' => $gradeLevelName,
                    'turmas_count' => count($classIds),
                    'total_hm' => $totalHM,
                    'total_m' => $totalM,
                    'eval_hm' => $evalHM,
                    'eval_m' => $evalM,
                    'passed_hm' => $passedHM,
                    'passed_m' => $passedM,
                    'failed_hm' => $failedHM,
                    'failed_m' => $failedM,
                    'rate_hm' => $rateHM,
                    'rate_m' => $rateM,
                ];
            }
        }

        // 3. QUADRO 2: Subject performance for selected class (or overall)
        if ($selectedSchool && $selectedClass) {
            $students = Student::withoutGlobalScopes()
                ->where('school_id', $selectedSchool->id)
                ->whereHas('enrollments', function ($q) use ($selectedClass) {
                    $q->where('class_id', $selectedClass->id);
                })
                ->orderBy('first_name')
                ->get();

            if ($students->isEmpty()) {
                $students = Student::withoutGlobalScopes()
                    ->where('school_id', $selectedSchool->id)
                    ->where('class_id', $selectedClass->id)
                    ->get();
            }

            $subjects = $selectedClass->subjects->isEmpty()
                ? Subject::active()->get()
                : $selectedClass->subjects;

            $allGrades = Grade::withoutGlobalScopes()
                ->where('school_id', $selectedSchool->id)
                ->whereIn('student_id', $students->pluck('id'))
                ->where('class_id', $selectedClass->id)
                ->where('term', $term)
                ->where('year', $year)
                ->get();

            foreach ($subjects as $subject) {
                $hmEvaluated = 0;
                $mEvaluated = 0;
                $hmPositives = 0;
                $mPositives = 0;
                $hmNegatives = 0;
                $mNegatives = 0;

                foreach ($students as $student) {
                    $gender = strtolower($student->gender ?? 'male');
                    $isFemale = in_array($gender, ['female', 'f', 'mulher']);

                    $stGrades = $allGrades->filter(fn ($g) => (int)$g->student_id === $student->id && (int)$g->subject_id === $subject->id);
                    $mt = Grade::calculateMT($student->id, $subject->id, $selectedClass->id, $term, $stGrades);

                    if ($mt !== null) {
                        $hmEvaluated++;
                        if ($isFemale) {
                            $mEvaluated++;
                        }

                        if ($mt >= $passingThreshold) {
                            $hmPositives++;
                            if ($isFemale) {
                                $mPositives++;
                            }
                        } else {
                            $hmNegatives++;
                            if ($isFemale) {
                                $mNegatives++;
                            }
                        }
                    }
                }

                $hmPerc = $hmEvaluated > 0 ? round(($hmPositives / $hmEvaluated) * 100, 1) : 0;
                $mPerc = $mEvaluated > 0 ? round(($mPositives / $mEvaluated) * 100, 1) : 0;

                $mapData[] = [
                    'subject' => $subject,
                    'hm_evaluated' => $hmEvaluated,
                    'm_evaluated' => $mEvaluated,
                    'hm_positives' => $hmPositives,
                    'm_positives' => $mPositives,
                    'hm_negatives' => $hmNegatives,
                    'm_negatives' => $mNegatives,
                    'hm_perc' => $hmPerc,
                    'm_perc' => $mPerc,
                ];
            }
        }

        return view('reports.pedagogical-map', compact(
            'schools',
            'selectedSchool',
            'provinces',
            'districts',
            'zips',
            'selectedProvinceId',
            'selectedDistrictId',
            'selectedZipId',
            'allClasses',
            'selectedClass',
            'year',
            'term',
            'classMatrix',
            'mapData',
            'passingThreshold'
        ));
    }

    public function exportCsv(Request $request)
    {
        $this->authorize('view_grades');

        $year = (int) $request->get('year', current_school_year());
        $term = (int) $request->get('term', 1);
        $classId = $request->get('class_id');

        $class = ClassRoom::findOrFail($classId);

        $students = Student::whereHas('enrollments', function ($q) use ($class) {
            $q->where('class_id', $class->id);
        })->orderBy('first_name')->get();

        if ($students->isEmpty()) {
            $students = $class->students;
        }

        $subjects = $class->subjects->isEmpty() ? Subject::active()->get() : $class->subjects;

        $allGrades = Grade::whereIn('student_id', $students->pluck('id'))
            ->where('class_id', $class->id)
            ->where('term', $term)
            ->where('year', $year)
            ->get();

        $filename = "Mapa_Aproveitamento_Pedagogico_Turma_{$class->name}_Trimestre_{$term}_{$year}.csv";
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($subjects, $students, $allGrades, $class, $term) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['MAPA DE APROVEITAMENTO PEDAGÓGICO - MINEDH (MOÇAMBIQUE)'], ';');
            fputcsv($file, ["Turma: {$class->name}", "Ano Lectivo: {$class->school_year}", "Trimestre: {$term}º Trimestre"], ';');
            fputcsv($file, [], ';');

            fputcsv($file, [
                'Disciplina',
                'Avaliados (HM)',
                'Avaliados (M)',
                'Positivas (HM)',
                'Positivas (M)',
                'Negativas (HM)',
                'Negativas (M)',
                '% Aproveitamento (HM)',
                '% Aproveitamento (M)',
            ], ';');

            $passingThreshold = (float) setting('passing_grade', 9.5);

            foreach ($subjects as $subject) {
                $hmEvaluated = 0;
                $mEvaluated = 0;
                $hmPositives = 0;
                $mPositives = 0;
                $hmNegatives = 0;
                $mNegatives = 0;

                foreach ($students as $student) {
                    $gender = strtolower($student->gender ?? 'male');
                    $isFemale = in_array($gender, ['female', 'f', 'mulher']);

                    $stGrades = $allGrades->filter(fn ($g) => (int)$g->student_id === $student->id && (int)$g->subject_id === $subject->id);
                    $mt = Grade::calculateMT($student->id, $subject->id, $class->id, $term, $stGrades);

                    if ($mt !== null) {
                        $hmEvaluated++;
                        if ($isFemale) $mEvaluated++;

                        if ($mt >= $passingThreshold) {
                            $hmPositives++;
                            if ($isFemale) $mPositives++;
                        } else {
                            $hmNegatives++;
                            if ($isFemale) $mNegatives++;
                        }
                    }
                }

                $hmPerc = $hmEvaluated > 0 ? round(($hmPositives / $hmEvaluated) * 100, 1) : 0;
                $mPerc = $mEvaluated > 0 ? round(($mPositives / $mEvaluated) * 100, 1) : 0;

                fputcsv($file, [
                    $subject->name,
                    $hmEvaluated,
                    $mEvaluated,
                    $hmPositives,
                    $mPositives,
                    $hmNegatives,
                    $mNegatives,
                    $hmPerc . '%',
                    $mPerc . '%',
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
