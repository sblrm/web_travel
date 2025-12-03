<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Booking $booking
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Booking Berhasil - '.$this->booking->booking_code)
            ->greeting('Halo '.$this->booking->visitor_name.'!')
            ->line('Terima kasih telah melakukan booking di '.$this->booking->destination->name.'.')
            ->line('Kode Booking: **'.$this->booking->booking_code.'**')
            ->line('Tanggal Kunjungan: '.$this->booking->getFormattedVisitDateAttribute())
            ->line('Jumlah Pengunjung: '.$this->booking->quantity.' orang')
            ->line('Total Pembayaran: **'.$this->booking->getFormattedTotalAmountAttribute().'**')
            ->line('Silakan lakukan pembayaran dalam 24 jam.')
            ->action('Lihat Detail Booking', route('bookings.show', $this->booking))
            ->line('Terima kasih telah menggunakan CulturalTrip!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
            'destination_name' => $this->booking->destination->name,
            'total_amount' => $this->booking->total_amount,
            'message' => 'Booking berhasil dibuat. Silakan lakukan pembayaran dalam 24 jam.',
        ];
    }
}
