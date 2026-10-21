<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultSettings = [
            // Identidade da Instituição
            ['key' => 'school_name', 'value' => 'Escola dos Visionários', 'group' => 'identity'],
            ['key' => 'school_short_name', 'value' => 'Visionários', 'group' => 'identity'],
            ['key' => 'slogan', 'value' => 'Aqui se Prepara a Nova Geração', 'group' => 'identity'],
            ['key' => 'system_name', 'value' => 'Visionários School System', 'group' => 'identity'],
            ['key' => 'logo_url', 'value' => '/images/logo-visionarios.png', 'group' => 'identity'],
            ['key' => 'primary_color', 'value' => '#0F766E', 'group' => 'identity'],
            ['key' => 'secondary_color', 'value' => '#F59E0B', 'group' => 'identity'],
            ['key' => 'institution_type', 'value' => 'escola_privada', 'group' => 'identity'],
            ['key' => 'grading_scale', 'value' => '0_20', 'group' => 'academic'],
            
            // Contactos & Localização
            ['key' => 'phone', 'value' => '+258 85 558 0079 / +258 87 656 6029', 'group' => 'contact'],
            ['key' => 'email', 'value' => 'contacto@visionarios.co.mz', 'group' => 'contact'],
            ['key' => 'address', 'value' => 'Av. Eduardo Mondlane, Bairro Mapiazua, Quelimane', 'group' => 'contact'],
            
            // Académico
            ['key' => 'current_academic_year', 'value' => '2025', 'group' => 'academic'],
            ['key' => 'next_academic_year', 'value' => '2026', 'group' => 'academic'],
            
            // Turnos & Horários de Atraso
            ['key' => 'shift_morning_late_time', 'value' => '07:30', 'group' => 'academic'],
            ['key' => 'shift_afternoon_late_time', 'value' => '13:00', 'group' => 'academic'],
            ['key' => 'shift_night_late_time', 'value' => '18:00', 'group' => 'academic'],

            // Avaliações & Médias
            ['key' => 'passing_grade', 'value' => '10', 'group' => 'grading'],
            ['key' => 'grading_scale', 'value' => '0_20', 'group' => 'grading'],
            ['key' => 'min_terms_for_final_grade', 'value' => '3', 'group' => 'grading'],
            ['key' => 'macs_weight', 'value' => '2', 'group' => 'grading'],
            ['key' => 'acp_weight_in_mt', 'value' => '1', 'group' => 'grading'],
            ['key' => 'continuous_weight', 'value' => '66.67', 'group' => 'grading'],
            ['key' => 'exam_weight', 'value' => '33.33', 'group' => 'grading'],
            ['key' => 'default_room_capacity', 'value' => '45', 'group' => 'grading'],
            ['key' => 'max_unexcused_absences', 'value' => '15', 'group' => 'grading'],

            // Sistema & Contas Demo
            ['key' => 'demo_email', 'value' => 'dono@visionarios.co.mz', 'group' => 'system'],
        ];

        foreach ($defaultSettings as $item) {
            Setting::updateOrCreate(
                ['key' => $item['key']],
                ['value' => $item['value'], 'group' => $item['group']]
            );
        }

        // Garantir Licença Ativa Ilimitada no Sistema para as filiais da Escola dos Visionários
        $licenseSchoolId = \App\Models\School::where('code', 'VIS-QUE')->value('id') ?? \App\Models\School::value('id');
        if (!$licenseSchoolId) {
            return;
        }

        \App\Models\License::withoutGlobalScopes()->updateOrCreate(
            ['school_id' => $licenseSchoolId],
            [
                'client_name' => 'Escola dos Visionários — Quelimane (Sede)',
                'license_key' => 'VISIONARIOS-ENTERPRISE-UNLIMITED',
                'status' => 'active',
                'expires_at' => null,
                'plan' => 'enterprise',
            ]
        );

        $molocueSchoolId = \App\Models\School::where('code', 'VIS-MOL')->value('id');
        if ($molocueSchoolId) {
            \App\Models\License::withoutGlobalScopes()->updateOrCreate(
                ['school_id' => $molocueSchoolId],
                [
                    'client_name' => 'Escola dos Visionários — Alto Molócuè (Filial)',
                    'license_key' => 'VISIONARIOS-MOLOCUE-ENTERPRISE',
                    'status' => 'active',
                    'expires_at' => null,
                    'plan' => 'enterprise',
                ]
            );
        }
    }
}
