<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Production-safe: with DEMO_SEED=false (default .env.example) only the
     * essential setup data is seeded (admin user, settings, grade bands).
     * Set DEMO_SEED=true to additionally populate a realistic demo school.
     */
    public function run(): void
    {
        $this->call([
            SchoolSettingSeeder::class,
            GradeBandSeeder::class,
            AdminUserSeeder::class,
        ]);

        if (config('services.demo_seed') || env('DEMO_SEED', false)) {
            $this->call([
                AcademicStructureSeeder::class,
                PeopleSeeder::class,
                OperationsSeeder::class,
            ]);
        }
    }
}
