<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Phone>
 */
class PhoneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $phone = substr(str_replace('+','', $this->faker->e164phoneNumber()), 1);

        return [
            'phone' => $phone,
            'created_at' => now(),
        ];
    }
}
