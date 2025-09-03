<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Events\MergeSessionCartIntoUser;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    protected $listen = [
        Login::class => [
            MergeSessionCartIntoUser::class,
        ],
    ];
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        Inertia::share([
            'cart' => function () {
                if (Auth::check()) {
                    // For logged in users, take from DB column (json) or relation
                    $cart = Auth::user()->cart ?? ['count' => 0];
                    return ['count' => $cart['count'] ?? 0];
                }

                // For guests, fallback to session
                $cart = session('cart', ['count' => 0]);
                return ['count' => $cart['count'] ?? 0];
            },
        ]);
    }
}
