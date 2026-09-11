<?php

namespace App\Http\Controllers;

use App\Helpers\EducationLevel;
use App\Models\School;
use App\Models\Student;
use App\Models\Grade;
use App\Models\ClassRoom;
use App\Models\Province;
use App\Models\District;
use App\Models\Zip;
use App\Services\HierarchyScopeResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class TerritorialAggregationController extends Controller
{
    protected HierarchyScopeResolver $scopeResolver;

    public function __construct(HierarchyScopeResolver $scopeResolver)
    {
        $this->scopeResolver = $scopeResolver;
    }

    /**
     * Build MINEDH Aggregated Pedagogical Map Data by Grade Levels (8ª - 12ª) and Cycles.
     */
    protected function buildPedagogicalMapData(array $schoolIds, int $year, int $term): array
    {
        // Grade levels para o Mapa Pedagógico do Ensino Secundário (ESG1 + ESG2)
        // Valores pós-normalização (grade_level direto: 7=7ªClasse, ..., 12=12ªClasse)
        $gradeLevels = [
            '7'  => '7ª Classe — ESG1 (1.º Ciclo)',
            '8'  => '8ª Classe — ESG1 (1.º Ciclo)',
            '9'  => '9ª Classe — ESG1 ⭐ Exame Nacional',
            '10' => '10ª Classe — ESG2 (2.º Ciclo) ⭐ Exame Nacional',
            '11' => '11ª Classe — ESG2 (2.º Ciclo)',
            '12' => '12ª Classe — ESG2 ⭐ Exame Nacional',
        ];

        $passingThreshold = (float) setting('passing_grade', 9.5);

        $mapData = [];

        // Totals accumulators
        $totals = [
            'entries_hm' => 0, 'entries_m' => 0,
            'dropouts_hm' => 0, 'dropouts_m' => 0,
            'transfers_hm' => 0, 'transfers_m' => 0,
            'evaluated_hm' => 0, 'evaluated_m' => 0,
            'passed_hm' => 0, 'passed_m' => 0,
        ];

        // 1º Ciclo accumulators (8ª, 9ª, 10ª)
        $ciclo1 = [
            'entries_hm' => 0, 'entries_m' => 0,
            'dropouts_hm' => 0, 'dropouts_m' => 0,
            'transfers_hm' => 0, 'transfers_m' => 0,
            'evaluated_hm' => 0, 'evaluated_m' => 0,
            'passed_hm' => 0, 'passed_m' => 0,
        ];

        // 2º Ciclo accumulators (11ª, 12ª)
        $ciclo2 = [
            'entries_hm' => 0, 'entries_m' => 0,
            'dropouts_hm' => 0, 'dropouts_m' => 0,
            'transfers_hm' => 0, 'transfers_m' => 0,
            'evaluated_hm' => 0, 'evaluated_m' => 0,
            'passed_hm' => 0, 'passed_m' => 0,
        ];

        foreach ($gradeLevels as $level => $label) {
            // Find students in these schools enrolled in this grade level
            $classIds = ClassRoom::withoutGlobalScopes()
                ->whereIn('school_id', $schoolIds)
                ->where('grade_level', $level)
                ->pluck('id')
                ->toArray();

            $students = Student::withoutGlobalScopes()
                ->whereIn('school_id', $schoolIds)
                ->whereHas('enrollments', function ($q) use ($classIds) {
                    $q->whereIn('class_id', $classIds);
                })->get();

            if ($students->isEmpty() && \Illuminate\Support\Facades\Schema::hasColumn('students', 'class_id')) {
                // Fallback to direct class matching if enrollment record is absent and column exists
                $students = Student::withoutGlobalScopes()
                    ->whereIn('school_id', $schoolIds)
                    ->whereIn('class_id', $classIds)
                    ->get();
            }

            $entriesHM = $students->count();
            $entriesM = $students->where('gender', 'F')->count();

            $dropoutsHM = $students->where('status', 'dropped')->count();
            $dropoutsM = $students->where('status', 'dropped')->where('gender', 'F')->count();

            $transfersHM = $students->where('status', 'transferred')->count();
            $transfersM = $students->where('status', 'transferred')->where('gender', 'F')->count();

            // Grades for these students
            $grades = Grade::withoutGlobalScopes()
                ->join('students', 'grades.student_id', '=', 'students.id')
                ->whereIn('grades.school_id', $schoolIds)
                ->whereIn('grades.class_id', $classIds)
                ->where('grades.term', $term)
                ->where('grades.year', $year)
                ->whereNotNull('grades.grade')
                ->select('grades.student_id', 'grades.grade', 'students.gender')
                ->get();

            // Average grade per student to calculate overall pass/fail per student in this term
            $studentAverages = $grades->groupBy('student_id')->map(function ($studentGrades) {
                return [
                    'avg' => $studentGrades->avg('grade'),
                    'gender' => $studentGrades->first()->gender ?? 'M',
                ];
            });

            $evalHM = $studentAverages->count();
            $evalM = $studentAverages->where('gender', 'F')->count();

            $passedHM = $studentAverages->where('avg', '>=', $passingThreshold)->count();
            $passedM = $studentAverages->where('gender', 'F')->where('avg', '>=', $passingThreshold)->count();

            $approvalRateHM = $evalHM > 0 ? round(($passedHM / $evalHM) * 100, 1) : 0.0;
            $approvalRateM = $evalM > 0 ? round(($passedM / $evalM) * 100, 1) : 0.0;
            $dropoutRateHM = $entriesHM > 0 ? round(($dropoutsHM / $entriesHM) * 100, 1) : 0.0;

            $row = [
                'level' => $level,
                'label' => $label,
                'entries_hm' => $entriesHM, 'entries_m' => $entriesM,
                'dropouts_hm' => $dropoutsHM, 'dropouts_m' => $dropoutsM,
                'transfers_hm' => $transfersHM, 'transfers_m' => $transfersM,
                'evaluated_hm' => $evalHM, 'evaluated_m' => $evalM,
                'passed_hm' => $passedHM, 'passed_m' => $passedM,
                'approval_rate_hm' => $approvalRateHM, 'approval_rate_m' => $approvalRateM,
                'dropout_rate_hm' => $dropoutRateHM,
            ];

            $mapData[$level] = $row;

            // Accumulate
            $totals['entries_hm'] += $entriesHM; $totals['entries_m'] += $entriesM;
            $totals['dropouts_hm'] += $dropoutsHM; $totals['dropouts_m'] += $dropoutsM;
            $totals['transfers_hm'] += $transfersHM; $totals['transfers_m'] += $transfersM;
            $totals['evaluated_hm'] += $evalHM; $totals['evaluated_m'] += $evalM;
            $totals['passed_hm'] += $passedHM; $totals['passed_m'] += $passedM;

            if (in_array((string)$level, ['8', '9', '10'])) {
                $ciclo1['entries_hm'] += $entriesHM; $ciclo1['entries_m'] += $entriesM;
                $ciclo1['dropouts_hm'] += $dropoutsHM; $ciclo1['dropouts_m'] += $dropoutsM;
                $ciclo1['transfers_hm'] += $transfersHM; $ciclo1['transfers_m'] += $transfersM;
                $ciclo1['evaluated_hm'] += $evalHM; $ciclo1['evaluated_m'] += $evalM;
                $ciclo1['passed_hm'] += $passedHM; $ciclo1['passed_m'] += $passedM;
            } else {
                $ciclo2['entries_hm'] += $entriesHM; $ciclo2['entries_m'] += $entriesM;
                $ciclo2['dropouts_hm'] += $dropoutsHM; $ciclo2['dropouts_m'] += $dropoutsM;
                $ciclo2['transfers_hm'] += $transfersHM; $ciclo2['transfers_m'] += $transfersM;
                $ciclo2['evaluated_hm'] += $evalHM; $ciclo2['evaluated_m'] += $evalM;
                $ciclo2['passed_hm'] += $passedHM; $ciclo2['passed_m'] += $passedM;
            }
        }

        // Compute cycle rates
        $ciclo1['approval_rate_hm'] = $ciclo1['evaluated_hm'] > 0 ? round(($ciclo1['passed_hm'] / $ciclo1['evaluated_hm']) * 100, 1) : 0.0;
        $ciclo1['approval_rate_m'] = $ciclo1['evaluated_m'] > 0 ? round(($ciclo1['passed_m'] / $ciclo1['evaluated_m']) * 100, 1) : 0.0;
        $ciclo1['dropout_rate_hm'] = $ciclo1['entries_hm'] > 0 ? round(($ciclo1['dropouts_hm'] / $ciclo1['entries_hm']) * 100, 1) : 0.0;

        $ciclo2['approval_rate_hm'] = $ciclo2['evaluated_hm'] > 0 ? round(($ciclo2['passed_hm'] / $ciclo2['evaluated_hm']) * 100, 1) : 0.0;
        $ciclo2['approval_rate_m'] = $ciclo2['evaluated_m'] > 0 ? round(($ciclo2['passed_m'] / $ciclo2['evaluated_m']) * 100, 1) : 0.0;
        $ciclo2['dropout_rate_hm'] = $ciclo2['entries_hm'] > 0 ? round(($ciclo2['dropouts_hm'] / $ciclo2['entries_hm']) * 100, 1) : 0.0;

        $totals['approval_rate_hm'] = $totals['evaluated_hm'] > 0 ? round(($totals['passed_hm'] / $totals['evaluated_hm']) * 100, 1) : 0.0;
        $totals['approval_rate_m'] = $totals['evaluated_m'] > 0 ? round(($totals['passed_m'] / $totals['evaluated_m']) * 100, 1) : 0.0;
        $totals['dropout_rate_hm'] = $totals['entries_hm'] > 0 ? round(($totals['dropouts_hm'] / $totals['entries_hm']) * 100, 1) : 0.0;

        return [
            'by_level' => $mapData,
            'ciclo1' => $ciclo1,
            'ciclo2' => $ciclo2,
            'totals' => $totals,
        ];
    }

    /**
     * Display Territorial MINEDH Pedagogical Map
     */
    public function pedagogicalMap(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('view_territorial_dashboard') && 
            !$user->hasAnyRole(['super_admin', 'province_admin', 'province_inspector', 'district_admin', 'district_inspector', 'zip_coordinator', 'zip_admin'])) {
            abort(403, 'Acesso não autorizado ao Mapa de Aproveitamento Pedagógico Território MINEDH.');
        }

        $year = (int) $request->input('year', current_school_year());
        $term = (int) $request->input('term', 1);

        $allowedSchoolIds = $this->scopeResolver->resolveVisibleSchoolIds($user);

        $selectedProvinceId = $request->input('province_id');
        $selectedDistrictId = $request->input('district_id');
        $selectedZipId = $request->input('zip_id');

        $schoolsQuery = School::query();
        if (!is_null($allowedSchoolIds)) {
            $schoolsQuery->whereIn('id', $allowedSchoolIds);
        }

        if ($selectedProvinceId) $schoolsQuery->where('province_id', $selectedProvinceId);
        if ($selectedDistrictId) $schoolsQuery->where('district_id', $selectedDistrictId);
        if ($selectedZipId) $schoolsQuery->where('zip_id', $selectedZipId);

        $targetSchoolIds = $schoolsQuery->pluck('id')->toArray();

        $mapData = $this->buildPedagogicalMapData($targetSchoolIds, $year, $term);

        $provinces = Province::where('is_active', true)->get();
        $districts = District::where('is_active', true)->get();
        $zips = Zip::where('is_active', true)->get();

        return view('territorial.map', compact(
            'mapData',
            'year',
            'term',
            'provinces',
            'districts',
            'zips',
            'selectedProvinceId',
            'selectedDistrictId',
            'selectedZipId'
        ));
    }

    /**
     * Export MINEDH Pedagogical Map to PDF
     */
    public function exportPdf(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('view_territorial_dashboard') && 
            !$user->hasAnyRole(['super_admin', 'province_admin', 'province_inspector', 'district_admin', 'district_inspector', 'zip_coordinator', 'zip_admin'])) {
            abort(403, 'Acesso não autorizado para exportar o Mapa de Aproveitamento MINEDH.');
        }

        $year = (int) $request->input('year', current_school_year());
        $term = (int) $request->input('term', 1);

        $allowedSchoolIds = $this->scopeResolver->resolveVisibleSchoolIds($user);

        $selectedProvinceId = $request->input('province_id');
        $selectedDistrictId = $request->input('district_id');
        $selectedZipId = $request->input('zip_id');

        $schoolsQuery = School::query();
        if (!is_null($allowedSchoolIds)) {
            $schoolsQuery->whereIn('id', $allowedSchoolIds);
        }

        if ($selectedProvinceId) $schoolsQuery->where('province_id', $selectedProvinceId);
        if ($selectedDistrictId) $schoolsQuery->where('district_id', $selectedDistrictId);
        if ($selectedZipId) $schoolsQuery->where('zip_id', $selectedZipId);

        $targetSchoolIds = $schoolsQuery->pluck('id')->toArray();

        $mapData = $this->buildPedagogicalMapData($targetSchoolIds, $year, $term);

        $pdf = Pdf::loadView('territorial.pdf_map', compact('mapData', 'year', 'term'))
            ->setPaper('a4', 'landscape');

        return $pdf->download("MINEDH_Mapa_Aproveitamento_Pedagogico_{$term}Trim_{$year}.pdf");
    }

    /**
     * Export MINEDH Pedagogical Map to CSV
     */
    public function exportCsv(Request $request)
    {
        $user = Auth::user();

        if (!$user->can('view_territorial_dashboard') && 
            !$user->hasAnyRole(['super_admin', 'province_admin', 'province_inspector', 'district_admin', 'district_inspector', 'zip_coordinator', 'zip_admin'])) {
            abort(403, 'Acesso não autorizado.');
        }

        $year = (int) $request->input('year', current_school_year());
        $term = (int) $request->input('term', 1);

        $allowedSchoolIds = $this->scopeResolver->resolveVisibleSchoolIds($user);
        $targetSchoolIds = is_null($allowedSchoolIds) ? School::pluck('id')->toArray() : $allowedSchoolIds;

        $mapData = $this->buildPedagogicalMapData($targetSchoolIds, $year, $term);

        $filename = "MAPA_APROVEITAMENTO_MINEDH_{$term}TRIM_{$year}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($mapData) {
            $file = fopen('php://output', 'w');
            // BOM for UTF-8 Excel support
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['REPÚBLICA DE MOÇAMBIQUE - MINEDH']);
            fputcsv($file, ['MAPA DE APROVEITAMENTO PEDAGÓGICO DO ENSINO SECUNDÁRIO']);
            fputcsv($file, []);
            fputcsv($file, ['Classe', 'Entradas HM', 'Entradas M', 'Desistentes HM', 'Desistentes M', 'Avaliados HM', 'Avaliados M', 'Aprovados HM', 'Aprovados M', '% Aproveitamento HM', '% Aproveitamento M']);

            foreach ($mapData['by_level'] as $row) {
                fputcsv($file, [
                    $row['label'],
                    $row['entries_hm'], $row['entries_m'],
                    $row['dropouts_hm'], $row['dropouts_m'],
                    $row['evaluated_hm'], $row['evaluated_m'],
                    $row['passed_hm'], $row['passed_m'],
                    $row['approval_rate_hm'] . '%',
                    $row['approval_rate_m'] . '%',
                ]);
            }

            // Totals
            fputcsv($file, []);
            fputcsv($file, [
                'TOTAL GERAL',
                $mapData['totals']['entries_hm'], $mapData['totals']['entries_m'],
                $mapData['totals']['dropouts_hm'], $mapData['totals']['dropouts_m'],
                $mapData['totals']['evaluated_hm'], $mapData['totals']['evaluated_m'],
                $mapData['totals']['passed_hm'], $mapData['totals']['passed_m'],
                $mapData['totals']['approval_rate_hm'] . '%',
                $mapData['totals']['approval_rate_m'] . '%',
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
