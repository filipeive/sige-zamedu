<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PautaController extends Controller
{
    private function checkTeacherClassAccess(ClassRoom $class): void
    {
        $user = auth()->user();
        if ($user && $user->hasRole('teacher')) {
            $teacher = \App\Models\Teacher::where('user_id', $user->id)->first();
            if (! $teacher || ! $teacher->canAccessClass($class->id)) {
                abort(403, 'Acesso não autorizado: Você só pode visualizar a pauta das turmas que lecciona.');
            }
        }
    }

    private function getScopeData(?int $restrictToClassId = null): array
    {
        $user = auth()->user();
        $isTeacher = $user && $user->hasRole('teacher');

        if ($isTeacher) {
            $teacher = \App\Models\Teacher::where('user_id', $user->id)->first();
            if (! $teacher) {
                abort(403, 'Perfil de professor não encontrado.');
            }
            $classIds = $teacher->allTeachingClassIds();
            $availableClasses = ClassRoom::whereIn('id', $classIds)
                ->orderBy('grade_level')->orderBy('name')->get();
        } else {
            $availableClasses = ClassRoom::active()->orderBy('grade_level')->orderBy('name')->get();
        }

        $availableSubjects = \App\Models\Subject::active()->orderBy('name')->get();

        return [
            'availableClasses' => $availableClasses,
            'availableSubjects' => $availableSubjects,
            'isTeacher' => (bool) $isTeacher,
        ];
    }

    private function resolveSubjectsForTeacher(ClassRoom $class, Request $request)
    {
        $user = auth()->user();
        if (! $user || ! $user->hasRole('teacher')) {
            return $class->subjects->isEmpty() ? Subject::active()->get() : $class->subjects;
        }

        $teacher = \App\Models\Teacher::where('user_id', $user->id)->first();
        if (! $teacher) {
            abort(403, 'Perfil de professor não encontrado.');
        }

        if ($request->filled('subject_id')) {
            $subjectId = (int) $request->get('subject_id');
            if ($class->teacher_id === $teacher->id) {
                return Subject::where('id', $subjectId)->get();
            }
            $teacherSubjects = $teacher->taughtSubjectsInClass($class);
            if (! $teacherSubjects->contains('id', $subjectId)) {
                abort(403, 'Não lecciona esta disciplina nesta turma.');
            }
            return $teacherSubjects->where('id', $subjectId)->values();
        }

        return $class->subjects->isEmpty() ? Subject::active()->get() : $class->subjects;
    }

    public function index(Request $request)
    {
        $this->authorize('view_grades');
        $classId = $request->get('class_id');
        if ($classId) {
            $class = ClassRoom::find($classId);
            if ($class) {
                $this->checkTeacherClassAccess($class);
                return redirect()->route('pautas.trimestral', array_merge(['class' => $class->id], $request->except('class_id')));
            }
        }

        $user = auth()->user();
        if ($user && $user->hasRole('teacher')) {
            $teacher = \App\Models\Teacher::where('user_id', $user->id)->first();
            $firstClass = $teacher ? ClassRoom::whereIn('id', $teacher->allTeachingClassIds())->active()->orderBy('grade_level')->orderBy('name')->first() : null;
            if ($firstClass) {
                return redirect()->route('pautas.trimestral', array_merge(['class' => $firstClass->id], $request->query()));
            }
            return redirect()->route('teacher.dashboard')->with('info', 'Nenhuma turma atribuída encontrada para gerar pauta.');
        }

        $firstClass = ClassRoom::active()->orderBy('grade_level')->orderBy('name')->first();
        if ($firstClass) {
            return redirect()->route('pautas.trimestral', array_merge(['class' => $firstClass->id], $request->query()));
        }
        return redirect()->route('grades.dashboard')->with('info', 'Nenhuma turma activa encontrada para gerar pauta.');
    }

    public function trimestral(ClassRoom $class, Request $request)
    {
        $this->authorize('view_grades');
        $this->checkTeacherClassAccess($class);

        if ($request->filled('class_id') && (int) $request->class_id !== (int) $class->id) {
            return redirect()->route('pautas.trimestral', array_merge(['class' => $request->class_id], $request->except('class_id')));
        }

        $termRaw = $request->get('term', 1);
        $year = (int) $request->get('year', current_school_year());

        if ($termRaw === 'all' || $termRaw === 'final') {
            return redirect()->route('pautas.final', ['class' => $class->id, 'year' => $year]);
        }

        $term = (int) $termRaw;
        $subjects = $this->resolveSubjectsForTeacher($class, $request);
        $classData = $this->buildPautaData($class, $term, $year, 'trimestral', $subjects);
        $scope = $this->getScopeData();

        return view('pautas.trimestral', array_merge($classData, [
            'class' => $class,
            'term' => $term,
            'year' => $year,
            'availableClasses' => $scope['availableClasses'],
            'availableSubjects' => $scope['availableSubjects'],
            'isTeacher' => $scope['isTeacher'],
            'selectedSubjectId' => (int) $request->get('subject_id'),
        ]));
    }

    public function anual(ClassRoom $class, Request $request)
    {
        $this->authorize('view_grades');
        $this->checkTeacherClassAccess($class);

        if ($request->filled('class_id') && (int) $request->class_id !== (int) $class->id) {
            return redirect()->route('pautas.anual', array_merge(['class' => $request->class_id], $request->except('class_id')));
        }

        $year = (int) $request->get('year', current_school_year());
        $subjects = $this->resolveSubjectsForTeacher($class, $request);
        $classData = $this->buildPautaData($class, null, $year, 'anual', $subjects);
        $scope = $this->getScopeData();

        return view('pautas.anual', array_merge($classData, [
            'class' => $class,
            'year' => $year,
            'availableClasses' => $scope['availableClasses'],
            'availableSubjects' => $scope['availableSubjects'],
            'isTeacher' => $scope['isTeacher'],
            'selectedSubjectId' => (int) $request->get('subject_id'),
        ]));
    }

    public function final(ClassRoom $class, Request $request)
    {
        $this->authorize('view_grades');
        $this->checkTeacherClassAccess($class);

        if ($request->filled('class_id') && (int) $request->class_id !== (int) $class->id) {
            return redirect()->route('pautas.final', array_merge(['class' => $request->class_id], $request->except('class_id')));
        }

        $year = (int) $request->get('year', current_school_year());
        $subjects = $this->resolveSubjectsForTeacher($class, $request);
        $classData = $this->buildPautaData($class, null, $year, 'final', $subjects);
        $scope = $this->getScopeData();

        return view('pautas.final', array_merge($classData, [
            'class' => $class,
            'year' => $year,
            'availableClasses' => $scope['availableClasses'],
            'availableSubjects' => $scope['availableSubjects'],
            'isTeacher' => $scope['isTeacher'],
            'selectedSubjectId' => (int) $request->get('subject_id'),
        ]));
    }

    public function exame(ClassRoom $class, Request $request)
    {
        $this->authorize('view_grades');
        $this->checkTeacherClassAccess($class);

        if ($request->filled('class_id') && (int) $request->class_id !== (int) $class->id) {
            return redirect()->route('pautas.exame', array_merge(['class' => $request->class_id], $request->except('class_id')));
        }

        $year = (int) $request->get('year', current_school_year());
        $subjects = $this->resolveSubjectsForTeacher($class, $request);
        $classData = $this->buildPautaData($class, null, $year, 'final', $subjects);
        $scope = $this->getScopeData();

        $juryPresident = setting("jury_class_{$class->id}_president", '');
        $juryExaminer1 = setting("jury_class_{$class->id}_examiner1", '');
        $juryExaminer2 = setting("jury_class_{$class->id}_examiner2", '');
        $juryCode = setting("jury_class_{$class->id}_code", "Júri Nº {$class->name}");

        return view('pautas.exame', array_merge($classData, [
            'class' => $class,
            'year' => $year,
            'availableClasses' => $scope['availableClasses'],
            'availableSubjects' => $scope['availableSubjects'],
            'isTeacher' => $scope['isTeacher'],
            'selectedSubjectId' => (int) $request->get('subject_id'),
            'juryPresident' => $juryPresident,
            'juryExaminer1' => $juryExaminer1,
            'juryExaminer2' => $juryExaminer2,
            'juryCode' => $juryCode,
        ]));
    }

    public function updateJury(ClassRoom $class, Request $request)
    {
        $this->authorize('view_grades');

        $validated = $request->validate([
            'jury_president' => 'nullable|string|max:191',
            'jury_examiner1' => 'nullable|string|max:191',
            'jury_examiner2' => 'nullable|string|max:191',
            'jury_code' => 'nullable|string|max:191',
        ]);

        \App\Models\Setting::updateOrCreate(['key' => "jury_class_{$class->id}_president"], ['value' => $validated['jury_president'] ?? '']);
        \App\Models\Setting::updateOrCreate(['key' => "jury_class_{$class->id}_examiner1"], ['value' => $validated['jury_examiner1'] ?? '']);
        \App\Models\Setting::updateOrCreate(['key' => "jury_class_{$class->id}_examiner2"], ['value' => $validated['jury_examiner2'] ?? '']);
        \App\Models\Setting::updateOrCreate(['key' => "jury_class_{$class->id}_code"], ['value' => $validated['jury_code'] ?? '']);

        \Illuminate\Support\Facades\Cache::forget("setting_jury_class_{$class->id}_president");
        \Illuminate\Support\Facades\Cache::forget("setting_jury_class_{$class->id}_examiner1");
        \Illuminate\Support\Facades\Cache::forget("setting_jury_class_{$class->id}_examiner2");
        \Illuminate\Support\Facades\Cache::forget("setting_jury_class_{$class->id}_code");

        return back()->with('success', 'Júri de Exame atualizado com sucesso!');
    }

    public function exportPdf(ClassRoom $class, Request $request)
    {
        $this->authorize('view_grades');
        $this->checkTeacherClassAccess($class);

        $type = $request->get('type', 'trimestral');
        $term = (int) $request->get('term', 1);
        $year = (int) $request->get('year', current_school_year());
        $paperInput = strtolower($request->get('paper', 'auto'));

        $subjects = $this->resolveSubjectsForTeacher($class, $request);
        $data = $this->buildPautaData($class, $term, $year, $type, $subjects);
        $data['class'] = $class;
        $data['type'] = $type;
        $data['term'] = $term;
        $data['year'] = $year;

        $paper = ($paperInput === 'auto') ? (count($data['subjects']) > 7 ? 'a3' : 'a4') : $paperInput;
        $data['paper'] = $paper;

        $pdf = Pdf::loadView('pautas.pdf', $data)->setPaper($paper, 'landscape');
        return $pdf->download("Pauta_{$type}_Turma_{$class->name}_{$year}.pdf");
    }

    public function exportExcel(ClassRoom $class, Request $request)
    {
        $this->authorize('view_grades');
        $this->checkTeacherClassAccess($class);

        $type = $request->get('type', 'trimestral');
        $term = (int) $request->get('term', 1);
        $year = (int) $request->get('year', current_school_year());

        $subjects = $this->resolveSubjectsForTeacher($class, $request);
        $data = $this->buildPautaData($class, $term, $year, $type, $subjects);
        $subjects = $data['subjects'];
        $matrix = $data['matrix'];

        $filename = "Pauta_{$type}_Turma_{$class->name}_{$year}.csv";
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($subjects, $matrix, $type) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            $header1 = ['Nome do Aluno', 'N.º Aluno'];
            foreach ($subjects as $subject) {
                $subName = $subject->code ?? $subject->name;
                array_push($header1, $subName, '', '', '', '', '');
            }
            array_push($header1, 'Média Geral', 'Resultado Final');
            fputcsv($file, $header1, ';');

            foreach ($matrix as $row) {
                $student = $row['student'];
                $line = [
                    $student->first_name . ' ' . $student->last_name,
                    $student->student_number ?? '',
                ];

                foreach ($subjects as $subject) {
                    $subData = $row['subjects'][$subject->id] ?? [];
                    if ($type === 'trimestral') {
                        $line[] = $subData['acs1'] ?? '-';
                        $line[] = $subData['acs2'] ?? '-';
                        $line[] = $subData['acs3'] ?? '-';
                        $line[] = $subData['macs'] ?? '-';
                        $line[] = $subData['acp'] ?? '-';
                        $line[] = $subData['mt'] ?? '-';
                    } else {
                        $line[] = $subData['mt1'] ?? '-';
                        $line[] = $subData['mt2'] ?? '-';
                        $line[] = $subData['mt3'] ?? '-';
                        $line[] = $subData['mf'] ?? '-';
                        $line[] = $subData['exam'] ?? '-';
                        $line[] = $subData['mfd'] ?? '-';
                    }
                }

                $line[] = $row['overall_average'] > 0 ? number_format($row['overall_average'], 1) : '-';
                $line[] = $row['final_status'];

                fputcsv($file, $line, ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function buildPautaData(ClassRoom $class, ?int $term, int $year, string $type, $subjects = null): array
    {
        $allActiveClasses = ClassRoom::active()->orderBy('grade_level')->orderBy('name')->get();

        $students = Student::whereHas('enrollments', function ($q) use ($class) {
            $q->where('class_id', $class->id);
        })
        ->orderBy('first_name')
        ->orderBy('last_name')
        ->get();

        if ($students->isEmpty()) {
            $students = $class->students()->orderBy('first_name')->get();
        }

        if ($subjects === null) {
            $subjects = $class->subjects->isEmpty()
                ? Subject::active()->get()
                : $class->subjects;
        }

        $allGrades = Grade::whereIn('student_id', $students->pluck('id'))
            ->where('class_id', $class->id)
            ->where('year', $year)
            ->get();

        $matrix = [];
        $passingThreshold = (float) setting('passing_grade', 9.5);
        $isExamClass = in_array((int) $class->grade_level, [9, 10, 12]);

        $macsWeight = (float) setting('macs_weight', 2);
        $acpWeight = (float) setting('acp_weight_in_mt', 1);
        $totalWeight = $macsWeight + $acpWeight;

        foreach ($students as $student) {
            $studentData = [
                'student' => $student,
                'subjects' => [],
                'overall_average' => 0,
                'final_status' => 'Pendente',
            ];

            $totalAverages = [];
            $failedSubjectsCount = 0;

            foreach ($subjects as $subject) {
                $studentSubjectGrades = $allGrades->filter(function ($g) use ($student, $subject) {
                    return (int) $g->student_id === $student->id && (int) $g->subject_id === $subject->id;
                });

                if ($type === 'trimestral') {
                    $termGrades = $studentSubjectGrades->where('term', $term);

                    $acs1 = $termGrades->where('assessment_type', Grade::TYPE_ACS1)->first()?->grade
                            ?? $termGrades->whereIn('assessment_type', ['continuous', 'test'])->first()?->grade;
                    $acs2 = $termGrades->where('assessment_type', Grade::TYPE_ACS2)->first()?->grade;
                    $acs3 = $termGrades->where('assessment_type', Grade::TYPE_ACS3)->first()?->grade;

                    $validAcs = array_filter([$acs1, $acs2, $acs3], fn($v) => $v !== null);
                    $macs = count($validAcs) > 0 ? array_sum($validAcs) / count($validAcs) : null;
                    $acp = $termGrades->whereIn('assessment_type', [Grade::TYPE_ACP, 'exam', 'ACF', 'continuous_test'])->first()?->grade;

                    $mt = null;
                    if ($macs !== null && $acp !== null) {
                        $mt = (($macs * $macsWeight) + ($acp * $acpWeight)) / $totalWeight;
                    } elseif ($macs !== null) {
                        $mt = $macs;
                    } elseif ($acp !== null) {
                        $mt = $acp;
                    }

                    $studentData['subjects'][$subject->id] = [
                        'acs1' => $acs1 !== null ? round($acs1, 1) : '-',
                        'acs2' => $acs2 !== null ? round($acs2, 1) : '-',
                        'acs3' => $acs3 !== null ? round($acs3, 1) : '-',
                        'macs' => $macs !== null ? round($macs, 1) : '-',
                        'acp' => $acp !== null ? round($acp, 1) : '-',
                        'mt' => $mt !== null ? round($mt, 1) : '-',
                    ];

                    if ($mt !== null) {
                        $totalAverages[] = $mt;
                        if ($mt < $passingThreshold) {
                            $failedSubjectsCount++;
                        }
                    }
                } else {
                    // Anual ou Final Consolidada Moçambicana (SNE)
                    $mts = [];
                    for ($t = 1; $t <= 3; $t++) {
                        $tGrades = $studentSubjectGrades->where('term', $t);
                        $tAcs = array_filter([
                            $tGrades->where('assessment_type', Grade::TYPE_ACS1)->first()?->grade ?? $tGrades->whereIn('assessment_type', ['continuous', 'test'])->first()?->grade,
                            $tGrades->where('assessment_type', Grade::TYPE_ACS2)->first()?->grade,
                            $tGrades->where('assessment_type', Grade::TYPE_ACS3)->first()?->grade,
                        ], fn($v) => $v !== null);

                        $tMacs = count($tAcs) > 0 ? array_sum($tAcs) / count($tAcs) : null;
                        $tAcp = $tGrades->whereIn('assessment_type', [Grade::TYPE_ACP, 'exam', 'ACF', 'continuous_test'])->first()?->grade;

                        if ($tMacs !== null && $tAcp !== null) {
                            $mts[$t] = (($tMacs * $macsWeight) + ($tAcp * $acpWeight)) / $totalWeight;
                        } elseif ($tMacs !== null) {
                            $mts[$t] = $tMacs;
                        } elseif ($tAcp !== null) {
                            $mts[$t] = $tAcp;
                        } else {
                            $mts[$t] = null;
                        }
                    }

                    $validMts = array_filter($mts, fn($v) => $v !== null);
                    $mf = count($validMts) === 3
                        ? array_sum($validMts) / 3
                        : (count($validMts) > 0 ? array_sum($validMts) / count($validMts) : null);

                    $ne = $studentSubjectGrades->where('assessment_type', 'exam')->first()?->grade;
                    $acf = $studentSubjectGrades->where('assessment_type', 'ACF')->first()?->grade;
                    $neEficaz = $acf !== null ? max($ne ?? 0, $acf) : $ne;

                    if ($isExamClass && $neEficaz !== null && $mf !== null) {
                        $mfd = round(($mf + $neEficaz) / 2);
                    } else {
                        $mfd = $mf !== null ? round($mf) : null;
                    }

                    $studentData['subjects'][$subject->id] = [
                        'mt1' => $mts[1] !== null ? round($mts[1], 1) : '-',
                        'mt2' => $mts[2] !== null ? round($mts[2], 1) : '-',
                        'mt3' => $mts[3] !== null ? round($mts[3], 1) : '-',
                        'mf' => $mf !== null ? round($mf, 1) : '-',
                        'exam' => $neEficaz !== null ? round($neEficaz, 1) : '-',
                        'mfd' => $mfd !== null ? round($mfd, 1) : '-',
                    ];

                    if ($mfd !== null) {
                        $totalAverages[] = $mfd;
                        if ($mfd < 10) {
                            $failedSubjectsCount++;
                        }
                    }
                }
            }

            $totalSubjectsCount = count($subjects);
            $gradedCount = count($totalAverages);

            if ($gradedCount > 0) {
                $studentData['overall_average'] = round(array_sum($totalAverages) / count($subjects), 1);
                if ($type === 'trimestral') {
                    $studentData['final_status'] = $studentData['overall_average'] >= $passingThreshold ? 'Aprovado' : 'Retido';
                } else {
                    $studentData['final_status'] = ($studentData['overall_average'] >= 10 && $failedSubjectsCount <= 2) ? 'Aprovado' : 'Retido';
                }
            } else {
                $studentData['overall_average'] = 0;
                $studentData['final_status'] = 'Em Curso';
            }

            $matrix[] = $studentData;
        }

        $stats = $this->calculatePautaStats($matrix, $subjects, $type);

        return [
            'classes' => $allActiveClasses,
            'subjects' => $subjects,
            'matrix' => $matrix,
            'stats' => $stats,
            'isExamClass' => $isExamClass,
            'calculationRules' => [
                'macs_calculation_method' => setting('macs_calculation_method', 'launched_only'),
                'default_acs_count' => (int) setting('default_acs_count', 3),
                'macs_weight' => $macsWeight,
                'acp_weight_in_mt' => $acpWeight,
                'passing_grade' => $passingThreshold,
            ],
        ];
    }

    private function calculatePautaStats(array $matrix, $subjects, string $type): array
    {
        $stats = [
            'global' => ['total' => count($matrix), 'approved' => 0, 'failed' => 0],
            'subjects' => [],
        ];

        foreach ($matrix as $row) {
            if ($row['final_status'] === 'Aprovado') {
                $stats['global']['approved']++;
            } else {
                $stats['global']['failed']++;
            }
        }

        foreach ($subjects as $subject) {
            $stats['subjects'][$subject->id] = [
                'hm_evaluated' => 0, 'm_evaluated' => 0,
                'hm_positives' => 0, 'm_positives' => 0,
                'hm_negatives' => 0, 'm_negatives' => 0,
                'hm_perc' => 0, 'm_perc' => 0,
            ];

            foreach ($matrix as $row) {
                $student = $row['student'];
                $gender = strtolower($student->gender ?? 'male');
                $isFemale = ($gender === 'female' || $gender === 'f' || $gender === 'mulher');

                $subData = $row['subjects'][$subject->id] ?? null;
                if (! $subData) continue;

                $val = ($type === 'trimestral') ? ($subData['mt'] ?? '-') : ($subData['mfd'] ?? '-');
                if (! is_numeric($val)) continue;

                $gradeValue = (float) $val;
                $isPositive = $gradeValue >= 9.5;

                $stats['subjects'][$subject->id]['hm_evaluated']++;
                if ($isFemale) {
                    $stats['subjects'][$subject->id]['m_evaluated']++;
                }

                if ($isPositive) {
                    $stats['subjects'][$subject->id]['hm_positives']++;
                    if ($isFemale) {
                        $stats['subjects'][$subject->id]['m_positives']++;
                    }
                } else {
                    $stats['subjects'][$subject->id]['hm_negatives']++;
                    if ($isFemale) {
                        $stats['subjects'][$subject->id]['m_negatives']++;
                    }
                }
            }

            $hmEval = $stats['subjects'][$subject->id]['hm_evaluated'];
            $mEval = $stats['subjects'][$subject->id]['m_evaluated'];

            $stats['subjects'][$subject->id]['hm_perc'] = $hmEval > 0
                ? round(($stats['subjects'][$subject->id]['hm_positives'] / $hmEval) * 100, 1)
                : 0;

            $stats['subjects'][$subject->id]['m_perc'] = $mEval > 0
                ? round(($stats['subjects'][$subject->id]['m_positives'] / $mEval) * 100, 1)
                : 0;
        }

        return $stats;
    }

    /**
     * Atualizar regras de cálculo de notas (MACS, MT, Pesos e Limiar MINEDH).
     */
    public function updateCalculationRules(Request $request)
    {
        $validated = $request->validate([
            'macs_calculation_method' => 'required|in:launched_only,fixed_count',
            'default_acs_count' => 'required|integer|min:1|max:6',
            'macs_weight' => 'required|numeric|min:0.1|max:10',
            'acp_weight_in_mt' => 'required|numeric|min:0.1|max:10',
            'passing_grade' => 'required|numeric|min:0|max:20',
        ]);

        foreach ($validated as $key => $value) {
            \App\Models\Setting::updateOrCreate(
                ['key' => $key],
                ['value' => (string) $value]
            );
            \Illuminate\Support\Facades\Cache::forget("setting_{$key}");
        }

        return back()->with('success', 'Regras e fórmula de cálculo de notas atualizadas com sucesso!');
    }
}
