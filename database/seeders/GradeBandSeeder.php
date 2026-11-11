<?php

namespace Database\Seeders;

use App\Models\GradeBand;
use Illuminate\Database\Seeder;

class GradeBandSeeder extends Seeder
{
    /**
     * Standard Bangladeshi grading scale (GPA system).
     */
    public function run(): void
    {
        $bands = [
            ['grade' => 'A+', 'min_percent' => 80,   'max_percent' => 100,  'grade_point' => 5.00],
            ['grade' => 'A',  'min_percent' => 70,   'max_percent' => 79.99, 'grade_point' => 4.00],
            ['grade' => 'A-', 'min_percent' => 60,   'max_percent' => 69.99, 'grade_point' => 3.50],
            ['grade' => 'B',  'min_percent' => 50,   'max_percent' => 59.99, 'grade_point' => 3.00],
            ['grade' => 'C',  'min_percent' => 40,   'max_percent' => 49.99, 'grade_point' => 2.00],
            ['grade' => 'D',  'min_percent' => 33,   'max_percent' => 39.99, 'grade_point' => 1.00],
            ['grade' => 'F',  'min_percent' => 0,    'max_percent' => 32.99, 'grade_point' => 0.00],
        ];

        foreach ($bands as $band) {
            GradeBand::updateOrCreate(['grade' => $band['grade']], $band);
        }
    }
}
