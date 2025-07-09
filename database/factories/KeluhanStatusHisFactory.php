<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;


class KeluhanStatusHisFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'keluhan_id' => KeluhanPelanggan::factory(),
            'status_keluhan' => fake()->randomElement(['0', '1' ,'2']),
            'updated_at' => fake()->dateTimeBetween('-1 year', 'now')
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
