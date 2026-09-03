<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Grade;
use App\Models\Setting;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    /**
     * Centro Inteligente de Emissão de Certidões e Certificados com Verificação de Elegibilidade.
     */
    public function index(Request $request)
    {
        $this->authorize('view_students');

        $query = Student::with(['currentEnrollment.class', 'enrollments.class'])
            ->withCount('grades')
            ->withAvg('grades', 'grade');

        // Filtro por termo de pesquisa
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('student_number', 'like', "%{$search}%");
            });
        }

        // Filtro por Turma
        if ($request->filled('class_id')) {
            $query->whereHas('currentEnrollment', function ($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        // Filtro de Elegibilidade inteligente
        if ($request->filled('eligibility')) {
            if ($request->eligibility === 'with_grades') {
                $query->has('grades');
            } elseif ($request->eligibility === 'without_grades') {
                $query->doesntHave('grades');
            } elseif ($request->eligibility === 'approved') {
                $query->where(function ($q) {
                    $q->where('status', 'graduated')
                      ->orWhereHas('grades', function ($g) {
                          $g->selectRaw('student_id, AVG(grade) as avg_grade')
                            ->groupBy('student_id')
                            ->havingRaw('avg_grade >= 10');
                      });
                });
            }
        }

        // Estatísticas para KPI Cards
        $totalStudents = Student::count();
        $studentsWithGrades = Student::has('grades')->count();
        $pendingStudents = Student::doesntHave('grades')->count();
        $approvedStudents = Student::where('status', 'graduated')
            ->orWhereHas('grades', function ($g) {
                $g->selectRaw('student_id, AVG(grade) as avg_grade')
                  ->groupBy('student_id')
                  ->havingRaw('avg_grade >= 10');
            })->count();

        $students = $query->orderBy('first_name')->paginate(15);
        $classes = ClassRoom::active()->orderBy('name')->get();

        return view('certificates.index', compact(
            'students',
            'classes',
            'totalStudents',
            'studentsWithGrades',
            'approvedStudents',
            'pendingStudents'
        ));
    }

    /**
     * Emitir Certidão de Habilitações Literárias (MINEDH Moçambique).
     */
    public function certidao(Student $student)
    {
        $this->authorize('view_students');

        // Validação de segurança: Impede documento vazio se não houver notas
        $gradesCount = Grade::where('student_id', $student->id)->count();
        if ($gradesCount === 0) {
            return redirect()->back()
                ->with('error', "Impossível emitir Certidão: O(A) estudante {$student->full_name} não possui notas ou avaliações registadas no sistema.");
        }

        $student->load(['enrollments.class', 'parent']);
        $schoolName = Setting::get('school_name', 'ESCOLA SECUNDÁRIA VISIONÁRIOS');
        $province = Setting::get('school_province', Setting::get('province', 'ZAMBÉZIA'));
        $district = Setting::get('school_district', Setting::get('district', 'NAMACURRA'));
        $directorName = Setting::get('director_name', 'O Director da Escola');
        $secretaryName = Setting::get('secretary_name', 'O Chefe da Secretaria');

        $grades = Grade::where('student_id', $student->id)
            ->with('subject')
            ->get()
            ->groupBy('subject_id');

        $subjectsData = [];
        $totalSum = 0;
        $subjectCount = 0;

        foreach ($grades as $subjectId => $subGrades) {
            $subName = $subGrades->first()->subject->name ?? 'Disciplina';
            $avgGrade = round($subGrades->avg('grade'), 1);
            $subjectsData[] = [
                'name' => $subName,
                'grade' => $avgGrade,
                'grade_words' => $this->gradeToWords($avgGrade),
            ];
            $totalSum += $avgGrade;
            $subjectCount++;
        }

        $finalAverage = $subjectCount > 0 ? round($totalSum / $subjectCount, 1) : 0;
        $finalAverageWords = $this->gradeToWords($finalAverage);

        $pdf = Pdf::loadView('certificates.pdf_certidao', compact(
            'student',
            'schoolName',
            'province',
            'district',
            'directorName',
            'secretaryName',
            'subjectsData',
            'finalAverage',
            'finalAverageWords'
        ));

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("Certidao_Habilitacoes_{$student->student_number}.pdf");
    }

    /**
     * Emitir Certificado Oficial de Conclusão do Ensino (MINEDH).
     */
    public function certificado(Student $student)
    {
        $this->authorize('view_students');

        // Validação de segurança: Impede documento de conclusão vazio se não houver notas
        $gradesCount = Grade::where('student_id', $student->id)->count();
        if ($gradesCount === 0) {
            return redirect()->back()
                ->with('error', "Impossível emitir Certificado de Conclusão: O(A) estudante {$student->full_name} não possui avaliações lançadas.");
        }

        $student->load(['enrollments.class']);
        $schoolName = Setting::get('school_name', 'ESCOLA SECUNDÁRIA VISIONÁRIOS');
        $province = Setting::get('school_province', Setting::get('province', 'ZAMBÉZIA'));
        $district = Setting::get('school_district', Setting::get('district', 'NAMACURRA'));
        $directorName = Setting::get('director_name', 'O Director da Escola');

        $lastEnrollment = $student->enrollments->last();
        $className = $lastEnrollment?->class?->name ?? '12ª Classe';
        $educationLevelName = $lastEnrollment?->class?->education_level_name ?? 'Ensino Secundário Geral';

        $pdf = Pdf::loadView('certificates.pdf_certificado', compact(
            'student',
            'schoolName',
            'province',
            'district',
            'directorName',
            'className',
            'educationLevelName'
        ));

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download("Certificado_Conclusao_{$student->student_number}.pdf");
    }

    /**
     * Auxiliar para converter nota em palavras por extenso em português.
     */
    private function gradeToWords($grade): string
    {
        $integerPart = (int) floor($grade);
        $decimalPart = (int) round(($grade - $integerPart) * 10);

        $words = [
            0 => 'Zero', 1 => 'Um', 2 => 'Dois', 3 => 'Três', 4 => 'Quatro',
            5 => 'Cinco', 6 => 'Seis', 7 => 'Sete', 8 => 'Oito', 9 => 'Nove',
            10 => 'Dez', 11 => 'Onze', 12 => 'Doze', 13 => 'Treze', 14 => 'Catorze',
            15 => 'Quinze', 16 => 'Dezasseis', 17 => 'Dezassete', 18 => 'Dezoito',
            19 => 'Dezanove', 20 => 'Vinte'
        ];

        $intWord = $words[$integerPart] ?? (string)$integerPart;
        
        if ($decimalPart > 0) {
            $decWord = $words[$decimalPart] ?? (string)$decimalPart;
            return "{$intWord} valores e {$decWord} décimas";
        }

        return "{$intWord} valores";
    }
}
