<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Review;
use App\Observers\BookingObserver;
use App\Observers\PaymentObserver;
use App\Observers\ReviewObserver;
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
        Review::observe(ReviewObserver::class);
        Booking::observe(BookingObserver::class);
        Payment::observe(PaymentObserver::class);
    }
}
