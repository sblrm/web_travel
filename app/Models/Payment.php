<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'payment_method_id',
        'payment_code',
        'amount',
        'account_holder_name',
        'transfer_from',
        'proof_image',
        'notes',
        'status',
        'paid_at',
        'verified_at',
        'verified_by',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'paid_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Payment $payment) {
            if (empty($payment->payment_code)) {
                $payment->payment_code = self::generatePaymentCode();
            }
        });
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public static function generatePaymentCode(): string
    {
        $date = now()->format('Ymd');
        $random = strtoupper(Str::random(4));

        return "PAY-{$date}-{$random}";
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeUploaded($query)
    {
        return $query->where('status', 'uploaded');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isUploaded(): bool
    {
        return $this->status === 'uploaded';
    }

    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function canUploadProof(): bool
    {
        return in_array($this->status, ['pending', 'rejected']);
    }

    public function canVerify(): bool
    {
        return $this->status === 'uploaded';
    }

    public function markAsUploaded(): void
    {
        $this->update([
            'status' => 'uploaded',
            'paid_at' => now(),
        ]);
    }

    public function markAsVerified(int $verifiedBy): void
    {
        $this->update([
            'status' => 'verified',
            'verified_at' => now(),
            'verified_by' => $verifiedBy,
        ]);

        // Update booking status
        $this->booking->markAsPaid();
    }

    public function markAsRejected(string $reason, int $rejectedBy): void
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'verified_by' => $rejectedBy,
        ]);
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp '.number_format($this->amount, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Upload',
            'uploaded' => 'Menunggu Verifikasi',
            'verified' => 'Terverifikasi',
            'rejected' => 'Ditolak',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'uploaded' => 'info',
            'verified' => 'success',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }
}
