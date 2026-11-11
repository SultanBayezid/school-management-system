<?php

namespace Database\Seeders;

use App\Models\SchoolSetting;
use Illuminate\Database\Seeder;

class SchoolSettingSeeder extends Seeder
{
    public function run(): void
    {
        SchoolSetting::firstOrCreate(
            ['id' => 1],
            [
                'school_name'    => 'Greenfield International School',
                'school_address' => '12 College Road, Dhaka 1205, Bangladesh',
                'phone'          => '+880 2 5566 7788',
                'email'          => 'office@greenfield.edu.bd',
                'website'        => 'https://greenfield.edu.bd',
                'principal_name' => 'Dr. Ayesha Rahman',
                'established_year' => 1998,
            ],
        );
    }
}
