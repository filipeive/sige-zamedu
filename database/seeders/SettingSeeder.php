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
            ['key' => 'school_name', 'value' => 'ZamEdu', 'group' => 'identity'],
            ['key' => 'school_short_name', 'value' => 'ZamEdu', 'group' => 'identity'],
            ['key' => 'logo_url', 'value' => '/images/logo.png', 'group' => 'identity'],
            ['key' => 'primary_color', 'value' => '#1B5E20', 'group' => 'identity'],
            ['key' => 'secondary_color', 'value' => '#FFB300', 'group' => 'identity'],
            ['key' => 'institution_type', 'value' => 'escola_privada', 'group' => 'identity'],
            ['key' => 'grading_scale', 'value' => '0_20', 'group' => 'academic'],
            
            // Contactos & Localização
            ['key' => 'phone', 'value' => '+258 84 000 0000', 'group' => 'contact'],
            ['key' => 'email', 'value' => 'contacto@zamedu.co.mz', 'group' => 'contact'],
            ['key' => 'address', 'value' => 'Maputo, Moçambique', 'group' => 'contact'],
            
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
            ['key' => 'demo_email', 'value' => 'demo@zamedu.co.mz', 'group' => 'system'],
        ];

        foreach ($defaultSettings as $item) {
            Setting::updateOrCreate(
                ['key' => $item['key']],
                ['value' => $item['value'], 'group' => $item['group']]
            );
        }

        // Garantir Licença Ativa Ilimitada no Sistema
        \App\Models\License::updateOrCreate(
            ['id' => 1],
            [
                'client_name' => 'ZamEdu School',
                'license_key' => 'UNLIMITED-ZAMEDU-KEY',
                'status' => 'active',
                'expires_at' => null,
                'plan' => 'enterprise',
            ]
        );
    }
}
