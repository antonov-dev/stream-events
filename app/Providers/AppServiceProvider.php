<?php

namespace App\Providers;

use App\Modules\Payments\Converters\CurrencyConverter;
use App\Modules\Payments\Converters\SimpleCurrencyConverter;
use App\Modules\Payments\Currency;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->bind(CurrencyConverter::class, function () {
            return new SimpleCurrencyConverter([
                Currency::EUR => [
                    Currency::USD => 1.1
                ],
                Currency::CAD => [
                    Currency::USD => 0.75
                ],
                Currency::USD => [
                    Currency::USD => 1
                ]
            ]);
        });
    }
}
