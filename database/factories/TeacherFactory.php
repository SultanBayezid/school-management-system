<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TeacherFactory extends Factory
{
    public function definition(): array
    {
        return [
            'employee_id'   => 'EMP-'.str_pad((string) $this->faker->unique()->numberBetween(1, 99999), 4, '0', STR_PAD_LEFT),
            'first_name'    => $this->faker->firstName(),
            'last_name'     => $this->faker->lastName(),
            'gender'        => $this->faker->randomElement(['male', 'female']),
            'date_of_birth' => $this->faker->date(max: '-25 years'),
            'phone'         => '+88016'.$this->faker->numberBetween(10000000, 99999999),
            'email'         => $this->faker->unique()->safeEmail(),
            'address'       => $this->faker->address(),
            'qualification' => $this->faker->randomElement(['BEd', 'MEd', 'MSc', 'MA']),
            'join_date'     => $this->faker->date(max: 'now'),
            'is_active'     => true,
        ];
    }
}
