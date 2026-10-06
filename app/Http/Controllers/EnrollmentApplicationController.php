<?php

namespace App\Http\Controllers;

use App\Models\EnrollmentApplication;
use App\Models\EnrollmentDocument;
use App\Models\FeeType;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Models\User;
use App\Models\ParentModel;
use App\Models\Payment;
use App\Models\SchoolAllocation;
use App\Models\Province;
use App\Models\District;
use App\Models\School;
use App\Services\AcademicYearService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EnrollmentApplicationController extends Controller
{
    /**
     * Quadro oficial de vagas escolares abertas no SNE.
     */
    public function vacancies(Request $request)
    {
        $academicYear = next_school_year();
        $provinces = Province::where('is_active', true)->with(['districts.zips.schools'])->get();

        $provinceId = $request->query('province_id');
        $districtId = $request->query('district_id');
        $level = $request->query('level', 'all');
        $type = $request->query('type', 'all');

        $districts = $provinceId
            ? District::where('province_id', $provinceId)->where('is_active', true)->get()
            : District::where('is_active', true)->get();

        $schoolsQuery = School::with(['province', 'district', 'zip', 'classRooms'])
            ->where('is_active', true);

        if ($provinceId) {
            $schoolsQuery->where('province_id', $provinceId);
        }

        if ($districtId) {
            $schoolsQuery->where('district_id', $districtId);
        }

        if ($type && $type !== 'all') {
            $schoolsQuery->where('type', $type);
        }

        $schools = $schoolsQuery->paginate(12)->withQueryString();

        return view('public.vacancies', compact(
            'academicYear',
            'provinces',
            'districts',
            'provinceId',
            'districtId',
            'level',
            'type',
            'schools'
        ));
    }

    /**
     * Consulta pública de afetação oficial de concluintes do primário (6ª -> 7ª classe).
     */
    public function allocations(Request $request)
    {
        $academicYear = next_school_year();
        $search = $request->query('search');
        $provinceId = $request->query('province_id');
        $districtId = $request->query('district_id');

        $provinces = Province::where('is_active', true)->get();
        $districts = $provinceId
            ? District::where('province_id', $provinceId)->where('is_active', true)->get()
            : District::where('is_active', true)->get();

        $query = SchoolAllocation::with(['destinationSchool.province', 'destinationSchool.district', 'originSchool'])
            ->where('academic_year', $academicYear);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('student_code', 'like', "%{$search}%")
                  ->orWhere('student_name', 'like', "%{$search}%");
            });
        }

        if ($provinceId) {
            $query->where('province_id', $provinceId);
        }

        if ($districtId) {
            $query->where('district_id', $districtId);
        }

        $allocations = $query->latest()->paginate(9)->withQueryString();

        return view('public.allocation_check', compact(
            'academicYear',
            'search',
            'provinceId',
            'districtId',
            'provinces',
            'districts',
            'allocations'
        ));
    }

    /**
     * Show the public pre-enrollment form.
     */
    public function create(Request $request)
    {
        $classes = ClassRoom::all();
        $academicYear = next_school_year(); // Default for next year
        $fees = FeeType::where('academic_year', $academicYear)->get();
        $provinces = Province::where('is_active', true)->with(['districts.zips.schools'])->get();
        $schools = School::with(['province', 'district', 'zip'])->get();
        $selectedSchoolId = $request->query('school_id');

        $allocation = null;
        if ($request->filled('allocation_id')) {
            $allocation = SchoolAllocation::with(['destinationSchool.province', 'destinationSchool.district', 'originSchool'])->find($request->query('allocation_id'));
            if ($allocation && $allocation->destination_school_id) {
                $selectedSchoolId = $allocation->destination_school_id;
            }
        }

        return view('enrollments.applications.create', compact('classes', 'academicYear', 'fees', 'provinces', 'schools', 'selectedSchoolId', 'allocation'));
    }

    /**
     * Store a new pre-enrollment application.
     */
    public function store(Request $request)
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'allocation_id' => 'nullable|exists:school_allocations,id',
            'student.first_name' => 'required|string|max:255',
            'student.last_name' => 'required|string|max:255',
            'student.birth_date' => 'required|date',
            'student.gender' => 'required|in:male,female',
            'student.grade_level' => 'required|string',
            'student.shift' => 'nullable|string|max:50',
            'student.address' => 'required|string|max:500',
            'student.emergency_contact' => 'required|string|max:255',
            'student.emergency_phone' => 'required|string|max:20',
            'student.special_needs_description' => 'nullable|string|max:1000',
            'parent.first_name' => 'required|string|max:255',
            'parent.last_name' => 'required|string|max:255',
            'parent.phone' => 'required|string|max:20',
            'parent.relationship' => 'nullable|string|max:50',
            'parent.email' => 'nullable|email|max:255',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        try {
            DB::beginTransaction();

            $academicYear = next_school_year();
            $schoolId = $request->input('school_id');

            // Calculate total fees
            $gradeLevel = $request->input('student.grade_level');
            $fees = FeeType::where('academic_year', $academicYear)
                ->where(function ($query) use ($gradeLevel) {
                    $query->where('grade_level', $gradeLevel)
                        ->orWhereNull('grade_level');
                })
                ->where('is_mandatory', true)
                ->get();

            $totalAmount = $fees->sum('amount');

            $studentData = $request->student;
            $studentData['school_id'] = $schoolId;

            $application = EnrollmentApplication::create([
                'school_id' => $schoolId,
                'type' => 'NEW',
                'status' => 'PENDING',
                'student_data' => $studentData,
                'parent_data' => $request->parent,
                'academic_year' => $academicYear,
                'total_amount' => $totalAmount,
                'submitted_at' => now(),
            ]);

            // Handle document uploads
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $type => $file) {
                    $path = $file->store('enrollment_documents/' . $application->id, 'public');
                    EnrollmentDocument::create([
                        'application_id' => $application->id,
                        'document_type' => $type,
                        'file_path' => $path,
                    ]);
                }
            }

            // If an allocation was confirmed with this pre-enrollment
            if ($request->filled('allocation_id')) {
                $allocation = SchoolAllocation::find($request->input('allocation_id'));
                if ($allocation) {
                    $allocation->status = 'confirmed';
                    $allocation->save();
                }
            }

            DB::commit();

            return redirect()->route('public.pre-enrollment.success', $application->id)
                ->with('success', 'Sua pré-inscrição foi enviada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao enviar pré-inscrição: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the renewal form for parents.
     */
    public function renewalCreate(Student $student)
    {
        // Verificar se o aluno pertence ao pai logado
        $parent = \App\Models\ParentModel::where('user_id', auth()->id())->firstOrFail();
        if ($student->parent_id !== $parent->user_id) {
            abort(403, 'Acesso não autorizado.');
        }

        $academicYear = app(AcademicYearService::class)->getNextYear();
        $fees = FeeType::where('academic_year', $academicYear)->get();

        // Buscar dívidas pendentes
        $pendingDebts = Payment::where('student_id', $student->id)
            ->whereIn('status', ['pending', 'overdue'])
            ->get();

        return view('enrollments.applications.renewal', compact('student', 'academicYear', 'fees', 'pendingDebts'));
    }

    /**
     * Store a renewal application.
     */
    public function renewalStore(Request $request, Student $student)
    {
        $this->authorize('update', $student);

        $request->validate([
            'parent.phone' => 'required|string|max:20',
            'parent.email' => 'nullable|email|max:255',
            'student.address' => 'required|string|max:500',
            'student.emergency_contact' => 'required|string|max:255',
            'student.emergency_phone' => 'required|string|max:20',
            'student.has_special_needs' => 'boolean',
            'student.special_needs_description' => 'nullable|string',
            'medical_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'passport_photo' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'terms_accepted' => 'accepted',
        ]);

        try {
            DB::beginTransaction();

            $academicYear = app(AcademicYearService::class)->getNextYear();

            // Calculate total fees
            $gradeLevel = $student->currentClass->grade_level ?? 'primary'; // Fallback ou buscar da última matrícula
            $fees = FeeType::where('academic_year', $academicYear)
                ->where(function ($query) use ($gradeLevel) {
                    $query->where('grade_level', $gradeLevel)
                        ->orWhereNull('grade_level');
                })
                ->where('is_mandatory', true)
                ->get();

            $totalAmount = $fees->sum('amount');

            $studentData = array_merge($request->student, [
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'birth_date' => $student->birth_date,
                'gender' => $student->gender,
            ]);

            // Handle file uploads
            if ($request->hasFile('medical_certificate')) {
                $studentData['medical_certificate'] = $request->file('medical_certificate')->store('student_documents/' . $student->id, 'public');
            }
            if ($request->hasFile('passport_photo')) {
                $studentData['passport_photo'] = $request->file('passport_photo')->store('student_photos/' . $student->id, 'public');
            }

            $application = EnrollmentApplication::create([
                'type' => 'RENEWAL',
                'status' => 'PENDING',
                'student_id' => $student->id,
                'student_data' => $studentData,
                'parent_data' => $request->parent,
                'academic_year' => $academicYear,
                'total_amount' => $totalAmount,
                'submitted_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('parent.dashboard')
                ->with('success', 'Pedido de renovação enviado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao enviar renovação: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Secretary: List applications.
     */
    public function index(Request $request)
    {
        $query = EnrollmentApplication::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $applications = $query->latest()->paginate(20);

        return view('enrollments.applications.index', compact('applications'));
    }

    /**
     * Secretary: Show application details.
     */
    public function show(EnrollmentApplication $application)
    {
        $application->load('documents', 'student');
        return view('enrollments.applications.show', compact('application'));
    }

    /**
     * Secretary: Update status.
     */
    public function updateStatus(Request $request, EnrollmentApplication $application)
    {
        $request->validate([
            'status' => 'required|string',
            'admin_notes' => 'nullable|string',
        ]);

        $application->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
        ]);

        if ($request->status === 'APPROVED') {
            $application->update(['approved_at' => now()]);
        }

        return back()->with('success', 'Status da aplicação atualizado!');
    }

    /**
     * Secretary: Confirm payment.
     */
    public function confirmPayment(Request $request, EnrollmentApplication $application)
    {
        $request->validate([
            'payment_reference' => 'required|string',
            'payment_date' => 'required|date',
            'payment_proof' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $path = null;
        if ($request->hasFile('payment_proof')) {
            $path = $request->file('payment_proof')->store('payment_proofs', 'public');
        }

        $application->update([
            'payment_status' => 'PAID',
            'payment_reference' => $request->payment_reference,
            'payment_date' => $request->payment_date,
            'payment_proof_path' => $path,
            'status' => 'DOCUMENT_DELIVERED', // Automatically move to next step
        ]);

        return back()->with('success', 'Pagamento confirmado com sucesso!');
    }

    /**
     * Secretary: Finalize enrollment (Convert to Student).
     */
    public function finalize(Request $request, EnrollmentApplication $application)
    {
        if ($application->status !== 'APPROVED' || $application->payment_status !== 'PAID') {
            return back()->with('error', 'A aplicação deve estar APROVADA e PAGA para ser finalizada.');
        }

        $request->validate([
            'class_id' => 'required|exists:classes,id',
        ]);

        try {
            DB::beginTransaction();

            // 1. Create or update Parent
            $parent = null;
            if ($application->type === 'NEW') {
                // Check if user already exists by email
                $user = User::where('email', $application->parent_data['email'])->first();

                if (!$user) {
                    // Create new user for parent
                    $user = User::create([
                        'name' => $application->parent_data['first_name'] . ' ' . $application->parent_data['last_name'],
                        'email' => $application->parent_data['email'],
                        'password' => Hash::make(Str::random(12)), // Random password, parent should reset it
                        'phone' => $application->parent_data['phone'],
                        'status' => 'active',
                    ]);
                    $user->assignRole('parent');
                }

                $parent = ParentModel::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'first_name' => $application->parent_data['first_name'],
                        'last_name' => $application->parent_data['last_name'],
                        'phone' => $application->parent_data['phone'],
                        'email' => $application->parent_data['email'],
                        'relationship' => $application->parent_data['relationship'] ?? 'Other',
                    ]
                );
            } else {
                $student = Student::find($application->student_id);
                $parent = $student->parent;

                // Update parent contact info if changed during renewal
                if ($parent) {
                    $parent->update([
                        'phone' => $application->parent_data['phone'] ?? $parent->phone,
                        'email' => $application->parent_data['email'] ?? $parent->email,
                    ]);

                    if ($parent->user) {
                        $parent->user->update([
                            'phone' => $application->parent_data['phone'] ?? $parent->user->phone,
                            'email' => $application->parent_data['email'] ?? $parent->user->email,
                        ]);
                    }
                }
            }

            // 2. Create or Update Student
            if ($application->type === 'NEW') {
                // Fetch document paths from EnrollmentDocument
                $passportPhoto = EnrollmentDocument::where('application_id', $application->id)
                    ->where('document_type', 'photo')
                    ->first()?->file_path;

                $medicalCert = EnrollmentDocument::where('application_id', $application->id)
                    ->where('document_type', 'medical')
                    ->first()?->file_path;

                // Get dynamic monthly fee based on grade level
                $gradeLevel = $application->student_data['grade_level'] ?? 'primary';
                $monthlyFee = FeeType::where('academic_year', $application->academic_year)
                    ->where('grade_level', $gradeLevel)
                    ->where('code', 'LIKE', 'TUI_%')
                    ->first()?->amount ?? 2300.00;

                $student = Student::create([
                    'first_name' => $application->student_data['first_name'],
                    'last_name' => $application->student_data['last_name'],
                    'birthdate' => $application->student_data['birth_date'] ?? $application->student_data['birthdate'],
                    'gender' => $application->student_data['gender'],
                    'parent_id' => $parent->user_id,
                    'status' => 'active',
                    'student_number' => 'VIS' . current_school_year() . str_pad(Student::count() + 1, 4, '0', STR_PAD_LEFT),
                    'address' => $application->student_data['address'] ?? null,
                    'emergency_contact' => $application->student_data['emergency_contact'] ?? null,
                    'emergency_phone' => $application->student_data['emergency_phone'] ?? null,
                    'has_special_needs' => $application->student_data['has_special_needs'] ?? false,
                    'special_needs_description' => $application->student_data['special_needs_description'] ?? null,
                    'medical_certificate' => $medicalCert ?? ($application->student_data['medical_certificate'] ?? null),
                    'passport_photo' => $passportPhoto ?? ($application->student_data['passport_photo'] ?? null),
                    'registration_date' => now(),
                    'monthly_fee' => $monthlyFee,
                ]);
            } else {
                $student = Student::find($application->student_id);

                // Update student data from application
                $student->update([
                    'address' => $application->student_data['address'] ?? $student->address,
                    'emergency_contact' => $application->student_data['emergency_contact'] ?? $student->emergency_contact,
                    'emergency_phone' => $application->student_data['emergency_phone'] ?? $student->emergency_phone,
                    'has_special_needs' => $application->student_data['has_special_needs'] ?? $student->has_special_needs,
                    'special_needs_description' => $application->student_data['special_needs_description'] ?? $student->special_needs_description,
                    'medical_certificate' => $application->student_data['medical_certificate'] ?? $student->medical_certificate,
                    'passport_photo' => $application->student_data['passport_photo'] ?? $student->passport_photo,
                ]);
            }

            // 3. Create Enrollment
            $gradeLevel = $student->currentClass->grade_level ?? $application->student_data['grade_level'] ?? 'primary';
            $monthlyFee = FeeType::where('academic_year', $application->academic_year)
                ->where('grade_level', $gradeLevel)
                ->where('code', 'LIKE', 'TUI_%')
                ->first()?->amount ?? 2300.00;

            \App\Models\Enrollment::create([
                'student_id' => $student->id,
                'class_id' => $request->class_id,
                'school_year' => $application->academic_year,
                'enrollment_date' => now(),
                'status' => 'active',
                'monthly_fee' => $monthlyFee,
                'payment_day' => 10,
            ]);

            // 4. Update Application
            $application->update([
                'status' => 'ENROLLED',
                'student_id' => $student->id,
            ]);

            DB::commit();

            return redirect()->route('students.show', $student->id)
                ->with('success', 'Matrícula finalizada com sucesso! Aluno agora está ativo.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao finalizar matrícula: ' . $e->getMessage());
        }
    }
}
