<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\Subject;
use App\Models\Enrollment;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentPortalController extends Controller
{
    /**
     * Obtém o registo de Estudante associado ao utilizador autenticado.
     */
    protected function getStudent(): ?Student
    {
        $user = Auth::user();
        if (!$user) return null;

        // 1. Procura por vínculo de email ou número de estudante
        $student = Student::withoutGlobalScopes()
            ->where('student_number', $user->email)
            ->orWhere('id', $user->student_id ?? 0)
            ->first();

        // 2. Se não encontrar diretamente, associa ao primeiro aluno ativo da escola para efeitos de demonstração
        if (!$student && $user->school_id) {
            $student = Student::withoutGlobalScopes()
                ->where('school_id', $user->school_id)
                ->where('status', 'active')
                ->first();
        }

        // 3. Fallback geral para demonstração
        if (!$student) {
            $student = Student::withoutGlobalScopes()->where('status', 'active')->first() 
                ?? Student::withoutGlobalScopes()->first();
        }

        return $student;
    }

    /**
     * Dashboard do Portal do Estudante.
     */
    public function dashboard(Request $request)
    {
        $student = $this->getStudent();

        if (!$student) {
            return view('student-portal.dashboard', [
                'student' => null,
                'noStudentRecord' => true,
            ]);
        }

        $currentYear = function_exists('current_school_year') ? current_school_year() : now()->year;
        $currentTerm = (int) $request->input('term', 1);

        // Turma e Matrícula Atual
        $enrollment = Enrollment::withoutGlobalScopes()
            ->where('student_id', $student->id)
            ->where('school_year', $currentYear)
            ->with(['class'])
            ->first();

        $class = $enrollment?->class;
        $isExamClass = $class ? in_array((int) $class->grade_level, [6, 9, 12]) : false;

        // Disciplinas da turma
        $subjects = $class 
            ? $class->subjects()->get() 
            : Subject::withoutGlobalScopes()->limit(8)->get();

        // Notas do aluno para este ano e trimestre
        $allStudentGrades = Grade::withoutGlobalScopes()
            ->where('student_id', $student->id)
            ->where('year', $currentYear)
            ->where('term', $currentTerm)
            ->get();

        $classId = (int) ($class?->id ?? $enrollment?->class_id ?? $allStudentGrades->first()?->class_id ?? 0);

        // Notas por Disciplina no Trimestre Atual
        $gradesData = [];
        $totalSum = 0;
        $validSubjectsCount = 0;

        foreach ($subjects as $subject) {
            $grades = $allStudentGrades
                ->where('subject_id', $subject->id)
                ->where('year', $currentYear)
                ->where('term', $currentTerm)
                ->keyBy('assessment_type');

            $acs1 = isset($grades['ACS1']) ? (float)$grades['ACS1']->grade : null;
            $acs2 = isset($grades['ACS2']) ? (float)$grades['ACS2']->grade : null;
            $acs3 = isset($grades['ACS3']) ? (float)$grades['ACS3']->grade : null;
            $acp  = isset($grades['ACP'])  ? (float)$grades['ACP']->grade  : null;
            $acf  = isset($grades['ACF'])  ? (float)$grades['ACF']->grade  : null;

            // Motor de cálculo oficial MINEDH (Lei 18/2018)
            if ($classId > 0) {
                $macs = Grade::calculateMACS((int)$student->id, (int)$subject->id, $classId, $currentTerm, $allStudentGrades);
                $mt   = Grade::calculateMT((int)$student->id, (int)$subject->id, $classId, $currentTerm, $allStudentGrades);
            } else {
                $acsValues = array_filter([$acs1, $acs2, $acs3], fn($v) => $v !== null);
                $macs = count($acsValues) > 0 ? round(array_sum($acsValues) / count($acsValues), 1) : null;
                $mt = ($macs !== null && $acp !== null) ? round((2 * $macs + $acp) / 3, 1) : ($macs ?? $acp);
            }

            if ($mt !== null) {
                $totalSum += $mt;
                $validSubjectsCount++;
            }

            $gradesData[] = [
                'subject' => $subject,
                'acs1' => $acs1,
                'acs2' => $acs2,
                'acs3' => $acs3,
                'acp'  => $acp,
                'acf'  => $acf,
                'macs' => $macs,
                'mt'   => $mt,
                'status' => $mt === null ? 'Pendente' : ($mt >= 9.5 ? 'Positivo' : 'Reforço'),
            ];
        }

        $overallAverage = $validSubjectsCount > 0 ? round($totalSum / $validSubjectsCount, 1) : null;

        // Assiduidade do Estudante
        $attendances = Attendance::withoutGlobalScopes()
            ->where('student_id', $student->id)
            ->whereYear('attendance_date', $currentYear)
            ->get();

        $totalDays = $attendances->count();
        $presences = $attendances->where('status', 'present')->count();
        $justifiedAbsences = $attendances->where('status', 'justified')->count();
        $unjustifiedAbsences = $attendances->where('status', 'absent')->count();
        $attendanceRate = $totalDays > 0 ? round((($presences + $justifiedAbsences) / $totalDays) * 100, 1) : 100.0;

        return view('student-portal.dashboard', [
            'student' => $student,
            'enrollment' => $enrollment,
            'class' => $class,
            'isExamClass' => $isExamClass,
            'currentTerm' => $currentTerm,
            'currentYear' => $currentYear,
            'gradesData' => $gradesData,
            'overallAverage' => $overallAverage,
            'totalDays' => $totalDays,
            'presences' => $presences,
            'justifiedAbsences' => $justifiedAbsences,
            'unjustifiedAbsences' => $unjustifiedAbsences,
            'attendanceRate' => $attendanceRate,
            'noStudentRecord' => false,
        ]);
    }

    /**
     * Simulação de Exame Nacional (Fórmula MINEDH).
     */
    public function simulateExam(Request $request)
    {
        $request->validate([
            'maf' => 'required|numeric|min:0|max:20',
            'ne'  => 'required|numeric|min:0|max:20',
            'acf' => 'nullable|numeric|min:0|max:20',
        ]);

        $maf = (float) $request->input('maf');
        $ne  = (float) $request->input('ne');
        $acf = $request->input('acf') !== null ? (float) $request->input('acf') : null;

        // NE_Eficaz = max(NE, ACF)
        $neEficaz = $acf !== null ? max($ne, $acf) : $ne;
        // NFD = round((MAF + NE_Eficaz) / 2)
        $nfd = (int) round(($maf + $neEficaz) / 2);
        $approved = $nfd >= 10;

        return response()->json([
            'success' => true,
            'ne_eficaz' => $neEficaz,
            'nfd' => $nfd,
            'approved' => $approved,
            'status' => $approved ? 'Aprovado' : 'Não Aprovado',
        ]);
    }
}
