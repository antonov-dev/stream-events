<?php

namespace Database\Factories;

use App\Modules\Payments\Converters\CurrencyConverter;
use App\Modules\Payments\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MerchSale>
 */
class MerchSaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = fake()->dateTimeBetween('-3 months');
        $price = fake()->randomFloat(2, 5, 100);
        $currency = collect(Currency::all())->values()->random();

        /** @var CurrencyConverter $converter */
        $converter = resolve(CurrencyConverter::class);

        return [
            'name' => fake()->word,
            'amount' => fake()->randomNumber(1),
            'price' => $price,
            'price_usd' => $converter->convert($price, $currency, Currency::USD),
            'currency' => $currency,
            'user_id' => 1,
            'created_at' => $date,
            'updated_at' => $date
        ];
    }
}
