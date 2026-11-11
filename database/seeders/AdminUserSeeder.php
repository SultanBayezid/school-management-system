<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@school.test'],
            [
                'name'      => 'System Administrator',
                'password'  => 'password',
                'role'      => User::ROLE_ADMIN,
                'is_active' => true,
            ],
        );
    }
}
