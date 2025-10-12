<?php

namespace App\Providers;

use Illuminate\Contracts\Validation\Factory;
use Illuminate\Support\ServiceProvider;

class ValidatorsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(Factory $validator): void
    {
        $validator->extend('isbn', function ($attribute, $value, $parameters, $validator) {
            $value = str_replace(['-', ' '], '', $value);

            // ISBN-10
            if (preg_match('/^\d{9}[\dX]$/', $value)) {
                $sum = 0;
                for ($i = 0; $i < 9; $i++) {
                    $sum += ($i + 1) * $value[$i];
                }
                $check = $value[9] === 'X' ? 10 : (int) $value[9];

                return ($sum + 10 * $check) % 11 === 0;
            }

            // ISBN-13
            if (preg_match('/^\d{13}$/', $value)) {
                $sum = 0;
                for ($i = 0; $i < 12; $i++) {
                    $sum += (int) $value[$i] * ($i % 2 === 0 ? 1 : 3);
                }
                $check = (10 - ($sum % 10)) % 10;

                return $check === (int) $value[12];
            }

            return false;
        }, 'The :attribute must be a valid ISBN-10 or ISBN-13 number.');
    }
}
