<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use App\Models\Province;
use App\Models\District;
use App\Models\Zip;
use App\Models\License;
use App\Models\Setting;
use App\Services\SchoolContext;
use App\Services\HierarchyScopeResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SchoolOnboardingController extends Controller
{
    protected HierarchyScopeResolver $scopeResolver;

    public function __construct(HierarchyScopeResolver $scopeResolver)
    {
        $this->scopeResolver = $scopeResolver;
    }

    /**
     * Display Multi-School Onboarding Wizard Form
     */
    public function wizard(Request $request)
    {
        $provinces = Province::where('is_active', true)->get();
        $districts = District::where('is_active', true)->get();
        $zips = Zip::where('is_active', true)->get();

        return view('onboarding.wizard', compact('provinces', 'districts', 'zips'));
    }

    /**
     * Store new school onboarding data & initialize environment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Step 1: Territorial Hierarchy
            'province_id' => 'required|exists:provinces,id',
            'district_id' => 'required|exists:districts,id',
            'zip_id' => 'required|exists:zips,id',

            // Step 2: School Details
            'school_name' => 'required|string|max:255',
            'school_code' => 'nullable|string|max:50|unique:schools,code',
            'type' => 'required|in:public,private,community',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:30',
            'address' => 'nullable|string|max:255',

            // Step 3: Primary Administrator Account
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',

            // Step 4: System Configurations
            'academic_year' => 'nullable|string|max:9',
            'passing_grade' => 'nullable|numeric|min:0|max:20',
        ], [
            'province_id.required' => 'Por favor, selecione a província.',
            'district_id.required' => 'Por favor, selecione o distrito.',
            'zip_id.required' => 'Por favor, selecione a ZIP.',
            'school_name.required' => 'O nome da escola é obrigatório.',
            'admin_name.required' => 'O nome do administrador é obrigatório.',
            'admin_email.required' => 'O e-mail do administrador é obrigatório.',
            'admin_email.unique' => 'Este e-mail de administrador já se encontra registado no sistema.',
            'password.required' => 'Por favor, defina a palavra-passe.',
            'password.confirmed' => 'A confirmação da palavra-passe não coincide.',
        ]);

        $newSchool = null;
        $adminUser = null;

        DB::transaction(function () use ($validated, &$newSchool, &$adminUser) {
            $schoolCode = $validated['school_code'] ?? ('ESC-' . strtoupper(Str::random(6)));

            // 1. Create School Record
            $newSchool = School::create([
                'province_id' => $validated['province_id'],
                'district_id' => $validated['district_id'],
                'zip_id' => $validated['zip_id'],
                'name' => $validated['school_name'],
                'code' => $schoolCode,
                'type' => $validated['type'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'] ?? '',
                'principal_name' => $validated['admin_name'],
                'is_active' => true,
            ]);

            // Set context for initial seeds
            SchoolContext::setSchoolId($newSchool->id);

            // 2. Create Primary Admin User
            $adminUser = User::create([
                'school_id' => $newSchool->id,
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'password' => Hash::make($validated['password']),
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            $adminUser->assignRole('admin');

            // 3. Initialize Default Settings for the School
            Setting::updateOrCreate(
                ['school_id' => $newSchool->id, 'key' => 'school_name'],
                ['value' => $validated['school_name']]
            );

            Setting::updateOrCreate(
                ['school_id' => $newSchool->id, 'key' => 'passing_grade'],
                ['value' => $validated['passing_grade'] ?? '9.5']
            );

            Setting::updateOrCreate(
                ['school_id' => $newSchool->id, 'key' => 'current_academic_year'],
                ['value' => $validated['academic_year'] ?? '2026']
            );

            Setting::updateOrCreate(
                ['school_id' => $newSchool->id, 'key' => 'grading_scale'],
                ['value' => '0_20']
            );

            // 4. Initialize License for School
            License::create([
                'school_id' => $newSchool->id,
                'client_name' => $validated['school_name'],
                'license_key' => 'ZAMEDU-ONBOARD-' . strtoupper(Str::random(12)),
                'status' => 'active',
                'plan' => $validated['type'] === 'public' ? 'government' : 'enterprise',
                'activated_at' => now(),
                'expires_at' => now()->addYear(),
            ]);
        });

        return redirect()->route('onboarding.school.success', $newSchool->id)
            ->with('success', 'Escola integrada com sucesso no ecossistema SIGE / ZAMEDU!');
    }

    /**
     * Display Onboarding Success Dashboard
     */
    public function success(School $school)
    {
        $school->load(['province', 'district', 'zip']);
        $adminUser = User::where('school_id', $school->id)->first();

        return view('onboarding.success', compact('school', 'adminUser'));
    }
}
