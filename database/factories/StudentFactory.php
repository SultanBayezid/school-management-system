<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'admission_no'      => 'ADM-'.now()->format('Y').'-'.str_pad((string) $this->faker->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'first_name'        => $this->faker->firstName(),
            'last_name'         => $this->faker->lastName(),
            'gender'            => $this->faker->randomElement(['male', 'female']),
            'date_of_birth'     => $this->faker->date(max: '-6 years'),
            'phone'             => '+88017'.$this->faker->numberBetween(10000000, 99999999),
            'address'           => $this->faker->address(),
            'blood_group'       => $this->faker->randomElement(['A+', 'B+', 'O+', 'AB+']),
            'guardian_name'     => $this->faker->name(),
            'guardian_phone'    => '+88018'.$this->faker->numberBetween(10000000, 99999999),
            'guardian_relation' => 'Father',
            'admission_date'    => $this->faker->date(max: 'now'),
            'is_active'         => true,
        ];
    }
}
