<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'destination_id',
        'rating',
        'comment',
        'images',
        'is_verified',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_verified' => 'boolean',
            'images' => 'array',
        ];
    }

    /**
     * Get the user who wrote this review
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the destination being reviewed
     */
    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    /**
     * Get votes for this review
     */
    public function votes(): HasMany
    {
        return $this->hasMany(ReviewVote::class);
    }

    /**
     * Check if user has voted on this review
     */
    public function userVote($userId = null)
    {
        $userId = $userId ?? auth()->id();

        return $this->votes()->where('user_id', $userId)->first();
    }

    /**
     * Get helpful votes count
     */
    public function getHelpfulVotesCountAttribute(): int
    {
        return $this->votes()->where('is_helpful', true)->count();
    }

    /**
     * Get unhelpful votes count
     */
    public function getUnhelpfulVotesCountAttribute(): int
    {
        return $this->votes()->where('is_helpful', false)->count();
    }

    /**
     * Scope to only get verified reviews
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope to get recent reviews
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope to order by most helpful
     */
    public function scopeMostHelpful($query)
    {
        return $query->orderBy('helpful_count', 'desc');
    }

    /**
     * Check if user has verified visit to this destination
     */
    public function hasVerifiedVisit(): bool
    {
        return UserVisit::where('user_id', $this->user_id)
            ->where('destination_id', $this->destination_id)
            ->where('is_verified', true)
            ->exists();
    }

    /**
     * Get formatted date
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->created_at->locale('id')->diffForHumans();
    }
}
