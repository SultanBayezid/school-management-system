<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ClassSubject;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class AcademicStructureSeeder extends Seeder
{
    public function run(): void
    {
        // ---- Academic year -------------------------------------------------
        $year = AcademicYear::firstOrCreate(
            ['name' => now()->format('Y')],
            [
                'start_date' => now()->startOfYear()->toDateString(),
                'end_date'   => now()->endOfYear()->toDateString(),
                'is_current' => true,
            ],
        );

        // ---- Subjects ------------------------------------------------------
        $subjects = collect([
            ['name' => 'Bangla',             'code' => 'BAN101', 'type' => 'core'],
            ['name' => 'English',            'code' => 'ENG101', 'type' => 'core'],
            ['name' => 'Mathematics',        'code' => 'MTH101', 'type' => 'core'],
            ['name' => 'Science',            'code' => 'SCI101', 'type' => 'core'],
            ['name' => 'Bangladesh & Global Studies', 'code' => 'BGS101', 'type' => 'core'],
            ['name' => 'Religion & Moral Education',  'code' => 'RME101', 'type' => 'core'],
            ['name' => 'ICT',                'code' => 'ICT101', 'type' => 'elective'],
            ['name' => 'Physical Education', 'code' => 'PED101', 'type' => 'elective'],
        ])->mapWithKeys(function ($data) {
            $subject = Subject::firstOrCreate(['code' => $data['code']], $data);

            return [$data['code'] => $subject];
        });

        // ---- Classes & sections -------------------------------------------
        $levels = [
            1 => 'Class One', 2 => 'Class Two', 3 => 'Class Three',
            4 => 'Class Four', 5 => 'Class Five',
        ];

        foreach ($levels as $level => $name) {
            $class = SchoolClass::firstOrCreate(
                ['name' => $name],
                ['level' => $level, 'description' => $name.' of primary school.'],
            );

            foreach (['A', 'B'] as $sectionName) {
                Section::firstOrCreate(
                    ['school_class_id' => $class->id, 'name' => 'Section '.$sectionName],
                    ['capacity' => 40],
                );
            }

            // Assign core subjects to every class.
            foreach ($subjects->where('type', 'core') as $subject) {
                ClassSubject::firstOrCreate([
                    'school_class_id' => $class->id,
                    'subject_id'      => $subject->id,
                ]);
            }
        }
    }
}
