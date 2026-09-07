<?php

namespace App\Console\Commands;

use App\Models\School;
use App\Models\Province;
use App\Models\District;
use App\Models\Zip;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BackfillSchoolTenant extends Command
{
    protected $signature = 'schools:backfill {--school_code=ESG-PAZ : Código da escola piloto a associar aos registos órfãos}';

    protected $description = 'Preenche a coluna school_id em todas as tabelas operacionais associando registos existentes à escola piloto.';

    public function handle(): int
    {
        $this->info('Iniciando o processo de backfill do tenant de escola...');

        $schoolCode = $this->option('school_code');
        $school = School::where('code', $schoolCode)->first();

        if (! $school) {
            $this->warn("Escola com código {$schoolCode} não encontrada. Criando escola piloto...");

            $province = Province::firstOrCreate(
                ['code' => 'ZAM'],
                ['name' => 'Zambézia', 'capital' => 'Quelimane', 'is_active' => true]
            );

            $district = District::firstOrCreate(
                ['code' => 'ZAM-NAM'],
                ['province_id' => $province->id, 'name' => 'Namacurra', 'is_active' => true]
            );

            $zip = Zip::firstOrCreate(
                ['code' => 'ZIP-01-NAM'],
                ['district_id' => $district->id, 'name' => 'ZIP Nº 01 - Namacurra Sede', 'is_active' => true]
            );

            $school = School::create([
                'province_id' => $province->id,
                'district_id' => $district->id,
                'zip_id' => $zip->id,
                'name' => 'Escola Comunitária Escola da Paz',
                'code' => $schoolCode,
                'type' => 'community',
                'is_active' => true,
            ]);
        }

        $this->info("Escola de destino: {$school->name} (ID: {$school->id})");

        $tables = [
            'students',
            'teachers',
            'classes',
            'subjects',
            'class_schedules',
            'class_subjects',
            'enrollments',
            'enrollment_applications',
            'enrollment_documents',
            'grades',
            'assessments',
            'attendances',
            'payments',
            'expenses',
            'fee_types',
            'financial_categories',
            'communications',
            'conversations',
            'messages',
            'sms_logs',
            'sms_inbox',
            'events',
            'gate_logs',
            'staff_leave_requests',
            'material_lists',
            'users',
            'settings',
            'licenses',
        ];

        $totalUpdated = 0;

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'school_id')) {
                $affected = DB::table($table)
                    ->whereNull('school_id')
                    ->update(['school_id' => $school->id]);

                if ($affected > 0) {
                    $this->line("• Tabela <comment>{$table}</comment>: {$affected} registos associados.");
                    $totalUpdated += $affected;
                }
            }
        }

        $this->info("Backfill concluído com sucesso! Total de registos associados: {$totalUpdated}");

        return Command::SUCCESS;
    }
}
