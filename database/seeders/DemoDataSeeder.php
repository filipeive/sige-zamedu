<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Teacher;
use App\Models\ParentModel;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\Payment;
use App\Models\Event;
use App\Models\Notification;
use App\Models\StaffLeaveRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        
        // 1. Professores
        $teachers = $this->createTeachers();

        // 2. Pais & Encarregados
        $parents = $this->createParents();

        // 3. Alunos
        $students = $this->createStudents($parents);

        // 4. Matrículas
        $enrollments = $this->createEnrollments($students);

        // 5. Lançamento de Notas (ACS1, ACS2, ACS3, ACP para Trimestres 1, 2 e 3)
        $this->createGrades($enrollments, $teachers);

        // 6. Registos de Assiduidade (Últimos 30 dias úteis)
        $this->createAttendance($enrollments);

        // 7. Histórico de Pagamentos de Propinas & Matrículas
        $this->createPayments($enrollments);

        // 8. Eventos e Licenças
        $this->createEvents();
        $this->createLeaveRequests($teachers);

        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $this->command->info('✅ Povoamento massivo de dados concluído com sucesso!');
    }

    private function createTeachers()
    {
        $teacherSpecs = [
            ['name' => 'Maria Santos Silva', 'email' => 'maria.silva@zamedu.co.mz', 'qualification' => 'Mestrado em Educação', 'specialization' => 'Português'],
            ['name' => 'João Carlos Mendes', 'email' => 'joao.mendes@zamedu.co.mz', 'qualification' => 'Licenciatura em Matemática', 'specialization' => 'Matemática'],
            ['name' => 'Ana Paula Costa', 'email' => 'ana.costa@zamedu.co.mz', 'qualification' => 'Licenciatura em Física', 'specialization' => 'Física'],
            ['name' => 'Carlos Alberto Nunes', 'email' => 'carlos.nunes@zamedu.co.mz', 'qualification' => 'Licenciatura em Química', 'specialization' => 'Química'],
            ['name' => 'Fernanda Lima', 'email' => 'fernanda.lima@zamedu.co.mz', 'qualification' => 'Licenciatura em Educação Física', 'specialization' => 'Educação Física'],
            ['name' => 'Roberto Afonso', 'email' => 'roberto.afonso@zamedu.co.mz', 'qualification' => 'Licenciatura em História', 'specialization' => 'História'],
            ['name' => 'Teresa Baloi', 'email' => 'teresa.baloi@zamedu.co.mz', 'qualification' => 'Licenciatura em Geografia', 'specialization' => 'Geografia'],
            ['name' => 'Élia Muthemba', 'email' => 'elia.muthemba@zamedu.co.mz', 'qualification' => 'Licenciatura em Biologia', 'specialization' => 'Biologia'],
            ['name' => 'Samuel Macamo', 'email' => 'samuel.macamo@zamedu.co.mz', 'qualification' => 'Licenciatura em Inglês', 'specialization' => 'Inglês'],
            ['name' => 'Lucinda Nguenha', 'email' => 'lucinda.nguenha@zamedu.co.mz', 'qualification' => 'Licenciatura em Filosofia', 'specialization' => 'Filosofia'],
        ];

        $teachers = collect();

        foreach ($teacherSpecs as $index => $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => bcrypt('123456'),
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]
            );

            if (method_exists($user, 'assignRole')) {
                $user->assignRole('teacher');
            }

            $names = explode(' ', $data['name']);
            $teacher = Teacher::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name' => $names[0],
                    'last_name' => implode(' ', array_slice($names, 1)),
                    'email' => $data['email'],
                    'phone' => '+258 84 ' . rand(100, 999) . ' ' . rand(1000, 9999),
                    'hire_date' => now()->subMonths(rand(12, 48)),
                    'qualification' => $data['qualification'],
                    'specialization' => $data['specialization'],
                    'bi_number' => rand(100000000, 999999999) . 'Z',
                    'birth_date' => now()->subYears(rand(28, 50)),
                    'gender' => rand(0, 1) ? 'male' : 'female',
                    'address' => 'Maputo, Moçambique',
                    'salary' => rand(25000, 45000),
                    'status' => 'active',
                ]
            );

            $teachers->push($teacher);

            // Manter o perfil do professor alinhado com a escola do utilizador
            // (o scope global de escola esconde registos com school_id diferente)
            if ($teacher->school_id !== $user->school_id) {
                $teacher->update(['school_id' => $user->school_id]);
            }

            // Atribuir professor a turmas existentes
            $classes = ClassRoom::whereNull('teacher_id')->orWhere('teacher_id', $teacher->id)->take(2)->get();
            foreach ($classes as $class) {
                $class->update(['teacher_id' => $teacher->id]);
            }
        }

        return $teachers;
    }

    private function createParents()
    {
        $parentNames = [
            ['name' => 'António Manuel Joaquim', 'email' => 'antonio.joaquim@gmail.com', 'profession' => 'Engenheiro Civil'],
            ['name' => 'Helena Maria Cardoso', 'email' => 'helena.cardoso@gmail.com', 'profession' => 'Médica Geral'],
            ['name' => 'José Francisco Mateus', 'email' => 'jose.mateus@gmail.com', 'profession' => 'Professor Universitário'],
            ['name' => 'Isabel Santos Pereira', 'email' => 'isabel.pereira@gmail.com', 'profession' => 'Gestora Bancária'],
            ['name' => 'Manuel João da Costa', 'email' => 'manuel.costa@gmail.com', 'profession' => 'Arquiteto'],
            ['name' => 'Graça Mabote', 'email' => 'graca.mabote@gmail.com', 'profession' => 'Economista'],
            ['name' => 'Paulo Cossa', 'email' => 'paulo.cossa@gmail.com', 'profession' => 'Advogado'],
            ['name' => 'Beatriz Langa', 'email' => 'beatriz.langa@gmail.com', 'profession' => 'Empresária'],
            ['name' => 'Alberto Vilanculos', 'email' => 'alberto.v@gmail.com', 'profession' => 'Contabilista'],
            ['name' => 'Sofia Tembe', 'email' => 'sofia.tembe@gmail.com', 'profession' => 'Farmacêutica'],
            ['name' => 'Gabriel Machava', 'email' => 'gabriel.machava@gmail.com', 'profession' => 'Piloto'],
            ['name' => 'Clara Sitoe', 'email' => 'clara.sitoe@gmail.com', 'profession' => 'Jornalista'],
            ['name' => 'Emílio Mondlane', 'email' => 'emilio.mondlane@gmail.com', 'profession' => 'Consultor de TI'],
            ['name' => 'Fátima Chissano', 'email' => 'fatima.chissano@gmail.com', 'profession' => 'Socióloga'],
            ['name' => 'Dário Nkomati', 'email' => 'dario.nkomati@gmail.com', 'profession' => 'Auditor'],
        ];

        $parents = collect();

        foreach ($parentNames as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => bcrypt('123456'),
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]
            );

            if (method_exists($user, 'assignRole')) {
                $user->assignRole('parent');
            }

            $names = explode(' ', $data['name']);
            $parent = ParentModel::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name' => $names[0],
                    'last_name' => implode(' ', array_slice($names, 1)),
                    'phone' => '+258 84 ' . rand(100, 999) . ' ' . rand(1000, 9999),
                    'email' => $data['email'],
                    'address' => 'Av. Julius Nyerere, Maputo',
                    'relationship' => rand(0, 1) ? 'Father' : 'Mother',
                    'profession' => $data['profession'],
                    'workplace' => 'Maputo, Moçambique',
                    'emergency_contact' => 'Familiar Próximo',
                    'emergency_phone' => '+258 82 ' . rand(100, 999) . ' ' . rand(1000, 9999),
                    'bi_number' => rand(100000000, 999999999) . 'M',
                    'birth_date' => now()->subYears(rand(30, 52)),
                ]
            );

            $parents->push($parent);
        }

        return $parents;
    }

    private function createStudents($parents)
    {
        $firstNames = ['Afonso', 'Amina', 'Bruno', 'Celso', 'Dália', 'Edson', 'Fatima', 'Gerson', 'Hélio', 'Iolanda', 'Jercio', 'Kátia', 'Lénio', 'Milena', 'Nádia', 'Octávio', 'Patrícia', 'Quirino', 'Rui', 'Sónia', 'Tânia', 'Urbano', 'Valdemar', 'Yara', 'Zainado'];
        $lastNames = ['Mabote', 'Langa', 'Sitoe', 'Tembe', 'Cossa', 'Machava', 'Chissano', 'Mondlane', 'Nkomati', 'Vilanculos', 'Matola', 'Gaza', 'Zambézia', 'Manica', 'Sofala'];

        $students = collect();
        $studentIndex = (Student::max('id') ?? 0) + 1;

        foreach ($parents as $parent) {
            $numChildren = rand(1, 3);

            for ($c = 0; $c < $numChildren; $c++) {
                $firstName = $firstNames[array_rand($firstNames)];
                $lastName = $parent->last_name ?: $lastNames[array_rand($lastNames)];
                $age = rand(6, 17);

                $studentNumber = 'ZAM' . date('Y') . str_pad($studentIndex, 4, '0', STR_PAD_LEFT);

                $student = Student::create([
                    'student_number' => $studentNumber,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'gender' => rand(0, 1) ? 'male' : 'female',
                    'birthdate' => now()->subYears($age)->subDays(rand(0, 350)),
                    'birth_place' => 'Maputo',
                    'registration_date' => now()->startOfYear()->addDays(rand(1, 20)),
                    'monthly_fee' => 2300.00,
                    'parent_id' => $parent->user_id,
                    'address' => $parent->address ?? 'Maputo, Moçambique',
                    'emergency_contact' => $parent->first_name . ' ' . $parent->last_name,
                    'emergency_phone' => $parent->phone,
                    'status' => 'active',
                ]);

                $students->push($student);
                $studentIndex++;
            }
        }

        return $students;
    }

    private function createEnrollments($students)
    {
        $classes = ClassRoom::all();

        if ($classes->isEmpty()) {
            // Criar turmas padrão se não existirem
            // Pré-Escolar/Primário (grade 0-7): 1 professor regente + assistente opcional
            // Secundário (grade 8-12): professores atribuídos por disciplina
            for ($g = 1; $g <= 12; $g++) {
                $isSecondary = $g >= 8;
                $classes->push(ClassRoom::create([
                    'name' => "Turma {$g}ª A",
                    'grade_level' => $g,
                    'section' => 'A',
                    'teaching_mode' => $isSecondary ? 'subject_based' : 'single_teacher',
                    'school_year' => current_school_year(),
                    'max_students' => 35,
                    'current_students' => 0,
                    'is_active' => true,
                    'room_number' => "Sala 10{$g}",
                ]));
            }
        }

        $enrollments = collect();
        $year = current_school_year();

        foreach ($students as $student) {
            $age = $student->age ?? 10;
            $grade = max(1, min(12, $age - 5));

            $class = $classes->where('grade_level', $grade)->first() ?? $classes->random();

            $enrollment = Enrollment::create([
                'student_id' => $student->id,
                'class_id' => $class->id,
                'school_year' => $year,
                'status' => 'active',
                'enrollment_date' => $student->registration_date ?? now()->startOfYear(),
                'monthly_fee' => $student->monthly_fee ?? 2300.00,
                'payment_day' => 10,
            ]);

            $class->increment('current_students');
            $enrollments->push($enrollment);
        }

        return $enrollments;
    }

    private function createGrades($enrollments, $teachers)
    {
        $subjects = Subject::all();
        if ($subjects->isEmpty()) {
            $subjectNames = ['Português', 'Matemática', 'Física', 'Química', 'Biologia', 'História', 'Geografia', 'Inglês'];
            foreach ($subjectNames as $sName) {
                Subject::create([
                    'name' => $sName,
                    'code' => strtoupper(substr($sName, 0, 3)),
                    'status' => 'active'
                ]);
            }
            $subjects = Subject::all();
        }

        $year = current_school_year();
        $assessmentTypes = ['continuous', 'test', 'exam', 'final'];

        foreach ($enrollments as $enrollment) {
            $student = $enrollment->student;
            $class = $enrollment->class;

            if (!$student || !$class) continue;

            // Selecionar 4 a 6 disciplinas por aluno
            $studentSubjects = $subjects->random(min(5, $subjects->count()));

            foreach ($studentSubjects as $subject) {
                // Gerar notas para Trimestres 1 e 2 completos, e Trimestre 3 parcial
                for ($term = 1; $term <= 3; $term++) {
                    // Determinar perfil do aluno (bom aluno, mediano, com dificuldades)
                    $basePerformance = rand(8, 18);

                    foreach ($assessmentTypes as $type) {
                        // Variabilidade realista na nota
                        $gradeVal = min(20, max(0, round($basePerformance + (rand(-30, 30) / 10), 1)));

                        Grade::create([
                            'student_id' => $student->id,
                            'class_id' => $class->id,
                            'subject_id' => $subject->id,
                            'assessment_type' => $type,
                            'grade' => $gradeVal,
                            'term' => $term,
                            'year' => $year,
                            'date_recorded' => now()->subMonths(12 - ($term * 3))->addDays(rand(1, 20)),
                            'teacher_id' => $class->teacher_id ?? $teachers->random()->id ?? null,
                            'comments' => $gradeVal >= 14 ? 'Excelente progresso.' : ($gradeVal < 10 ? 'Necessita de acompanhamento.' : 'Bom desempenho.'),
                        ]);
                    }
                }
            }
        }
    }

    private function createAttendance($enrollments)
    {
        $admin = User::first();
        $startDate = now()->subDays(45);

        foreach ($enrollments as $enrollment) {
            $current = clone $startDate;

            while ($current->isBefore(now())) {
                // Pular fins-de-semana
                if (!$current->isWeekend()) {
                    // 90% chance de presença, 5% atraso, 5% falta
                    $rand = rand(1, 100);
                    $status = match (true) {
                        $rand <= 88 => 'present',
                        $rand <= 94 => 'late',
                        $rand <= 97 => 'excused',
                        default => 'absent',
                    };

                    Attendance::create([
                        'student_id' => $enrollment->student_id,
                        'class_id' => $enrollment->class_id,
                        'attendance_date' => $current->format('Y-m-d'),
                        'status' => $status,
                        'arrival_time' => $status === 'late' ? $current->copy()->setHour(8)->setMinute(rand(15, 45)) : null,
                        'marked_by' => $admin->id,
                        'notes' => $status === 'excused' ? 'Atestado médico entregue.' : null,
                    ]);
                }

                $current->addDay();
            }
        }
    }

    private function createPayments($enrollments)
    {
        $year = current_school_year();
        $methods = ['mpesa', 'emola', 'bank', 'cash'];

        foreach ($enrollments as $enrollment) {
            // Pagamento de Matrícula
            Payment::create([
                'reference_number' => 'ZAM-MAT-' . $enrollment->student_id . '-' . $year . '-' . rand(100, 999),
                'student_id' => $enrollment->student_id,
                'enrollment_id' => $enrollment->id,
                'type' => 'matricula',
                'amount' => 2500.00,
                'month' => 1,
                'year' => $year,
                'due_date' => Carbon::create($year, 1, 15),
                'payment_date' => Carbon::create($year, 1, rand(5, 14)),
                'status' => 'paid',
                'payment_method' => $methods[array_rand($methods)],
            ]);

            // Mensalidades de Janeiro a Dezembro
            for ($m = 1; $m <= 12; $m++) {
                $dueDate = Carbon::create($year, $m, $enrollment->payment_day ?? 10);
                
                // Definir status realista
                if ($dueDate->isPast()) {
                    $randStatus = rand(1, 100);
                    if ($randStatus <= 82) {
                        $status = 'paid';
                        $paymentDate = $dueDate->copy()->subDays(rand(0, 5));
                        $penalty = 0;
                    } elseif ($randStatus <= 92) {
                        $status = 'paid';
                        $paymentDate = $dueDate->copy()->addDays(rand(3, 15));
                        $penalty = 230.00; // 10% multa
                    } else {
                        $status = 'overdue';
                        $paymentDate = null;
                        $penalty = 230.00;
                    }
                } else {
                    $status = rand(1, 100) <= 30 ? 'paid' : 'pending';
                    $paymentDate = $status === 'paid' ? now() : null;
                    $penalty = 0;
                }

                Payment::create([
                    'reference_number' => 'ZAM-MENS-' . $enrollment->student_id . '-' . sprintf('%02d', $m) . '-' . $year . '-' . rand(100, 999),
                    'student_id' => $enrollment->student_id,
                    'enrollment_id' => $enrollment->id,
                    'type' => 'mensalidade',
                    'amount' => $enrollment->monthly_fee ?? 2300.00,
                    'month' => $m,
                    'year' => $year,
                    'due_date' => $dueDate,
                    'payment_date' => $paymentDate,
                    'status' => $status,
                    'payment_method' => $status === 'paid' ? $methods[array_rand($methods)] : null,
                    'penalty' => $penalty,
                ]);
            }
        }
    }

    private function createEvents()
    {
        $admin = User::first();

        $eventData = [
            [
                'title' => 'Reunião Geral de Pais e Encarregados',
                'description' => 'Apresentação do relatório de desempenho do 1º Trimestre e plano pedagógico.',
                'event_date' => now()->addDays(10),
                'type' => 'meeting',
                'target_audience' => 'parents',
            ],
            [
                'title' => 'Feira das Ciências e Tecnologia ZamEdu',
                'description' => 'Exposição de projetos de inovação criados pelos alunos da 10ª à 12ª classe.',
                'event_date' => now()->addDays(25),
                'type' => 'activity',
                'target_audience' => 'all',
            ],
            [
                'title' => 'Exames Trimestrais de Avaliação (ACP)',
                'description' => 'Período oficial de exames parciais do 2º Trimestre.',
                'event_date' => now()->addDays(40),
                'type' => 'exam',
                'target_audience' => 'students',
            ],
        ];

        foreach ($eventData as $e) {
            Event::create([
                'title' => $e['title'],
                'description' => $e['description'],
                'event_date' => $e['event_date'],
                'start_time' => now()->setHour(9)->setMinute(0),
                'end_time' => now()->setHour(12)->setMinute(0),
                'type' => $e['type'],
                'target_audience' => $e['target_audience'],
                'created_by' => $admin->id ?? 1,
                'send_notification' => true,
            ]);
        }
    }

    private function createLeaveRequests($teachers)
    {
        foreach ($teachers->take(3) as $teacher) {
            StaffLeaveRequest::create([
                'staff_id' => $teacher->id,
                'leave_type' => 'sick',
                'start_date' => now()->subDays(rand(2, 10)),
                'end_date' => now()->addDays(rand(1, 3)),
                'reason' => 'Consulta médica especialista e recuperação.',
                'status' => 'approved',
            ]);
        }
    }
}