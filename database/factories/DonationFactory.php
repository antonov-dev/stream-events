<?php

namespace Database\Factories;

use App\Modules\Payments\Converters\CurrencyConverter;
use App\Modules\Payments\Currency;
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
        $amount = fake()->randomFloat(2, 5, 100);
        $currency = collect(Currency::all())->values()->random();

        /** @var CurrencyConverter $converter */
        $converter = resolve(CurrencyConverter::class);

        return [
            'amount' => $amount,
            'amount_usd' => $converter->convert($amount, $currency, Currency::USD),
            'message' => fake()->text(150),
            'currency' => $currency,
            'user_id' => 1,
            'created_at' => $date,
            'updated_at' => $date
        ];
    }
}
