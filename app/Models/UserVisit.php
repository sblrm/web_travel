<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'destination_id',
        'visit_date',
        'notes',
        'is_verified',
    ];

    protected function casts(): array
    {
        return [
            'visit_date' => 'date',
            'is_verified' => 'boolean',
        ];
    }

    /**
     * Get the user who visited
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the destination that was visited
     */
    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }
}
