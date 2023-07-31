<?php

namespace Database\Factories;

use App\Helpers\Payments\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Donation>
 */
class DonationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = fake()->dateTimeBetween('-3 months');

        return [
            'amount' => fake()->randomFloat(2, 5, 100),
            'message' => fake()->text(150),
            'currency' => collect(Currency::all())->values()->random(),
            'user_id' => 1,
            'created_at' => $date,
            'updated_at' => $date
        ];
    }
}
