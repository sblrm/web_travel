<?php

namespace App\Observers;

use App\Models\Review;
use App\Notifications\ReviewApprovedNotification;

class ReviewObserver
{
    /**
     * Handle the Review "created" event.
     */
    public function created(Review $review): void
    {
        //
    }

    /**
     * Handle the Review "updated" event.
     */
    public function updated(Review $review): void
    {
        // Send notification when review is verified
        if ($review->wasChanged('is_verified') && $review->is_verified) {
            $review->user->notify(new ReviewApprovedNotification($review));
        }
    }

    /**
     * Handle the Review "deleted" event.
     */
    public function deleted(Review $review): void
    {
        //
    }

    /**
     * Handle the Review "restored" event.
     */
    public function restored(Review $review): void
    {
        //
    }

    /**
     * Handle the Review "force deleted" event.
     */
    public function forceDeleted(Review $review): void
    {
        //
    }
}
