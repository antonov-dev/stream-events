<?php

namespace App\Helpers\Payments;

use Illuminate\Support\Facades\Cache;
use ReflectionClass;

class Currency
{
    const USD = 'USD';
    const CAD = 'CAD';
    const EUR = 'EUR';

    public static function all()
    {
        return Cache::remember('se:cur', 5 * 60, function () {
            return (new ReflectionClass(__CLASS__))->getConstants();
        });
    }
}
