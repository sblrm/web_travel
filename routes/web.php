<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Api\ReviewController as ApiReviewController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/destinasi', [DestinationController::class, 'index'])->name('destinations.index');
Route::get('/destinasi/{destination:slug}', [DestinationController::class, 'show'])->name('destinations.show');

// API Routes for AJAX
Route::get('/api/destinasi/{destination:slug}/reviews', [ApiReviewController::class, 'index'])->name('api.reviews.index');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Review routes
    Route::post('/destinasi/{destination}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::patch('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::post('/reviews/{review}/vote', [ReviewController::class, 'vote'])->name('reviews.vote');

    // Notification routes
    Route::get('/notifications/{notification}/read', [ProfileController::class, 'markNotificationAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [ProfileController::class, 'markAllNotificationsAsRead'])->name('notifications.mark-all-read');

    // Analytics dashboard
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    // Booking routes
    Route::get('/destinasi/{destination:slug}/booking', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/destinasi/{destination:slug}/booking', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

    // Payment routes
    Route::get('/payments/{booking}', [PaymentController::class, 'show'])->name('payments.show');
    Route::post('/payments/{booking}/upload', [PaymentController::class, 'upload'])->name('payments.upload');
    Route::get('/payments/{booking}/invoice', [PaymentController::class, 'invoice'])->name('payments.invoice');
});

require __DIR__.'/auth.php';
