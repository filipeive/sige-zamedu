<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected function addIndexIfNotExists(string $tableName, array $columns, string $indexName): void
    {
        try {
            Schema::table($tableName, function (Blueprint $table) use ($columns, $indexName) {
                $table->index($columns, $indexName);
            });
        } catch (\Throwable $e) {
            // Index already exists or duplicate key name, safely ignore
        }
    }

    protected function dropIndexIfExists(string $tableName, string $indexName): void
    {
        try {
            Schema::table($tableName, function (Blueprint $table) use ($indexName) {
                $table->dropIndex($indexName);
            });
        } catch (\Throwable $e) {
            // Index does not exist, safely ignore
        }
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Schools Territorial Indexes
        $this->addIndexIfNotExists('schools', ['province_id', 'district_id', 'zip_id'], 'idx_schools_territory_hierarchy');
        $this->addIndexIfNotExists('schools', ['province_id'], 'idx_schools_province_id');
        $this->addIndexIfNotExists('schools', ['district_id'], 'idx_schools_district_id');
        $this->addIndexIfNotExists('schools', ['zip_id'], 'idx_schools_zip_id');

        // 2. Users Tenant & Territorial Indexes
        $this->addIndexIfNotExists('users', ['school_id', 'status'], 'idx_users_school_status');
        $this->addIndexIfNotExists('users', ['province_id', 'district_id', 'zip_id'], 'idx_users_territory');

        // 3. Students Multi-Tenant Indexes
        $this->addIndexIfNotExists('students', ['school_id', 'status'], 'idx_students_school_status');
        $this->addIndexIfNotExists('students', ['school_id', 'gender'], 'idx_students_school_gender');

        // 4. Grades Performance Indexes
        $this->addIndexIfNotExists('grades', ['school_id', 'term', 'year'], 'idx_grades_school_term_year');
        $this->addIndexIfNotExists('grades', ['student_id', 'subject_id', 'class_id'], 'idx_grades_student_subject_class');

        // 5. Attendances Multi-Tenant Indexes
        $this->addIndexIfNotExists('attendances', ['school_id', 'attendance_date'], 'idx_attendances_school_date');

        // 6. Payments Multi-Tenant Indexes
        $this->addIndexIfNotExists('payments', ['school_id', 'status'], 'idx_payments_school_status');
        $this->addIndexIfNotExists('payments', ['student_id', 'payment_date'], 'idx_payments_student_date');

        // 7. Classes Multi-Tenant Indexes
        $this->addIndexIfNotExists('classes', ['school_id', 'grade_level'], 'idx_classes_school_grade_level');

        // 8. Enrollments Multi-Tenant Indexes
        $this->addIndexIfNotExists('enrollments', ['school_id', 'class_id'], 'idx_enrollments_school_class_id');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropIndexIfExists('enrollments', 'idx_enrollments_school_class_id');
        $this->dropIndexIfExists('classes', 'idx_classes_school_grade_level');
        $this->dropIndexIfExists('payments', 'idx_payments_student_date');
        $this->dropIndexIfExists('payments', 'idx_payments_school_status');
        $this->dropIndexIfExists('attendances', 'idx_attendances_school_date');
        $this->dropIndexIfExists('grades', 'idx_grades_student_subject_class');
        $this->dropIndexIfExists('grades', 'idx_grades_school_term_year');
        $this->dropIndexIfExists('students', 'idx_students_school_gender');
        $this->dropIndexIfExists('students', 'idx_students_school_status');
        $this->dropIndexIfExists('users', 'idx_users_territory');
        $this->dropIndexIfExists('users', 'idx_users_school_status');
        $this->dropIndexIfExists('schools', 'idx_schools_zip_id');
        $this->dropIndexIfExists('schools', 'idx_schools_district_id');
        $this->dropIndexIfExists('schools', 'idx_schools_province_id');
        $this->dropIndexIfExists('schools', 'idx_schools_territory_hierarchy');
    }
};
