<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FinancialCategory;

class FinancialCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Salários', 'type' => 'expense', 'color' => '#ef4444'],
            ['name' => 'Água e Luz', 'type' => 'expense', 'color' => '#f97316'],
            ['name' => 'Internet / Telefone', 'type' => 'expense', 'color' => '#f59e0b'],
            ['name' => 'Material Escolar', 'type' => 'expense', 'color' => '#84cc16'],
            ['name' => 'Manutenção', 'type' => 'expense', 'color' => '#10b981'],
            ['name' => 'Uniformes', 'type' => 'expense', 'color' => '#06b6d4'],
            ['name' => 'Aluguel', 'type' => 'expense', 'color' => '#3b82f6'],
            ['name' => 'Outras Despesas', 'type' => 'expense', 'color' => '#6366f1'],
            ['name' => 'Matrículas', 'type' => 'income', 'color' => '#8b5cf6'],
            ['name' => 'Mensalidades', 'type' => 'income', 'color' => '#d946ef'],
            ['name' => 'Outras Receitas', 'type' => 'income', 'color' => '#f43f5e'],
        ];

        foreach (\App\Models\School::pluck('id') as $schoolId) {
            foreach ($categories as $category) {
                FinancialCategory::withoutGlobalScopes()->firstOrCreate($category + ['school_id' => $schoolId]);
            }
        }
    }
}
