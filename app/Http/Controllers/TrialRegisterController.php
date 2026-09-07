<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\License;
use App\Models\Setting;
use App\Models\Province;
use App\Models\District;
use App\Models\School;
use App\Services\SchoolContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TrialRegisterController extends Controller
{
    /**
     * Formulário de Registo de Escola — Wizard Multi-Step.
     */
    public function create()
    {
        return view('public.trial-register');
    }

    /**
     * Processa o registo automático da escola e criação do ambiente.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Step 1: Dados da Escola
            'school_name' => 'required|string|max:255',
            'institution_type' => 'required|in:escola_privada,escola_publica',
            'province' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'education_level' => 'nullable|string|max:100',
            'estimated_students' => 'nullable|integer|min:10|max:5000',

            // Step 2: Conta do Administrador
            'director_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'required|string|max:30',
            'password' => 'required|string|min:6|confirmed',

            // Step 3: Configuração Inicial
            'academic_year' => 'nullable|string|max:9',
            'grading_scale' => 'nullable|in:0_20,0_10,A_F',
            'shift_morning_late' => 'nullable|date_format:H:i',
            'shift_afternoon_late' => 'nullable|date_format:H:i',
        ], [
            'school_name.required' => 'Por favor, indique o nome da escola.',
            'institution_type.required' => 'Selecione o tipo de instituição.',
            'director_name.required' => 'Por favor, indique o nome do responsável.',
            'email.required' => 'O endereço de e-mail é obrigatório.',
            'email.unique' => 'Este e-mail já se encontra registado no sistema.',
            'password.required' => 'Por favor, defina uma palavra-passe.',
            'password.min' => 'A palavra-passe deve ter pelo menos 6 caracteres.',
            'password.confirmed' => 'A confirmação da palavra-passe não coincide.',
        ]);

        DB::transaction(function () use ($validated, &$user) {
            // 1. Resolver Província e Distrito
            $province = Province::where('name', 'LIKE', '%' . ($validated['province'] ?? 'Maputo') . '%')->first()
                ?? Province::firstOrCreate(
                    ['code' => 'MPM'],
                    ['name' => $validated['province'] ?? 'Maputo', 'capital' => 'Maputo', 'is_active' => true]
                );

            $district = District::where('name', 'LIKE', '%' . ($validated['district'] ?? 'KaMpfumo') . '%')->first()
                ?? District::firstOrCreate(
                    ['code' => 'MPM-KMP'],
                    ['province_id' => $province->id, 'name' => $validated['district'] ?? 'KaMpfumo', 'is_active' => true]
                );

            // 2. Criar a Escola no Modelo Territorial Multi-Tenant
            $school = School::create([
                'province_id' => $province->id,
                'district_id' => $district->id,
                'name' => $validated['school_name'],
                'code' => 'ESC-' . strtoupper(Str::random(6)),
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'status' => 'active',
            ]);

            SchoolContext::setSchoolId($school->id);

            // 3. Criar Utilizador Administrador da Escola
            $user = User::create([
                'school_id' => $school->id,
                'name' => $validated['director_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            $user->assignRole('admin');

            // 4. Definir Configurações Iniciais da Escola
            Setting::set('school_name', $validated['school_name']);
            Setting::set('institution_type', $validated['institution_type'], 'identity');
            Setting::set('school_phone', $validated['phone']);
            Setting::set('school_province', $validated['province'] ?? 'Maputo');
            Setting::set('school_district', $validated['district'] ?? '');
            Setting::set('education_level', $validated['education_level'] ?? 'secundario');
            Setting::set('estimated_students', $validated['estimated_students'] ?? 250);

            // Step 3 settings
            Setting::set('current_academic_year', $validated['academic_year'] ?? date('Y'), 'academic');
            Setting::set('grading_scale', $validated['grading_scale'] ?? '0_20', 'academic');
            Setting::set('shift_morning_late_time', $validated['shift_morning_late'] ?? '07:30', 'academic');
            Setting::set('shift_afternoon_late_time', $validated['shift_afternoon_late'] ?? '13:00', 'academic');

            // 5. Gerar Licença de Avaliação Gratuita (15 Dias)
            License::create([
                'school_id' => $school->id,
                'client_name' => $validated['school_name'],
                'license_key' => 'TRIAL-' . strtoupper(Str::random(12)),
                'activated_at' => now(),
                'expires_at' => now()->addDays(15),
                'grace_period_days' => 5,
                'status' => 'active',
                'plan' => 'Gestão Completa (15 Dias Grátis)',
                'signature' => hash('sha256', $validated['school_name'] . now()),
            ]);

            // 4. Marcar setup como concluído
            Setting::set('onboarding_completed', 'true', 'system');
        });

        // 5. Autenticar Automaticamente o Novo Administrador
        Auth::login($user);

        $typeLabel = $validated['institution_type'] === 'escola_publica' ? 'Pública' : 'Privada';
        return redirect()->route('dashboard')->with('success',
            "🎉 Parabéns! O ambiente da escola '{$validated['school_name']}' (Escola {$typeLabel}) foi criado com sucesso! Tem 15 dias de avaliação gratuita."
        );
    }
}

