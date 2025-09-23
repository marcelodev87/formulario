<?php

namespace App\Providers;

use App\Models\Institution;
use App\Models\Invite;
use App\Models\Member;
use App\Policies\InstitutionPolicy;
use App\Policies\MemberPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Institution::class, InstitutionPolicy::class);
        Gate::policy(Member::class, MemberPolicy::class);

        RateLimiter::for('magic-link', function ($request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        Route::bind('invite', function (string $value) {
            return Invite::where('key', $value)->firstOrFail();
        });

        Validator::extend('cpf', function ($attribute, $value) {
            $numbers = preg_replace('/\D/', '', (string) $value);

            if (strlen($numbers) !== 11 || preg_match('/^(\d)\1{10}$/', $numbers)) {
                return false;
            }

            for ($t = 9; $t < 11; $t++) {
                $sum = 0;

                for ($i = 0; $i < $t; $i++) {
                    $sum += (int) $numbers[$i] * (($t + 1) - $i);
                }

                $digit = ((10 * $sum) % 11) % 10;

                if ((int) $numbers[$t] !== $digit) {
                    return false;
                }
            }

            return true;
        }, 'O campo :attribute deve ser um CPF válido.');

        Validator::extend('phone_br', function ($attribute, $value) {
            return (bool) preg_match('/^\(\d{2}\) \d{4,5}\-\d{4}$/', (string) $value);
        }, 'O campo :attribute deve estar no formato (00) 0000-00000.');

        Validator::extend('cep', function ($attribute, $value) {
            return (bool) preg_match('/^\d{5}\-\d{3}$/', (string) $value);
        }, 'O campo :attribute deve estar no formato 00000-000.');
    }
}
