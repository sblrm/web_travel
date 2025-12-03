<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Destination extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'province_id',
        'category_id',
        'city',
        'description',
        'latitude',
        'longitude',
        'opening_hours',
        'closing_hours',
        'est_visit_duration',
        'ticket_price',
        'rating',
        'images',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'rating' => 'decimal:2',
            'images' => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Destination $destination) {
            if (empty($destination->slug)) {
                $destination->slug = Str::slug($destination->name);
            }
        });
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function verifiedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_verified', true);
    }

    /**
     * Get average rating from verified reviews
     */
    public function getAverageRatingAttribute(): float
    {
        $average = $this->verifiedReviews()->avg('rating');

        return $average ? round($average, 1) : ($this->rating ?? 0);
    }

    /**
     * Get total review count
     */
    public function getReviewCountAttribute(): int
    {
        return $this->verifiedReviews()->count();
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp '.number_format($this->ticket_price, 0, ',', '.');
    }

    public function getEstimatedDurationAttribute(): string
    {
        $hours = floor($this->est_visit_duration / 60);
        $minutes = $this->est_visit_duration % 60;

        if ($hours > 0) {
            return $hours.' jam '.($minutes > 0 ? $minutes.' menit' : '');
        }

        return $minutes.' menit';
    }
}
