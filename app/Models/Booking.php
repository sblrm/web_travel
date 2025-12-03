<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'user_id',
        'destination_id',
        'visit_date',
        'quantity',
        'unit_price',
        'total_amount',
        'visitor_name',
        'visitor_email',
        'visitor_phone',
        'notes',
        'status',
        'expires_at',
        'confirmed_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'visit_date' => 'date',
            'quantity' => 'integer',
            'unit_price' => 'integer',
            'total_amount' => 'integer',
            'expires_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Booking $booking) {
            if (empty($booking->booking_code)) {
                $booking->booking_code = self::generateBookingCode();
            }
            if (empty($booking->expires_at)) {
                $booking->expires_at = now()->addHours(24);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public static function generateBookingCode(): string
    {
        $date = now()->format('Ymd');
        $random = strtoupper(Str::random(4));

        return "BK-{$date}-{$random}";
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAwaitingPayment($query)
    {
        return $query->where('status', 'awaiting_payment');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['cancelled', 'expired', 'completed']);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast() && $this->status === 'awaiting_payment';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isAwaitingPayment(): bool
    {
        return $this->status === 'awaiting_payment';
    }

    public function isPaid(): bool
    {
        return in_array($this->status, ['paid', 'verified', 'completed']);
    }

    public function isVerified(): bool
    {
        return in_array($this->status, ['verified', 'completed']);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function canCancel(): bool
    {
        return in_array($this->status, ['pending', 'awaiting_payment']);
    }

    public function canUploadPayment(): bool
    {
        return $this->status === 'awaiting_payment' && ! $this->isExpired();
    }

    public function markAsAwaitingPayment(): void
    {
        $this->update(['status' => 'awaiting_payment']);
    }

    public function markAsPaid(): void
    {
        $this->update([
            'status' => 'paid',
            'confirmed_at' => now(),
        ]);
    }

    public function markAsVerified(): void
    {
        $this->update(['status' => 'verified']);
    }

    public function markAsCompleted(): void
    {
        $this->update(['status' => 'completed']);
    }

    public function markAsCancelled(?string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);
    }

    public function markAsExpired(): void
    {
        $this->update(['status' => 'expired']);
    }

    public function getFormattedTotalAmountAttribute(): string
    {
        return 'Rp '.number_format($this->total_amount, 0, ',', '.');
    }

    public function getFormattedUnitPriceAttribute(): string
    {
        return 'Rp '.number_format($this->unit_price, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Konfirmasi',
            'awaiting_payment' => 'Menunggu Pembayaran',
            'paid' => 'Sudah Dibayar',
            'verified' => 'Terverifikasi',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            'expired' => 'Kadaluarsa',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'awaiting_payment' => 'info',
            'paid' => 'success',
            'verified' => 'success',
            'completed' => 'primary',
            'cancelled' => 'danger',
            'expired' => 'secondary',
            default => 'secondary',
        };
    }
}
