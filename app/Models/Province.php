<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Province extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Province $province) {
            if (empty($province->slug)) {
                $province->slug = Str::slug($province->name);
            }
        });
    }

    public function destinations(): HasMany
    {
        return $this->hasMany(Destination::class);
    }
}
