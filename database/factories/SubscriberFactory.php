<?php

namespace Database\Factories;

use App\Models\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscriber>
 */
class SubscriberFactory extends Factory
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
            'name' => fake()->name,
            'tier_id' => Arr::random([Subscriber::TIER_1, Subscriber::TIER_2, Subscriber::TIER_3]),
            'user_id' => 1,
            'created_at' => $date,
            'updated_at' => $date
        ];
    }
}
