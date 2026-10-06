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
use App\Models\Enrollment;
use App\Services\HierarchyScopeResolver;
use Illuminate\Http\Request;

class PedagogicalMapController extends Controller
{
    protected HierarchyScopeResolver $scopeResolver;

    public function __construct(HierarchyScopeResolver $scopeResolver)
    {
        $this->scopeResolver = $scopeResolver;
    }
    // 
    public function index(Request $request)
    {
        $this->authorize('view_grades');
        $data = $this->buildPedagogicalMapData($request);
        return view('reports.pedagogical-map', $data);
    }

    public function exportPdf(Request $request)
    {
        $this->authorize('view_grades');
        $data = $this->buildPedagogicalMapData($request);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pedagogical-map-pdf', $data)
            ->setPaper('a4', 'landscape');

        $schoolName = \Illuminate\Support\Str::slug($data['selectedSchool']?->name ?? 'escola', '_');
        $term = $data['term'];
        $year = $data['year'];

        return $pdf->download("Mapa_Rendimento_Escolar_{$schoolName}_T{$term}_{$year}.pdf");
    }

    public function exportCsv(Request $request)
    {
        $this->authorize('view_grades');
        $data = $this->buildPedagogicalMapData($request);

        $schoolName = \Illuminate\Support\Str::slug($data['selectedSchool']?->name ?? 'escola', '_');
        $term = $data['term'];
        $year = $data['year'];

        $filename = "Mapa_Rendimento_Escolar_{$schoolName}_T{$term}_{$year}.csv";
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            $selectedSchool = $data['selectedSchool'];
            $termStr = $data['term'] == 4 ? 'FINAL DO ANO' : $data['term'] . 'º TRIMESTRE';

            fputcsv($file, ['REPÚBLICA DE MOÇAMBIQUE'], ';');
            fputcsv($file, ['GOVERNO DA PROVÍNCIA DE ' . strtoupper($selectedSchool?->province?->name ?? 'PROVÍNCIA')], ';');
            fputcsv($file, ['SERVIÇO DISTRITAL DE EDUCAÇÃO, JUVENTUDE E TECNOLOGIA (SDEJT ' . strtoupper($selectedSchool?->district?->name ?? 'DISTRITO') . ')'], ';');
            if ($selectedSchool && $selectedSchool->zip) {
                fputcsv($file, ['ZONA DE INFLUÊNCIA PEDAGÓGICA: ' . strtoupper($selectedSchool->zip->name)], ';');
            }
            fputcsv($file, [strtoupper($selectedSchool?->name ?? 'ESCOLA')], ';');
            fputcsv($file, ["MAPA DE APROVEITAMENTO PEDAGÓGICO — {$termStr} DE {$data['year']}"], ';');
            fputcsv($file, [], ';');

            // Quadro I Header
            fputcsv($file, [
                'Classe',
                'Matrícula HM', 'Matrícula M',
                'Desistentes HM', 'Desistentes M',
                'Transf. HM', 'Transf. M',
                'Anulou HM', 'Anulou M',
                'PPF HM', 'PPF M',
                'Avaliados HM', 'Avaliados M',
                'Transitados HM', 'Transitados M',
                '% TRANS/ APROVADOS HM', '% TRANS/ APROVADOS M',
                '% Desistentes HM', '% Desistentes M'
            ], ';');

            $formatRow = function ($title, $r) {
                return [
                    $title,
                    $r['total_hm'], $r['total_m'],
                    $r['desist_hm'], $r['desist_m'],
                    $r['transf_hm'], $r['transf_m'],
                    $r['anulou_hm'], $r['anulou_m'],
                    $r['ppf_hm'], $r['ppf_m'],
                    $r['eval_hm'], $r['eval_m'],
                    $r['passed_hm'], $r['passed_m'],
                    $r['rate_hm'] . '%', $r['rate_m'] . '%',
                    $r['desist_rate_hm'] . '%', $r['desist_rate_m'] . '%'
                ];
            };

            // Preschool
            if (!empty($data['preschoolRows'])) {
                fputcsv($file, ['PRÉ-ESCOLAR / PRÉ-INFANTIL'], ';');
                foreach ($data['preschoolRows'] as $cm) {
                    fputcsv($file, $formatRow($cm['grade_level_name'], $cm), ';');
                }
            }

            // Ciclo 1
            if (!empty($data['ciclo1Rows'])) {
                fputcsv($file, [$data['ciclo1Data']['title']], ';');
                foreach ($data['ciclo1Rows'] as $cm) {
                    fputcsv($file, $formatRow($cm['grade_level_name'], $cm), ';');
                }
                fputcsv($file, $formatRow('TOTAL 1º CICLO', $data['ciclo1Data']), ';');
            }

            // Ciclo 2
            if (!empty($data['ciclo2Rows'])) {
                fputcsv($file, [$data['ciclo2Data']['title']], ';');
                foreach ($data['ciclo2Rows'] as $cm) {
                    fputcsv($file, $formatRow($cm['grade_level_name'], $cm), ';');
                }
                fputcsv($file, $formatRow('TOTAL 2º CICLO', $data['ciclo2Data']), ';');
            }

            // Total Geral
            fputcsv($file, $formatRow('TOTAL GERAL DA ESCOLA', $data['totalGeralData']), ';');

            fputcsv($file, [], ';');
            fputcsv($file, [], ';');
            fputcsv($file, ['O Diretor Adjunto Pedagógico (DAP)', '', '', '', '', '', '', '', 'O Diretor da Escola (DAE)'], ';');
            fputcsv($file, ['(Assinatura e Carimbo Oficial)', '', '', '', '', '', '', '', ($selectedSchool?->director_name ?? 'PROF. MARIA NHABINDE') . ' - (Assinatura e Carimbo Oficial)'], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function buildPedagogicalMapData(Request $request): array
    {
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

        // Ensure selected school is inside filtered schools list if filters were applied
        if ($selectedSchool && !$schools->contains('id', $selectedSchool->id)) {
            $selectedSchool = $schools->first();
        }

        // Fallback if no school matched filters
        if (!$selectedSchool) {
            $selectedSchool = School::with(['province', 'district', 'zip'])->first();
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

        // Level filter (primary | secondary | preschool | all)
        $educationLevel = $request->get('education_level') ?? $request->get('level');

        // Auto-determine level if not specified in request
        if (empty($educationLevel) && $selectedSchool) {
            $schoolGrades = ClassRoom::withoutGlobalScopes()
                ->where('school_id', $selectedSchool->id)
                ->pluck('grade_level')
                ->toArray();

            $hasPrimary = !empty(array_intersect($schoolGrades, \App\Helpers\EducationLevel::PRIMARY_GRADES));
            $hasSecondary = !empty(array_intersect($schoolGrades, \App\Helpers\EducationLevel::SECONDARY_GRADES));

            if ($hasPrimary && !$hasSecondary) {
                $educationLevel = 'primary';
            } elseif ($hasSecondary && !$hasPrimary) {
                $educationLevel = 'secondary';
            } else {
                $educationLevel = 'primary';
            }
        }

        // Classes for selected school filtered by level
        $allClasses = collect([]);
        $schoolTeachingType = 'Ensino Geral';

        if ($selectedSchool) {
            $allClassesQuery = ClassRoom::withoutGlobalScopes()
                ->where('school_id', $selectedSchool->id)
                ->orderBy('grade_level')
                ->orderBy('name');

            if (!empty($educationLevel) && $educationLevel !== 'all') {
                $allClassesQuery->byEducationLevel($educationLevel);
            }

            $allClasses = $allClassesQuery->get();

            if ($educationLevel === 'primary') {
                $schoolTeachingType = 'Ensino Básico / Primário (EP1 & EP2)';
            } elseif ($educationLevel === 'secondary') {
                $schoolTeachingType = 'Ensino Secundário Geral (ESG)';
            } elseif ($educationLevel === 'preschool') {
                $schoolTeachingType = 'Educação Pré-Escolar';
            } else {
                $schoolTeachingType = 'Ensino Geral (Primário & Secundário)';
            }
        }

        $selectedClass = $classId ? $allClasses->firstWhere('id', $classId) : $allClasses->first();

        // Dynamic cycle titles based on education level
        $ciclo1Title = '1º CICLO';
        $ciclo2Title = '2º CICLO';
        if ($educationLevel === 'primary') {
            $ciclo1Title = '1º CICLO (1ª A 3ª CLASSE)';
            $ciclo2Title = '2º CICLO (4ª A 6ª CLASSE)';
        } elseif ($educationLevel === 'secondary') {
            $ciclo1Title = '1º CICLO / ESG1 (7ª A 9ª CLASSE)';
            $ciclo2Title = '2º CICLO / ESG2 (10ª A 12ª CLASSE)';
        }

        $mapData = [];
        $classRows = [];
        $ciclo1Rows = [];
        $ciclo2Rows = [];
        $preschoolRows = [];
        $ciclo1Data = [
            'title' => $ciclo1Title,
            'turmas_count' => 0,
            'total_hm' => 0, 'total_m' => 0,
            'desist_hm' => 0, 'desist_m' => 0,
            'transf_hm' => 0, 'transf_m' => 0,
            'anulou_hm' => 0, 'anulou_m' => 0,
            'ppf_hm' => 0, 'ppf_m' => 0,
            'eval_hm' => 0, 'eval_m' => 0,
            'passed_hm' => 0, 'passed_m' => 0,
            'failed_hm' => 0, 'failed_m' => 0,
        ];
        $ciclo2Data = [
            'title' => $ciclo2Title,
            'turmas_count' => 0,
            'total_hm' => 0, 'total_m' => 0,
            'desist_hm' => 0, 'desist_m' => 0,
            'transf_hm' => 0, 'transf_m' => 0,
            'anulou_hm' => 0, 'anulou_m' => 0,
            'ppf_hm' => 0, 'ppf_m' => 0,
            'eval_hm' => 0, 'eval_m' => 0,
            'passed_hm' => 0, 'passed_m' => 0,
            'failed_hm' => 0, 'failed_m' => 0,
        ];
        $totalGeralData = [
            'title' => 'TOTAL GERAL DA ESCOLA',
            'turmas_count' => 0,
            'total_hm' => 0, 'total_m' => 0,
            'desist_hm' => 0, 'desist_m' => 0,
            'transf_hm' => 0, 'transf_m' => 0,
            'anulou_hm' => 0, 'anulou_m' => 0,
            'ppf_hm' => 0, 'ppf_m' => 0,
            'eval_hm' => 0, 'eval_m' => 0,
            'passed_hm' => 0, 'passed_m' => 0,
            'failed_hm' => 0, 'failed_m' => 0,
        ];

        $passingThreshold = (float) setting('passing_grade', 9.5);

        // 2. QUADRO 1: Group BY CLASSE (Grade Level) according to MINEDH official layout
        if ($selectedSchool && $allClasses->isNotEmpty()) {
            $groupedByGrade = $allClasses->groupBy('grade_level');

            foreach ($groupedByGrade as $gradeLevel => $classGroup) {
                $classIds = $classGroup->pluck('id')->toArray();
                $gradeLevelName = $classGroup->first()->grade_level_name;

                // Enrollments and students in this grade level
                $enrollments = Enrollment::withoutGlobalScopes()
                    ->whereIn('class_id', $classIds)
                    ->where('school_year', $year)
                    ->with('student')
                    ->get();

                if ($enrollments->isEmpty()) {
                    $students = Student::withoutGlobalScopes()
                        ->where('school_id', $selectedSchool->id)
                        ->whereHas('enrollments', function ($q) use ($classIds) {
                            $q->whereIn('class_id', $classIds);
                        })
                        ->get();
                } else {
                    $students = $enrollments->pluck('student')->filter()->unique('id');
                }

                $isFemaleStudent = function ($student) {
                    $g = strtolower($student->gender ?? '');
                    return in_array($g, ['f', 'female', 'feminino', 'mulher'], true);
                };

                $totalHM = $students->count();
                $totalM = $students->filter($isFemaleStudent)->count();

                $desistHM = $students->filter(fn($s) => in_array($s->status ?? '', ['inactive', 'dropped', 'desistente']))->count();
                $desistM = $students->filter(fn($s) => in_array($s->status ?? '', ['inactive', 'dropped', 'desistente']) && $isFemaleStudent($s))->count();

                $transfHM = $students->filter(fn($s) => in_array($s->status ?? '', ['transferred', 'transferido']))->count();
                $transfM = $students->filter(fn($s) => in_array($s->status ?? '', ['transferred', 'transferido']) && $isFemaleStudent($s))->count();

                $anulouHM = $students->filter(fn($s) => in_array($s->status ?? '', ['cancelled', 'anulou', 'suspended']))->count();
                $anulouM = $students->filter(fn($s) => in_array($s->status ?? '', ['cancelled', 'anulou', 'suspended']) && $isFemaleStudent($s))->count();

                $ppfHM = $students->filter(fn($s) => in_array($s->status ?? '', ['deceased', 'prescribed', 'falecido']))->count();
                $ppfM = $students->filter(fn($s) => in_array($s->status ?? '', ['deceased', 'prescribed', 'falecido']) && $isFemaleStudent($s))->count();

                // Grades for all classes of this grade level
                $classGrades = Grade::withoutGlobalScopes()
                    ->where('school_id', $selectedSchool->id)
                    ->whereIn('class_id', $classIds)
                    ->where('year', $year)
                    ->whereNotNull('grade')
                    ->get();

                // Get subjects for this grade level
                $gradeSubjects = Subject::withoutGlobalScopes()
                    ->whereHas('classes', function ($q) use ($classIds) {
                        $q->whereIn('class_id', $classIds);
                    })
                    ->get();

                if ($gradeSubjects->isEmpty()) {
                    $gradeSubjects = Subject::active()->get();
                }

                $evalHM = 0;
                $evalM = 0;
                $passedHM = 0;
                $passedM = 0;

                foreach ($students as $student) {
                    $isFemale = $isFemaleStudent($student);
                    $subjectMts = [];

                    foreach ($classGroup as $c) {
                        foreach ($gradeSubjects as $subj) {
                            $mt = Grade::calculateMT($student->id, $subj->id, $c->id, $term, $classGrades);
                            if ($mt !== null) {
                                $subjectMts[] = $mt;
                            }
                        }
                    }

                    if (empty($subjectMts)) {
                        $stGrades = $classGrades->where('student_id', $student->id);
                        if ($stGrades->isNotEmpty()) {
                            $subjectMts[] = round($stGrades->avg('grade'), 1);
                        }
                    }

                    if (!empty($subjectMts)) {
                        $evalHM++;
                        if ($isFemale) {
                            $evalM++;
                        }

                        $studentAvg = round(array_sum($subjectMts) / count($subjectMts), 1);
                        if ($studentAvg >= $passingThreshold) {
                            $passedHM++;
                            if ($isFemale) {
                                $passedM++;
                            }
                        }
                    }
                }

                $failedHM = max(0, $evalHM - $passedHM);
                $failedM = max(0, $evalM - $passedM);

                $rateHM = $evalHM > 0 ? round(($passedHM / $evalHM) * 100, 1) : 0.0;
                $rateM = $evalM > 0 ? round(($passedM / $evalM) * 100, 1) : 0.0;
                $desistRateHM = $totalHM > 0 ? round(($desistHM / $totalHM) * 100, 1) : 0.0;
                $desistRateM = $totalM > 0 ? round(($desistM / $totalM) * 100, 1) : 0.0;

                $rowItem = [
                    'is_ciclo' => false,
                    'grade_level' => $gradeLevel,
                    'grade_level_name' => $gradeLevelName,
                    'turmas_count' => count($classIds),
                    'total_hm' => $totalHM, 'total_m' => $totalM,
                    'desist_hm' => $desistHM, 'desist_m' => $desistM,
                    'transf_hm' => $transfHM, 'transf_m' => $transfM,
                    'anulou_hm' => $anulouHM, 'anulou_m' => $anulouM,
                    'ppf_hm' => $ppfHM, 'ppf_m' => $ppfM,
                    'eval_hm' => $evalHM, 'eval_m' => $evalM,
                    'passed_hm' => $passedHM, 'passed_m' => $passedM,
                    'failed_hm' => $failedHM, 'failed_m' => $failedM,
                    'rate_hm' => $rateHM, 'rate_m' => $rateM,
                    'desist_rate_hm' => $desistRateHM, 'desist_rate_m' => $desistRateM,
                ];

                $classRows[] = $rowItem;

                if ($gradeLevel === 0) {
                    $preschoolRows[] = $rowItem;
                } else {
                    $isCiclo1 = ($gradeLevel >= 1 && $gradeLevel <= 3) || ($gradeLevel >= 7 && $gradeLevel <= 9);
                    if ($isCiclo1) {
                        $ciclo1Rows[] = $rowItem;
                    } else {
                        $ciclo2Rows[] = $rowItem;
                    }
                }

                // Add to Ciclo 1 vs Ciclo 2
                if ($gradeLevel > 0) {
                    $isCiclo1 = ($gradeLevel >= 1 && $gradeLevel <= 3) || ($gradeLevel >= 7 && $gradeLevel <= 9);
                    
                    $targetCiclo = &$ciclo1Data;
                    if (!$isCiclo1) {
                        $targetCiclo = &$ciclo2Data;
                    }

                    $targetCiclo['turmas_count'] += count($classIds);
                    $targetCiclo['total_hm'] += $totalHM; $targetCiclo['total_m'] += $totalM;
                    $targetCiclo['desist_hm'] += $desistHM; $targetCiclo['desist_m'] += $desistM;
                    $targetCiclo['transf_hm'] += $transfHM; $targetCiclo['transf_m'] += $transfM;
                    $targetCiclo['anulou_hm'] += $anulouHM; $targetCiclo['anulou_m'] += $anulouM;
                    $targetCiclo['ppf_hm'] += $ppfHM; $targetCiclo['ppf_m'] += $ppfM;
                    $targetCiclo['eval_hm'] += $evalHM; $targetCiclo['eval_m'] += $evalM;
                    $targetCiclo['passed_hm'] += $passedHM; $targetCiclo['passed_m'] += $passedM;
                    $targetCiclo['failed_hm'] += $failedHM; $targetCiclo['failed_m'] += $failedM;
                }

                // Add to Total Geral
                $totalGeralData['turmas_count'] += count($classIds);
                $totalGeralData['total_hm'] += $totalHM; $totalGeralData['total_m'] += $totalM;
                $totalGeralData['desist_hm'] += $desistHM; $totalGeralData['desist_m'] += $desistM;
                $totalGeralData['transf_hm'] += $transfHM; $totalGeralData['transf_m'] += $transfM;
                $totalGeralData['anulou_hm'] += $anulouHM; $totalGeralData['anulou_m'] += $anulouM;
                $totalGeralData['ppf_hm'] += $ppfHM; $totalGeralData['ppf_m'] += $ppfM;
                $totalGeralData['eval_hm'] += $evalHM; $totalGeralData['eval_m'] += $evalM;
                $totalGeralData['passed_hm'] += $passedHM; $totalGeralData['passed_m'] += $passedM;
                $totalGeralData['failed_hm'] += $failedHM; $totalGeralData['failed_m'] += $failedM;
            }
        }

        // Calculate rates for Ciclos and Total Geral
        foreach ([&$ciclo1Data, &$ciclo2Data, &$totalGeralData] as &$cData) {
            $cData['rate_hm'] = $cData['eval_hm'] > 0 ? round(($cData['passed_hm'] / $cData['eval_hm']) * 100, 1) : 0.0;
            $cData['rate_m'] = $cData['eval_m'] > 0 ? round(($cData['passed_m'] / $cData['eval_m']) * 100, 1) : 0.0;
            $cData['desist_rate_hm'] = $cData['total_hm'] > 0 ? round(($cData['desist_hm'] / $cData['total_hm']) * 100, 1) : 0.0;
            $cData['desist_rate_m'] = $cData['total_m'] > 0 ? round(($cData['desist_m'] / $cData['total_m']) * 100, 1) : 0.0;
        }

        // 3. QUADRO 2: Subject performance for selected class
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

        return compact(
            'schools',
            'selectedSchool',
            'schoolTeachingType',
            'provinces',
            'districts',
            'zips',
            'selectedProvinceId',
            'selectedDistrictId',
            'selectedZipId',
            'educationLevel',
            'allClasses',
            'selectedClass',
            'year',
            'term',
            'classRows',
            'ciclo1Rows',
            'ciclo2Rows',
            'preschoolRows',
            'ciclo1Data',
            'ciclo2Data',
            'totalGeralData',
            'mapData',
            'passingThreshold'
        );
    }
}
