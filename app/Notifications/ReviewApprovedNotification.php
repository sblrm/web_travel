<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Review $review
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Review Anda Disetujui! 🎉')
            ->greeting('Halo '.$notifiable->name.'!')
            ->line('Review Anda untuk destinasi **'.$this->review->destination->name.'** telah disetujui dan sekarang tampil di halaman destinasi.')
            ->line('Rating: '.str_repeat('⭐', $this->review->rating))
            ->action('Lihat Review Anda', route('destinations.show', $this->review->destination->slug))
            ->line('Terima kasih telah berbagi pengalaman Anda dengan komunitas CulturalTrip!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'review_id' => $this->review->id,
            'destination_id' => $this->review->destination_id,
            'destination_name' => $this->review->destination->name,
            'destination_slug' => $this->review->destination->slug,
            'rating' => $this->review->rating,
            'message' => 'Review Anda untuk '.$this->review->destination->name.' telah disetujui!',
        ];
    }
}
