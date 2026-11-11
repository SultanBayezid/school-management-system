<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;

class PeopleSeeder extends Seeder
{
    public function run(): void
    {
        $year = \App\Models\AcademicYear::current();

        // ---- Teachers ------------------------------------------------------
        $teacherSeed = [
            'Abdul Karim', 'Nusrat Jahan', 'Rafiq Ahmed', 'Salma Khatun',
            'Jahangir Alam', 'Farhana Yasmin', 'Shahidul Islam', 'Ruma Akter',
        ];

        foreach ($teacherSeed as $index => $name) {
            [$first, $last] = explode(' ', $name, 2);

            $teacher = Teacher::firstOrCreate(
                ['employee_id' => sprintf('EMP-%03d', $index + 1)],
                [
                    'first_name'   => $first,
                    'last_name'    => $last,
                    'gender'       => $index % 2 === 0 ? 'male' : 'female',
                    'phone'        => '+8801711'.str_pad((string) (1000000 + $index * 1111), 7, '0'),
                    'email'        => strtolower($first.'.'.$last).'@greenfield.edu.bd',
                    'qualification' => collect(['BEd', 'MEd', 'MSc', 'MA'])->random(),
                    'join_date'    => now()->subYears(rand(1, 12))->toDateString(),
                ],
            );

            if ($index < 3) {
                // A few teachers get login accounts.
                User::firstOrCreate(
                    ['email' => strtolower($first.'.'.$last).'@school.test'],
                    [
                        'name'     => $name,
                        'password' => 'password',
                        'role'     => User::ROLE_TEACHER,
                        'is_active' => true,
                    ],
                )->teacher()->save($teacher);
            }
        }

        $teachers = Teacher::all();

        // Assign class teachers + subject teachers.
        SchoolClass::orderBy('level')->get()->each(function ($class, $i) use ($teachers) {
            $class->sections->each(function ($section, $j) use ($teachers, $i) {
                $section->update(['teacher_id' => $teachers[($i + $j) % $teachers->count()]->id]);
            });

            $class->classSubjects->each(function ($cs, $k) use ($teachers) {
                $cs->update(['teacher_id' => $teachers[$k % $teachers->count()]->id]);
            });
        });

        // ---- Students ------------------------------------------------------
        $firstNames = [
            'Tanvir' => 'male', 'Sumaiya' => 'female', 'Rakib' => 'male', 'Mim' => 'female',
            'Arif' => 'male', 'Nadia' => 'female', 'Shakib' => 'male', 'Tasnim' => 'female',
            'Imran' => 'male', 'Priya' => 'female', 'Naeem' => 'male', 'Rituparna' => 'female',
            'Fahim' => 'male', 'Mousumi' => 'female', 'Rifat' => 'male', 'Sharmin' => 'female',
        ];
        $lastNames = ['Hossain', 'Islam', 'Akter', 'Rahman', 'Chowdhury', 'Karim', 'Sultana', 'Mia'];

        $classes = SchoolClass::with('sections')->orderBy('level')->get();
        $admissionCounter = 1;

        foreach ($classes as $classIndex => $class) {
            foreach ($class->sections as $sectionIndex => $section) {
                for ($i = 1; $i <= 12; $i++) {
                    $first = array_rand($firstNames);
                    $gender = $firstNames[$first];
                    $last = $lastNames[array_rand($lastNames)];

                    $admissionNo = sprintf('ADM-%s-%04d', $year->name, $admissionCounter++);

                    $student = Student::firstOrCreate(
                        ['admission_no' => $admissionNo],
                        [
                            'first_name'       => $first,
                            'last_name'        => $last,
                            'gender'           => $gender,
                            'date_of_birth'    => now()->subYears(6 + $classIndex)->subDays(rand(0, 360))->toDateString(),
                            'address'          => rand(1, 50).' Green Road, Dhaka',
                            'blood_group'      => collect(['A+', 'B+', 'O+', 'AB+'])->random(),
                            'guardian_name'    => $lastNames[array_rand($lastNames)].' '.$last,
                            'guardian_phone'   => '+8801811'.str_pad((string) rand(1000000, 9999999), 7, '0'),
                            'guardian_relation' => 'Father',
                            'admission_date'   => now()->subMonths(rand(1, 36))->toDateString(),
                            'is_active'        => true,
                        ],
                    );

                    Enrollment::firstOrCreate(
                        ['student_id' => $student->id, 'academic_year_id' => $year->id],
                        [
                            'school_class_id' => $class->id,
                            'section_id'      => $section->id,
                            'roll_no'         => $i,
                            'status'          => 'active',
                        ],
                    );
                }
            }
        }

        // Two student logins + one parent login (linked to first two students).
        $firstTwo = Student::orderBy('admission_no')->take(2)->get();

        if ($firstTwo->count() >= 2) {
            $studentUser = User::firstOrCreate(
                ['email' => 'student@school.test'],
                ['name' => $firstTwo[0]->full_name(), 'password' => 'password', 'role' => User::ROLE_STUDENT, 'is_active' => true],
            );
            $firstTwo[0]->update(['user_id' => $studentUser->id]);

            $parentUser = User::firstOrCreate(
                ['email' => 'parent@school.test'],
                ['name' => ($firstTwo[1]->guardian_name ?? 'Guardian'), 'password' => 'password', 'role' => User::ROLE_PARENT, 'is_active' => true],
            );
            $firstTwo[1]->update(['parent_user_id' => $parentUser->id]);
        }
    }
}
