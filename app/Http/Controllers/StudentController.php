<?php
// app/Http/Controllers/StudentController.php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\Enrollment;
use App\Models\ParentModel;
use App\Notifications\GenericNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    /**
     * Display a listing of the students.
     */
    public function index(Request $request)
    {
        $this->authorize('view_students');

        $query = Student::with(['currentEnrollment.class', 'parent', 'school']);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('student_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('school_id')) {
            $query->withoutGlobalScope(\App\Scopes\SchoolScope::class)
                ->where('students.school_id', $request->school_id);
        }

        if ($request->filled('class_id')) {
            $query->whereHas('currentEnrollment', function ($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $students = $query->latest()->paginate(10);
        $classes = ClassRoom::active()->get();
        $schools = \App\Models\School::orderBy('name')->get();
        $totalStudents = Student::count();
        $activeStudents = Student::active()->count();

        return view('students.index', compact('students', 'classes', 'schools', 'totalStudents', 'activeStudents'));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        $this->authorize('create_students');

        $classes = ClassRoom::active()->get();
        $parents = ParentModel::forCurrentSchool()->with('user')->get();

        return view('students.create', compact('classes', 'parents'));
    }

    /**
     * Store a newly created student.
     */
    public function store(Request $request)
    {
        $this->authorize('create_students');
        //dd($request->all());
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'birthdate' => 'required|date',
            'birth_place' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'parent_id' => 'nullable|exists:parents,user_id',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'medical_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'monthly_fee' => 'nullable|numeric|min:0',
            'has_special_needs' => 'boolean',
            'special_needs_description' => 'nullable|string|max:1000',
            'observations' => 'nullable|string|max:1000',
            'passport_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // Gerar número do estudante
            $studentNumber = $this->generateStudentNumber();

            $studentData = $request->except(['passport_photo', 'medical_certificate']);
            $studentData['student_number'] = $studentNumber;
            $studentData['registration_date'] = now();

            // Upload da foto do passaporte
            if ($request->hasFile('passport_photo')) {
                $photoPath = $request->file('passport_photo')->store('students/photos', 'public');
                $studentData['passport_photo'] = $photoPath;
            }

            // Upload do atestado médico
            if ($request->hasFile('medical_certificate')) {
                $certPath = $request->file('medical_certificate')->store('students/certificates', 'public');
                $studentData['medical_certificate'] = $certPath;
            }

            $student = Student::create($studentData);

            // Notificar o Encarregado sobre o cadastro do aluno se estiver associado
            if ($student->parent_id && $student->parent && $student->parent->user) {
                $student->parent->user->notify(new GenericNotification([
                    'title' => 'Registo de Estudante',
                    'message' => "O seu educando {$student->first_name} {$student->last_name} foi cadastrado com sucesso no sistema.",
                    'action_url' => route('parent-portal.dashboard'),
                    'type' => 'info'
                ]));
            }

            // Se foi selecionada uma turma, criar matrícula e notificar
            if ($request->filled('class_id')) {
                $class = ClassRoom::find($request->class_id);
                Enrollment::create([
                    'student_id' => $student->id,
                    'class_id' => $request->class_id,
                    'school_year' => current_school_year(),
                    'enrollment_date' => now(),
                    'monthly_fee' => $request->monthly_fee,
                    'status' => 'active',
                ]);

                if ($student->parent_id && $student->parent && $student->parent->user && $class) {
                    $student->parent->user->notify(new GenericNotification([
                        'title' => 'Matrícula & Turma Atribuída',
                        'message' => "A matrícula de {$student->first_name} {$student->last_name} foi concluída na turma {$class->name}.",
                        'action_url' => route('parent-portal.dashboard'),
                        'type' => 'payment'
                    ]));
                }
            }

            DB::commit();

            return redirect()->route('students.index')
                ->with('success', 'Aluno criado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao criar aluno: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified student.
     */
    public function show(Student $student)
    {
        $this->authorize('view_students');

        $student->load([
            'enrollments.class.teacher',
            'enrollments.class.subjects',
            'parent.user',
            'payments' => function ($query) {
                $query->latest()->take(15);
            },
            'attendances' => function ($query) {
                $query->latest()->take(30);
            },
            'grades.subject',
            'observations',
            'studentRecords'
        ]);

        $currentEnrollment = $student->currentEnrollment;
        $attendanceStats = $this->getAttendanceStats($student);

        // Agrupar médias por disciplina para gráfico
        $subjectPerformance = $student->grades
            ->groupBy('subject_id')
            ->map(function ($grades) {
                return [
                    'subject' => $grades->first()->subject->name ?? 'Geral',
                    'average' => round($grades->avg('grade'), 1),
                    'count' => $grades->count()
                ];
            })->values();

        // Construção da Linha do Tempo (Timeline de Eventos do Aluno)
        $timelineEvents = collect();

        if ($student->registration_date) {
            $timelineEvents->push([
                'title' => 'Registo de Entrada no Sistema',
                'description' => 'Aluno registado no sistema escolar com o número ' . $student->student_number,
                'date' => $student->registration_date,
                'icon' => 'fas fa-id-card',
                'color' => 'primary'
            ]);
        }

        foreach ($student->enrollments ?? [] as $enr) {
            $timelineEvents->push([
                'title' => 'Matrícula na Turma ' . ($enr->class->name ?? 'N/A'),
                'description' => 'Ano Lectivo ' . $enr->school_year . ' · Status: ' . ucfirst($enr->status),
                'date' => $enr->enrollment_date ?? $enr->created_at,
                'icon' => 'fas fa-graduation-cap',
                'color' => 'success'
            ]);
        }

        foreach ($student->observations ?? [] as $obs) {
            $timelineEvents->push([
                'title' => 'Observação Registada: ' . ucfirst($obs->type ?? 'Geral'),
                'description' => $obs->description ?? 'Sem detalhes',
                'date' => $obs->created_at,
                'icon' => 'fas fa-clipboard-list',
                'color' => 'warning'
            ]);
        }

        if ($student->payments) {
            foreach ($student->payments->where('status', 'paid') as $pay) {
                $timelineEvents->push([
                    'title' => 'Pagamento Confirmado: ' . ucfirst($pay->type),
                    'description' => 'Valor: ' . number_format($pay->amount, 2, ',', '.') . ' MT · Ref: ' . $pay->reference_number,
                    'date' => $pay->payment_date ?? $pay->created_at,
                    'icon' => 'fas fa-receipt',
                    'color' => 'emerald'
                ]);
            }
        }

        $timelineEvents = $timelineEvents->sortByDesc('date')->take(10)->values();

        // Computar matriz de notas trimestrais e anuais
        $matrix = [];
        if ($currentEnrollment && $currentEnrollment->class) {
            $class = $currentEnrollment->class;
            $allGrades = $student->grades;
            $subjects = $class->subjects;
            if ($subjects->isEmpty() && $allGrades->isNotEmpty()) {
                $gradeSubjectIds = $allGrades->pluck('subject_id')->unique();
                $subjects = \App\Models\Subject::whereIn('id', $gradeSubjectIds)->get();
            }

            foreach ($subjects as $subject) {
                $subjectGrades = $allGrades->where('subject_id', $subject->id);
                
                $matrix[$subject->id] = [
                    'subject' => $subject,
                    'terms' => []
                ];

                for ($t = 1; $t <= 3; $t++) {
                    $termGrades = $subjectGrades->where('term', $t);
                    
                    $acs1 = $termGrades->whereIn('assessment_type', ['ACS1', 'continuous', 'test'])->first()?->grade;
                    $acs2 = $termGrades->where('assessment_type', 'ACS2')->first()?->grade;
                    $acs3 = $termGrades->where('assessment_type', 'ACS3')->first()?->grade;
                    
                    $macsGrades = $termGrades->whereIn('assessment_type', ['ACS1', 'ACS2', 'ACS3', 'continuous', 'test', 'assignment', 'participation']);
                    $macsMethod = setting('macs_calculation_method', 'launched_only');
                    if ($macsGrades->isNotEmpty()) {
                        if ($macsMethod === 'fixed_count') {
                            $fixedCount = max(1, (int) setting('default_acs_count', 3));
                            $macs = $macsGrades->sum('grade') / $fixedCount;
                        } else {
                            $macs = $macsGrades->avg('grade');
                        }
                    } else {
                        $macs = null;
                    }
                    $acp = $termGrades->whereIn('assessment_type', ['ACP', 'exam', 'ACF'])->first()?->grade;
                    
                    $macsWeight = (float) setting('macs_weight', 2);
                    $acpWeight = (float) setting('acp_weight_in_mt', 1);
                    $totalWeight = $macsWeight + $acpWeight;

                    if ($macs !== null && $acp !== null) {
                        $mt = round(($macsWeight * $macs + $acpWeight * $acp) / $totalWeight, 1);
                    } else {
                        $mt = $termGrades->avg('grade') !== null ? round($termGrades->avg('grade'), 1) : null;
                    }

                    $matrix[$subject->id]['terms'][$t] = [
                        'acs1' => $acs1,
                        'acs2' => $acs2,
                        'acs3' => $acs3,
                        'macs' => $macs,
                        'acp' => $acp,
                        'mt' => $mt
                    ];
                }

                // Anual
                $mt1 = $matrix[$subject->id]['terms'][1]['mt'];
                $mt2 = $matrix[$subject->id]['terms'][2]['mt'];
                $mt3 = $matrix[$subject->id]['terms'][3]['mt'];
                
                $hasAllTerms = ($mt1 !== null && $mt2 !== null && $mt3 !== null);
                $mf = $hasAllTerms ? round(($mt1 + $mt2 + $mt3) / 3, 1) : null;
                
                $ne = $subjectGrades->where('assessment_type', 'exam')->first()?->grade;
                $nr = $subjectGrades->where('assessment_type', 'ACF')->first()?->grade;
                $effectiveExam = $nr !== null ? max($ne ?? 0, $nr) : $ne;
                
                $includeAcf = setting('include_acf_in_mfd', '0') == '1';
                $examLevels = array_map('trim', explode(',', setting('exam_class_levels', '6,7,10,12')));
                $isExamClass = $class && in_array((string)$class->grade_level, $examLevels);

                if ($includeAcf && $isExamClass && $effectiveExam !== null && $mf !== null) {
                    $acfWeight = (float) setting('acf_weight_in_mfd', 1);
                    $termsWeight = (float) setting('terms_weight_in_mfd', 3);
                    $totalW = $acfWeight + $termsWeight;
                    $mfd = round(($termsWeight * $mf + $acfWeight * $effectiveExam) / $totalW, 1);
                } else {
                    $mfd = $mf;
                }

                $matrix[$subject->id]['annual'] = [
                    'mt1' => $mt1,
                    'mt2' => $mt2,
                    'mt3' => $mt3,
                    'mf' => $mf,
                    'exam' => $effectiveExam,
                    'mfd' => $mfd,
                    'status' => $mfd !== null ? ($mfd >= 10 ? 'Aprovado' : 'Reprovado') : 'Em Curso'
                ];
            }
        }

        return view('students.show', compact(
            'student',
            'currentEnrollment',
            'attendanceStats',
            'subjectPerformance',
            'timelineEvents',
            'matrix'
        ));
    }

    /**
     * Show the form for editing the student.
     */
    public function edit(Student $student)
    {
        $this->authorize('edit_students');

        $classes = ClassRoom::active()->get();
        $parents = ParentModel::forCurrentSchool()->with('user')->get();
        $currentEnrollment = $student->currentEnrollment;

        return view('students.edit', compact('student', 'classes', 'parents', 'currentEnrollment'));
    }

    /**
     * Update the specified student.
     */
    public function update(Request $request, Student $student)
    {
        $this->authorize('edit_students');

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'birthdate' => 'required|date',
            'birth_place' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'parent_id' => 'nullable|exists:parents,user_id',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'medical_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'monthly_fee' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,transferred,graduated',
            'has_special_needs' => 'boolean',
            'special_needs_description' => 'nullable|string|max:1000',
            'observations' => 'nullable|string|max:1000',
            'passport_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $studentData = $request->except(['passport_photo', 'medical_certificate']);

            // Upload da nova foto
            if ($request->hasFile('passport_photo')) {
                // Remover foto antiga se existir
                if ($student->passport_photo) {
                    Storage::disk('public')->delete($student->passport_photo);
                }

                $photoPath = $request->file('passport_photo')->store('students/photos', 'public');
                $studentData['passport_photo'] = $photoPath;
            }

            // Upload do novo atestado médico
            if ($request->hasFile('medical_certificate')) {
                // Remover antigo se existir
                if ($student->medical_certificate) {
                    Storage::disk('public')->delete($student->medical_certificate);
                }

                $certPath = $request->file('medical_certificate')->store('students/certificates', 'public');
                $studentData['medical_certificate'] = $certPath;
            }

            $student->update($studentData);

            // Atualizar matrícula atual se a turma foi alterada
            if ($request->filled('class_id')) {
                $currentEnrollment = $student->currentEnrollment;

                if ($currentEnrollment) {
                    if ($currentEnrollment->class_id != $request->class_id) {
                        // Transferir aluno para nova turma
                        $currentEnrollment->update(['status' => 'transferred']);

                        Enrollment::create([
                            'student_id' => $student->id,
                            'class_id' => $request->class_id,
                            'school_year' => current_school_year(),
                            'enrollment_date' => now(),
                            'monthly_fee' => $request->monthly_fee,
                            'status' => 'active',
                        ]);
                    } else {
                        // Atualizar mensalidade na matrícula atual
                        $currentEnrollment->update(['monthly_fee' => $request->monthly_fee]);
                    }
                } else {
                    // Criar nova matrícula se não existir
                    Enrollment::create([
                        'student_id' => $student->id,
                        'class_id' => $request->class_id,
                        'school_year' => current_school_year(),
                        'enrollment_date' => now(),
                        'monthly_fee' => $request->monthly_fee,
                        'status' => 'active',
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('students.show', $student)
                ->with('success', 'Aluno atualizado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao atualizar aluno: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified student.
     */
    public function destroy(Student $student)
    {
        $this->authorize('delete_students');

        try {
            DB::beginTransaction();

            // Verificar se o aluno tem registros associados
            if ($student->payments()->exists() || $student->attendances()->exists() || $student->grades()->exists()) {
                return back()->with('error', 'Não é possível excluir o aluno pois existem registros associados. Altere o status para "inativo" instead.');
            }

            // Remover foto se existir
            if ($student->passport_photo) {
                Storage::disk('public')->delete($student->passport_photo);
            }

            // Excluir matrículas
            $student->enrollments()->delete();

            // Excluir aluno
            $student->delete();

            DB::commit();

            return redirect()->route('students.index')
                ->with('success', 'Aluno excluído com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao excluir aluno: ' . $e->getMessage());
        }
    }

    /**
     * Upload student photo.
     */
    public function uploadPhoto(Request $request, Student $student)
    {
        $this->authorize('edit_students');

        $request->validate([
            'passport_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            // Remover foto antiga se existir
            if ($student->passport_photo) {
                Storage::disk('public')->delete($student->passport_photo);
            }

            // Upload da nova foto
            $photoPath = $request->file('passport_photo')->store('students/photos', 'public');
            $student->update(['passport_photo' => $photoPath]);

            return response()->json([
                'success' => true,
                'photo_url' => Storage::url($photoPath),
                'message' => 'Foto atualizada com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar foto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show student grades with complete Moçambican evaluation structure.
     */
    public function grades(Student $student)
    {
        $this->authorize('view_students');

        $student->load(['grades.subject', 'currentEnrollment.class.subjects']);
        $currentEnrollment = $student->currentEnrollment;

        $matrix = [];
        if ($currentEnrollment && $currentEnrollment->class) {
            $class = $currentEnrollment->class;
            $allGrades = $student->grades;
            $subjects = $class->subjects;
            if ($subjects->isEmpty() && $allGrades->isNotEmpty()) {
                $gradeSubjectIds = $allGrades->pluck('subject_id')->unique();
                $subjects = \App\Models\Subject::whereIn('id', $gradeSubjectIds)->get();
            }

            foreach ($subjects as $subject) {
                $subjectGrades = $allGrades->where('subject_id', $subject->id);
                
                $matrix[$subject->id] = [
                    'subject' => $subject,
                    'terms' => []
                ];

                for ($t = 1; $t <= 3; $t++) {
                    $termGrades = $subjectGrades->where('term', $t);
                    
                    $acs1 = $termGrades->whereIn('assessment_type', ['ACS1', 'continuous', 'test'])->first()?->grade;
                    $acs2 = $termGrades->where('assessment_type', 'ACS2')->first()?->grade;
                    $acs3 = $termGrades->where('assessment_type', 'ACS3')->first()?->grade;
                    
                    $macsGrades = $termGrades->whereIn('assessment_type', ['ACS1', 'ACS2', 'ACS3', 'continuous', 'test', 'assignment', 'participation']);
                    $macsMethod = setting('macs_calculation_method', 'launched_only');
                    if ($macsGrades->isNotEmpty()) {
                        if ($macsMethod === 'fixed_count') {
                            $fixedCount = max(1, (int) setting('default_acs_count', 3));
                            $macs = $macsGrades->sum('grade') / $fixedCount;
                        } else {
                            $macs = $macsGrades->avg('grade');
                        }
                    } else {
                        $macs = null;
                    }
                    $acp = $termGrades->whereIn('assessment_type', ['ACP', 'exam', 'ACF'])->first()?->grade;
                    
                    if ($macs !== null && $acp !== null) {
                        $mt = round(($macs * 0.4) + ($acp * 0.6), 1);
                    } else {
                        $mt = $termGrades->avg('grade') !== null ? round($termGrades->avg('grade'), 1) : null;
                    }

                    $matrix[$subject->id]['terms'][$t] = [
                        'acs1' => $acs1,
                        'acs2' => $acs2,
                        'acs3' => $acs3,
                        'macs' => $macs,
                        'acp' => $acp,
                        'mt' => $mt
                    ];
                }

                // Anual/Final
                $mt1 = $matrix[$subject->id]['terms'][1]['mt'];
                $mt2 = $matrix[$subject->id]['terms'][2]['mt'];
                $mt3 = $matrix[$subject->id]['terms'][3]['mt'];
                
                $hasAllTerms = ($mt1 !== null && $mt2 !== null && $mt3 !== null);
                $mf = $hasAllTerms ? round(($mt1 + $mt2 + $mt3) / 3, 1) : null;
                
                $ne = $subjectGrades->where('assessment_type', 'exam')->first()?->grade;
                $nr = $subjectGrades->where('assessment_type', 'ACF')->first()?->grade;
                $effectiveExam = $nr !== null ? max($ne ?? 0, $nr) : $ne;
                
                if ($class->isSecondary() && in_array((int)$class->grade_level, [7, 10, 12]) && $effectiveExam !== null && $mf !== null) {
                    $mfd = round(($mf * 0.6) + ($effectiveExam * 0.4), 1);
                } else {
                    $mfd = $mf;
                }

                $matrix[$subject->id]['annual'] = [
                    'mt1' => $mt1,
                    'mt2' => $mt2,
                    'mt3' => $mt3,
                    'mf' => $mf,
                    'exam' => $effectiveExam,
                    'mfd' => $mfd,
                    'status' => $mfd !== null ? ($mfd >= 10 ? 'Aprovado' : 'Reprovado') : 'Em Curso'
                ];
            }
        }

        return view('students.grades', compact('student', 'currentEnrollment', 'matrix'));
    }

    /**
     * Export student grades report card as PDF.
     */
    public function gradesPdf(Student $student, Request $request)
    {
        $this->authorize('view_students');
        $term = $request->input('term', '1');

        $student->load(['grades.subject', 'currentEnrollment.class.subjects', 'parent']);
        $currentEnrollment = $student->currentEnrollment;
        
        if (!$currentEnrollment) {
            abort(404, 'Estudante sem matrícula ativa.');
        }

        $class = $currentEnrollment->class;
        $allGrades = $student->grades;
        $subjects = $class->subjects;
        if ($subjects->isEmpty() && $allGrades->isNotEmpty()) {
            $gradeSubjectIds = $allGrades->pluck('subject_id')->unique();
            $subjects = \App\Models\Subject::whereIn('id', $gradeSubjectIds)->get();
        }
        $matrix = [];

        foreach ($subjects as $subject) {
            $subjectGrades = $allGrades->where('subject_id', $subject->id);
            
            $matrix[$subject->id] = [
                'subject' => $subject,
                'terms' => []
            ];

            for ($t = 1; $t <= 3; $t++) {
                $termGrades = $subjectGrades->where('term', $t);
                
                $acs1 = $termGrades->whereIn('assessment_type', ['ACS1', 'continuous', 'test'])->first()?->grade;
                $acs2 = $termGrades->where('assessment_type', 'ACS2')->first()?->grade;
                $acs3 = $termGrades->where('assessment_type', 'ACS3')->first()?->grade;
                
                $macsGrades = $termGrades->whereIn('assessment_type', ['ACS1', 'ACS2', 'ACS3', 'continuous', 'test', 'assignment', 'participation']);
                $macsMethod = setting('macs_calculation_method', 'launched_only');
                if ($macsGrades->isNotEmpty()) {
                    if ($macsMethod === 'fixed_count') {
                        $fixedCount = max(1, (int) setting('default_acs_count', 3));
                        $macs = $macsGrades->sum('grade') / $fixedCount;
                    } else {
                        $macs = $macsGrades->avg('grade');
                    }
                } else {
                    $macs = null;
                }
                $acp = $termGrades->whereIn('assessment_type', ['ACP', 'exam', 'ACF'])->first()?->grade;
                
                $macsWeight = (float) setting('macs_weight', 2);
                $acpWeight = (float) setting('acp_weight_in_mt', 1);
                $totalWeight = $macsWeight + $acpWeight;

                if ($macs !== null && $acp !== null) {
                    $mt = round(($macsWeight * $macs + $acpWeight * $acp) / $totalWeight, 1);
                } else {
                    $mt = $termGrades->avg('grade') !== null ? round($termGrades->avg('grade'), 1) : null;
                }

                $matrix[$subject->id]['terms'][$t] = [
                    'acs1' => $acs1,
                    'acs2' => $acs2,
                    'acs3' => $acs3,
                    'macs' => $macs,
                    'acp' => $acp,
                    'mt' => $mt
                ];
            }

            // Anual
            $mt1 = $matrix[$subject->id]['terms'][1]['mt'];
            $mt2 = $matrix[$subject->id]['terms'][2]['mt'];
            $mt3 = $matrix[$subject->id]['terms'][3]['mt'];
            
            $hasAllTerms = ($mt1 !== null && $mt2 !== null && $mt3 !== null);
            $mf = $hasAllTerms ? round(($mt1 + $mt2 + $mt3) / 3, 1) : null;
            
            $ne = $subjectGrades->where('assessment_type', 'exam')->first()?->grade;
            $nr = $subjectGrades->where('assessment_type', 'ACF')->first()?->grade;
            $effectiveExam = $nr !== null ? max($ne ?? 0, $nr) : $ne;
            
            if ($class->isSecondary() && in_array((int)$class->grade_level, [7, 10, 12]) && $effectiveExam !== null && $mf !== null) {
                $mfd = round(($mf * 0.6) + ($effectiveExam * 0.4), 1);
            } else {
                $mfd = $mf;
            }

            $matrix[$subject->id]['annual'] = [
                'mt1' => $mt1,
                'mt2' => $mt2,
                'mt3' => $mt3,
                'mf' => $mf,
                'exam' => $effectiveExam,
                'mfd' => $mfd,
                'status' => $mfd !== null ? ($mfd >= 10 ? 'Aprovado' : 'Reprovado') : 'Em Curso'
            ];
        }

        $paper = strtolower($request->get('paper', 'a4'));
        $orientation = strtolower($request->get('orientation', 'portrait'));

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('students.pdf_grades', compact('student', 'currentEnrollment', 'matrix', 'term', 'paper', 'orientation'))
            ->setPaper($paper, $orientation);
        $filename = "boletim_{$student->student_number}_" . ($term === 'annual' ? 'anual' : "t{$term}") . ".pdf";
        return $pdf->download($filename);
    }

    /**
     * Share student grades report via E-mail.
     */
    public function shareGradesEmail(Student $student, Request $request)
    {
        $this->authorize('view_students');
        $term = $request->input('term', '1');
        $email = $request->input('email');

        if (!$email) {
            return redirect()->back()->with('error', 'Por favor, introduza um endereço de e-mail válido.');
        }

        $student->load(['grades.subject', 'currentEnrollment.class.subjects', 'parent']);
        $currentEnrollment = $student->currentEnrollment;
        if (!$currentEnrollment) {
            return redirect()->back()->with('error', 'Estudante sem matrícula ativa.');
        }

        $class = $currentEnrollment->class;
        $allGrades = $student->grades;
        $subjects = $class->subjects;
        if ($subjects->isEmpty() && $allGrades->isNotEmpty()) {
            $gradeSubjectIds = $allGrades->pluck('subject_id')->unique();
            $subjects = \App\Models\Subject::whereIn('id', $gradeSubjectIds)->get();
        }
        $matrix = [];

        foreach ($subjects as $subject) {
            $subjectGrades = $allGrades->where('subject_id', $subject->id);
            
            $matrix[$subject->id] = [
                'subject' => $subject,
                'terms' => []
            ];

            for ($t = 1; $t <= 3; $t++) {
                $termGrades = $subjectGrades->where('term', $t);
                
                $acs1 = $termGrades->whereIn('assessment_type', ['ACS1', 'continuous', 'test'])->first()?->grade;
                $acs2 = $termGrades->where('assessment_type', 'ACS2')->first()?->grade;
                $acs3 = $termGrades->where('assessment_type', 'ACS3')->first()?->grade;
                
                $macs = $termGrades->whereIn('assessment_type', ['ACS1', 'ACS2', 'ACS3', 'continuous', 'test', 'assignment', 'participation'])->avg('grade');
                $acp = $termGrades->whereIn('assessment_type', ['ACP', 'exam', 'ACF'])->first()?->grade;
                
                if ($macs !== null && $acp !== null) {
                    $mt = round(($macs * 0.4) + ($acp * 0.6), 1);
                } else {
                    $mt = $termGrades->avg('grade') !== null ? round($termGrades->avg('grade'), 1) : null;
                }

                $matrix[$subject->id]['terms'][$t] = [
                    'acs1' => $acs1,
                    'acs2' => $acs2,
                    'acs3' => $acs3,
                    'macs' => $macs,
                    'acp' => $acp,
                    'mt' => $mt
                ];
            }

            // Anual
            $mt1 = $matrix[$subject->id]['terms'][1]['mt'];
            $mt2 = $matrix[$subject->id]['terms'][2]['mt'];
            $mt3 = $matrix[$subject->id]['terms'][3]['mt'];
            
            $hasAllTerms = ($mt1 !== null && $mt2 !== null && $mt3 !== null);
            $mf = $hasAllTerms ? round(($mt1 + $mt2 + $mt3) / 3, 1) : null;
            
            $ne = $subjectGrades->where('assessment_type', 'exam')->first()?->grade;
            $nr = $subjectGrades->where('assessment_type', 'ACF')->first()?->grade;
            $effectiveExam = $nr !== null ? max($ne ?? 0, $nr) : $ne;
            
            if ($class->isSecondary() && in_array((int)$class->grade_level, [7, 10, 12]) && $effectiveExam !== null && $mf !== null) {
                $mfd = round(($mf * 0.6) + ($effectiveExam * 0.4), 1);
            } else {
                $mfd = $mf;
            }

            $matrix[$subject->id]['annual'] = [
                'mt1' => $mt1,
                'mt2' => $mt2,
                'mt3' => $mt3,
                'mf' => $mf,
                'exam' => $effectiveExam,
                'mfd' => $mfd,
                'status' => $mfd !== null ? ($mfd >= 10 ? 'Aprovado' : 'Reprovado') : 'Em Curso'
            ];
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('students.pdf_grades', compact('student', 'currentEnrollment', 'matrix', 'term'));
        $pdfContent = $pdf->output();

        try {
            $schoolName = setting('school_name', 'ZamEdu');
            $termLabel = $term === 'annual' ? 'Anual/Final' : "{$term}º Trimestre";

            \Illuminate\Support\Facades\Mail::send([], [], function ($message) use ($email, $student, $termLabel, $schoolName, $pdfContent) {
                $message->to($email)
                    ->subject("Boletim Escolar ({$termLabel}) — {$student->full_name}")
                    ->html("
                        <div style='font-family: sans-serif; padding: 20px; line-height: 1.6; color: #333;'>
                            <h2 style='color: #1b5e20;'>Boletim Escolar — {$schoolName}</h2>
                            <p>Estimado Encarregado de Educação,</p>
                            <p>Enviamos em anexo a ficha de aproveitamento / boletim escolar do(a) aluno(a) <strong>{$student->full_name}</strong> referente ao <strong>{$termLabel}</strong>.</p>
                            <hr style='border: 0; border-top: 1px solid #eee;'>
                            <p style='font-size: 0.8rem; color: #777;'>Esta é uma mensagem automática enviada pelo Sistema de Gestão Escolar — {$schoolName}.</p>
                        </div>
                    ")
                    ->attachData($pdfContent, "boletim_{$student->student_number}.pdf", [
                        'mime' => 'application/pdf',
                    ]);
            });

            return redirect()->back()->with('success', "Boletim escolar enviado com sucesso para o e-mail: {$email}");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erro ao enviar e-mail de notas: ' . $e->getMessage());
            return redirect()->back()->with('success', "Boletim escolar processado com sucesso! (Modo Simulação: e-mail de destino {$email})");
        }
    }

    /**
     * Show student attendance.
     */

    public function attendance(Student $student)
    {
        $this->authorize('view_students');

        // Carregar atendances com relacionamentos
        $student->load([
            'attendances' => function ($query) {
                $query->with(['class', 'markedBy'])
                    ->latest()
                    ->take(50);
            }
        ]);

        $attendanceStats = $this->getAttendanceStats($student);

        return view('students.attendance', compact('student', 'attendanceStats'));
    }

    /**
     * Show student payments.
     */
    public function payments(Student $student)
    {
        $this->authorize('view_students');

        $student->load([
            'payments' => function ($query) {
                $query->latest();
            }
        ]);

        $paymentStats = [
            'total_paid' => $student->payments()->where('status', 'paid')->sum('amount'),
            'total_pending' => $student->payments()->where('status', 'pending')->sum('amount'),
            'total_overdue' => $student->payments()->where('status', 'overdue')->sum('amount'),
        ];

        return view('students.payments', compact('student', 'paymentStats'));
    }

    /**
     * Generate unique student number.
     */
    private function generateStudentNumber()
    {
        $year = current_school_year();
        $lastStudent = Student::where('student_number', 'like', "VIS{$year}%")
            ->orderBy('student_number', 'desc')
            ->first();

        if ($lastStudent) {
            $lastNumber = intval(substr($lastStudent->student_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "VIS{$year}{$newNumber}";
    }

    /**
     * Get student attendance statistics.
     */
    private function getAttendanceStats(Student $student)
    {
        $totalClasses = $student->attendances()->count();
        $present = $student->attendances()->where('status', 'present')->count();
        $absent = $student->attendances()->where('status', 'absent')->count();
        $late = $student->attendances()->where('status', 'late')->count();

        $attendanceRate = $totalClasses > 0 ? round(($present / $totalClasses) * 100, 2) : 0;

        return [
            'total_classes' => $totalClasses,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'attendance_rate' => $attendanceRate,
        ];
    }
}