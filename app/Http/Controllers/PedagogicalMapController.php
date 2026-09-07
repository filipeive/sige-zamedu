<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;

class PedagogicalMapController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view_grades');

        $year = (int) $request->get('year', current_school_year());
        $term = (int) $request->get('term', 1);
        $classId = $request->get('class_id');

        $classes = ClassRoom::active()->orderBy('grade_level')->orderBy('name')->get();
        $selectedClass = $classId ? ClassRoom::find($classId) : $classes->first();

        if (! $selectedClass && $classes->isNotEmpty()) {
            $selectedClass = $classes->first();
        }

        $mapData = [];
        $passingThreshold = (float) setting('passing_grade', 9.5);

        if ($selectedClass) {
            $students = Student::whereHas('enrollments', function ($q) use ($selectedClass) {
                $q->where('class_id', $selectedClass->id);
            })->orderBy('first_name')->get();

            if ($students->isEmpty()) {
                $students = $selectedClass->students;
            }

            $subjects = $selectedClass->subjects->isEmpty()
                ? Subject::active()->get()
                : $selectedClass->subjects;

            $allGrades = Grade::whereIn('student_id', $students->pluck('id'))
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
            'classes',
            'selectedClass',
            'year',
            'term',
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
