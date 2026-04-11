<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Number;
use App\Models\Reservation;
use App\Observers\ReservationObserver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use DateTimeInterface;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

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
        // Register observers
        Reservation::observe(ReservationObserver::class);

        // Register policy for Spatie Activity model so Shield can control access
        Gate::policy(\Spatie\Activitylog\Models\Activity::class, \App\Policies\ActivityPolicy::class);

        Number::macro('rupiah', function ($value) {
            return 'Rp. ' . number_format($value, 0, ',', '.');
        });

        Event::listen(function (Login $event) {
            $user = $event->user;
            if ($user) {
                $user->update([
                    'is_online' => true,
                    'last_login_at' => now(),
                ]);
            }
        });

        Event::listen(function (Logout $event) {
            $user = $event->user;
            if ($user) {
                $user->update([
                    'is_online' => false,
                ]);
            }
        });
    }
}
