<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassRoom;

class ClassRoomSeeder extends Seeder
{
    public function run()
    {
        $currentYear = current_school_year();

        $classes = [
            // Pré-Infantil
            ['name' => 'Pré-Infantil A', 'grade_level' => 0, 'max_students' => 20, 'classroom' => 'Sala 1'],
            ['name' => 'Pré-Infantil B', 'grade_level' => 0, 'max_students' => 20, 'classroom' => 'Sala 2'],

            // Pré-Escolar
            ['name' => 'Pré-Escolar A', 'grade_level' => 1, 'max_students' => 25, 'classroom' => 'Sala 3'],
            ['name' => 'Pré-Escolar B', 'grade_level' => 1, 'max_students' => 25, 'classroom' => 'Sala 4'],

            // 1ª Classe
            ['name' => '1ª Classe A', 'grade_level' => 2, 'max_students' => 30, 'classroom' => 'Sala 5'],
            ['name' => '1ª Classe B', 'grade_level' => 2, 'max_students' => 30, 'classroom' => 'Sala 6'],

            // 2ª Classe
            ['name' => '2ª Classe A', 'grade_level' => 3, 'max_students' => 32, 'classroom' => 'Sala 7'],
            ['name' => '2ª Classe B', 'grade_level' => 3, 'max_students' => 32, 'classroom' => 'Sala 8'],

            // 3ª Classe
            ['name' => '3ª Classe A', 'grade_level' => 4, 'max_students' => 35, 'classroom' => 'Sala 9'],
            ['name' => '3ª Classe B', 'grade_level' => 4, 'max_students' => 35, 'classroom' => 'Sala 10'],

            // 4ª Classe
            ['name' => '4ª Classe A', 'grade_level' => 5, 'max_students' => 35, 'classroom' => 'Sala 11'],
            ['name' => '4ª Classe B', 'grade_level' => 5, 'max_students' => 35, 'classroom' => 'Sala 12'],

            // 5ª Classe
            ['name' => '5ª Classe A', 'grade_level' => 6, 'max_students' => 40, 'classroom' => 'Sala 13'],
            ['name' => '5ª Classe B', 'grade_level' => 6, 'max_students' => 40, 'classroom' => 'Sala 14'],

            // 6ª Classe
            ['name' => '6ª Classe A', 'grade_level' => 7, 'max_students' => 40, 'classroom' => 'Sala 15'],
            ['name' => '6ª Classe B', 'grade_level' => 7, 'max_students' => 40, 'classroom' => 'Sala 16'],
        ];

        foreach ($classes as $class) {
            $isSecondary = $class['grade_level'] >= 8;
            ClassRoom::create([
                'name' => $class['name'],
                'grade_level' => $class['grade_level'],
                'teaching_mode' => $isSecondary ? 'subject_based' : 'single_teacher',
                'max_students' => $class['max_students'],
                'current_students' => 0,
                'classroom' => $class['classroom'],
                'school_year' => $currentYear,
                'is_active' => true,
            ]);
        }

        $this->command->info('Turmas criadas com sucesso!');
    }
}
