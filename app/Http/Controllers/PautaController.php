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
                abort(403, 'Acesso não autorizado: Você só pode visualizar a pauta das turmas que lecciona (regente ou como professor de disciplina).');
            }
        }
    }

    /**
     * Se o utilizador é professor e foi passado ?subject_id=, verificar que lecciona
     * essa disciplina na turma. Caso contrário, devolve a lista de subjects que lecciona.
     *
     * @return \Illuminate\Support\Collection<int,\App\Models\Subject>
     */
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

        // Disciplinas que este professor lecciona nesta turma
        $teacherSubjects = $teacher->taughtSubjectsInClass($class);

        // Se pediu filtro por subject_id, validar
        if ($request->filled('subject_id')) {
            $subjectId = (int) $request->get('subject_id');
            if (! $teacherSubjects->contains('id', $subjectId)) {
                abort(403, 'Não lecciona esta disciplina nesta turma.');
            }
            return $teacherSubjects->where('id', $subjectId)->values();
        }

        // Se não pediu filtro, devolver apenas as que lecciona (escopo de segurança)
        return $teacherSubjects;
    }

    /**
     * Index de Pautas - Redirecciona para a pauta trimestral da primeira turma activa.
     */
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
            // Turmas que o professor lecciona (regente OU disciplina atribuída)
            $firstClass = $teacher
                ? ClassRoom::whereIn('id', $teacher->allTeachingClassIds())->active()->orderBy('grade_level')->orderBy('name')->first()
                : null;
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

    /**
     * Pauta Trimestral de uma Turma.
     */
    public function trimestral(ClassRoom $class, Request $request)
    {
        $this->authorize('view_grades');
        $this->checkTeacherClassAccess($class);

        $termRaw = $request->get('term', 1);
        $year = (int) $request->get('year', current_school_year());

        if ($termRaw === 'all' || $termRaw === 'final') {
            return redirect()->route('pautas.final', ['class' => $class->id, 'year' => $year]);
        }

        $term = (int) $termRaw;
        $subjects = $this->resolveSubjectsForTeacher($class, $request);
        $classData = $this->buildPautaData($class, $term, $year, 'trimestral', $subjects);

        return view('pautas.trimestral', array_merge($classData, [
            'class' => $class,
            'term' => $term,
            'year' => $year,
            'isTeacher' => auth()->user()?->hasRole('teacher') ?? false,
        ]));
    }

    /**
     * Pauta Anual Consolidada de uma Turma (MT1, MT2, MT3).
     */
    public function anual(ClassRoom $class, Request $request)
    {
        $this->authorize('view_grades');
        $this->checkTeacherClassAccess($class);

        $year = (int) $request->get('year', current_school_year());
        $subjects = $this->resolveSubjectsForTeacher($class, $request);
        $classData = $this->buildPautaData($class, null, $year, 'anual', $subjects);

        return view('pautas.anual', array_merge($classData, [
            'class' => $class,
            'year' => $year,
            'isTeacher' => auth()->user()?->hasRole('teacher') ?? false,
        ]));
    }

    /**
     * Pauta Final com Frequência, Exames e Resultados de Promoção.
     */
    public function final(ClassRoom $class, Request $request)
    {
        $this->authorize('view_grades');
        $this->checkTeacherClassAccess($class);

        $year = (int) $request->get('year', current_school_year());
        $subjects = $this->resolveSubjectsForTeacher($class, $request);
        $classData = $this->buildPautaData($class, null, $year, 'final', $subjects);

        return view('pautas.final', array_merge($classData, [
            'class' => $class,
            'year' => $year,
            'isTeacher' => auth()->user()?->hasRole('teacher') ?? false,
        ]));
    }

    /**
     * Exportação de Pauta em PDF.
     */
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

        $subjectCount = count($data['subjects']);

        if ($paperInput === 'a3') {
            $paper = 'a3';
        } elseif ($paperInput === 'a4') {
            $paper = 'a4';
        } else {
            $paper = ($subjectCount > 6 && $type === 'trimestral') || ($subjectCount > 9) ? 'a3' : 'a4';
        }

        $data['paper'] = $paper;

        $pdf = Pdf::loadView('pautas.pdf', $data)
            ->setPaper($paper, 'landscape');

        $filename = "Pauta_{$type}_Turma_{$class->name}_{$year}_{$paper}.pdf";

        return $pdf->download($filename);
    }

    /**
     * Exportação de Pauta em Excel / CSV.
     */
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
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($subjects, $matrix, $type) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            $header1 = ['Nome do Aluno', 'N.º Aluno'];
            foreach ($subjects as $subject) {
                $subName = $subject->code ?? $subject->name;
                if ($type === 'trimestral') {
                    array_push($header1, $subName, '', '', '', '', '');
                } else {
                    array_push($header1, $subName, '', '', '');
                }
            }
            $header1[] = 'Média Geral';
            $header1[] = 'Resultado Final';
            fputcsv($file, $header1, ';');

            $header2 = ['', ''];
            foreach ($subjects as $subject) {
                if ($type === 'trimestral') {
                    array_push($header2, 'ACS1', 'ACS2', 'ACS3', 'MACS', 'ACP', 'MT');
                } else {
                    array_push($header2, 'MT1', 'MT2', 'MT3', 'MF');
                }
            }
            $header2[] = '';
            $header2[] = '';
            fputcsv($file, $header2, ';');

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

    /**
     * Auxiliar para calcular e organizar a matriz da pauta.
     *
     * @param  \Illuminate\Support\Collection|null  $subjects  Quando fornecido, restringe a pauta
     *                                                         a estas disciplinas (escopo do professor).
     */
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
            $students = $class->students;
        }

        // Se não foi passado um conjunto de subjects, usar o da turma (admin/secretaria)
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
        $classStats = [
            'total_students' => $students->count(),
            'approved' => 0,
            'failed' => 0,
            'exam' => 0,
        ];

        $passingThreshold = 9.5;

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

                    $macs = Grade::calculateMACS($student->id, $subject->id, $class->id, $term, $allGrades);
                    $acp = $termGrades->whereIn('assessment_type', [Grade::TYPE_ACP, 'exam', 'ACF'])->first()?->grade;
                    $mt = Grade::calculateMT($student->id, $subject->id, $class->id, $term, $allGrades);

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
                    // Anual ou Final
                    $mt1 = Grade::calculateMT($student->id, $subject->id, $class->id, 1, $allGrades);
                    $mt2 = Grade::calculateMT($student->id, $subject->id, $class->id, 2, $allGrades);
                    $mt3 = Grade::calculateMT($student->id, $subject->id, $class->id, 3, $allGrades);

                    $validMts = array_filter([$mt1, $mt2, $mt3], fn($v) => $v !== null);
                    $hasAllTerms = count($validMts) === 3;
                    $mf = $hasAllTerms ? round(($mt1 + $mt2 + $mt3) / 3, 1) : (!empty($validMts) ? round(array_sum($validMts) / count($validMts), 1) : null);

                    $ne = $studentSubjectGrades->where('assessment_type', 'exam')->first()?->grade;
                    $nr = $studentSubjectGrades->where('assessment_type', 'ACF')->first()?->grade;
                    $effectiveExam = $nr !== null ? max($ne ?? 0, $nr) : $ne;

                    $includeAcf = setting('include_acf_in_mfd', '0') == '1';
                    $examLevels = array_map('trim', explode(',', setting('exam_class_levels', '6,7,10,12')));
                    $isExamClass = in_array((string)$class->grade_level, $examLevels);

                    if ($includeAcf && $isExamClass && $effectiveExam !== null && $mf !== null) {
                        $acfWeight = (float) setting('acf_weight_in_mfd', 1);
                        $termsWeight = (float) setting('terms_weight_in_mfd', 3);
                        $totalW = $acfWeight + $termsWeight;
                        $mfd = round(($termsWeight * $mf + $acfWeight * $effectiveExam) / $totalW, 1);
                    } else {
                        $mfd = $mf;
                    }

                    $studentData['subjects'][$subject->id] = [
                        'mt1' => $mt1 !== null ? round($mt1, 1) : '-',
                        'mt2' => $mt2 !== null ? round($mt2, 1) : '-',
                        'mt3' => $mt3 !== null ? round($mt3, 1) : '-',
                        'mf' => $mf !== null ? round($mf, 1) : '-',
                        'exam' => $effectiveExam !== null ? round($effectiveExam, 1) : '-',
                        'mfd' => $mfd !== null ? round($mfd, 1) : '-',
                    ];

                    if ($mfd !== null) {
                        $totalAverages[] = $mfd;
                        if ($mfd < $passingThreshold) {
                            $failedSubjectsCount++;
                        }
                    }
                }
            }

            $totalSubjectsCount = count($subjects);
            $gradedCount = count($totalAverages);
            $avgMethod = setting('average_method', 'all_subjects');

            if ($gradedCount > 0) {
                if ($avgMethod === 'all_subjects') {
                    $studentData['overall_average'] = round(array_sum($totalAverages) / $totalSubjectsCount, 1);
                } else {
                    $studentData['overall_average'] = round(array_sum($totalAverages) / $gradedCount, 1);
                }
            } else {
                $studentData['overall_average'] = 0;
            }
            $studentData['graded_count'] = $gradedCount;
            $studentData['total_subjects'] = $totalSubjectsCount;

            if (!empty($totalAverages)) {
                if ($studentData['overall_average'] >= $passingThreshold && $failedSubjectsCount <= 2) {
                    $studentData['final_status'] = 'Aprovado';
                    $classStats['approved']++;
                } else {
                    $studentData['final_status'] = 'Retido';
                    $classStats['failed']++;
                }
            } else {
                $studentData['final_status'] = 'Em Curso';
            }

            $matrix[] = $studentData;
        }

        return [
            'classes' => $allActiveClasses,
            'subjects' => $subjects,
            'matrix' => $matrix,
            'stats' => $classStats,
        ];
    }

    private function calculateTermAverage($grades, int $term): ?float
    {
        $termGrades = $grades->where('term', $term);
        if ($termGrades->isEmpty()) {
            return null;
        }

        $acsGrades = $termGrades->whereIn('assessment_type', ['ACS1', 'ACS2', 'ACS3', 'test', 'assignment', 'participation']);
        $macsMethod = setting('macs_calculation_method', 'launched_only');
        if ($acsGrades->isNotEmpty()) {
            if ($macsMethod === 'fixed_count') {
                $fixedCount = max(1, (int) setting('default_acs_count', 3));
                $acs = $acsGrades->sum('grade') / $fixedCount;
            } else {
                $acs = $acsGrades->avg('grade');
            }
        } else {
            $acs = null;
        }
        $acp = $termGrades->whereIn('assessment_type', ['ACP', 'exam', 'ACF'])->first()?->grade;

        $macsWeight = (float) setting('macs_weight', 2);
        $acpWeight = (float) setting('acp_weight_in_mt', 1);
        $totalWeight = $macsWeight + $acpWeight;

        if ($acs !== null && $acp !== null) {
            return round(($macsWeight * $acs + $acpWeight * $acp) / $totalWeight, 1);
        }

        return round($termGrades->avg('grade'), 1);
    }
}
