<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Court extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'floor_type',
        'price_per_hour',
        'open_time',
        'close_time',
        'status',
        'image',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'price_per_hour' => 'decimal:2',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(Maintenance::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function getAverageRatingAttribute(): float
    {
        $avg = $this->reviews()->avg('rating');
        return $avg ? (float) round($avg, 1) : 0.0;
    }

    public function getReviewsCountAttribute(): int
    {
        return $this->reviews()->count();
    }

    public function getImageUrlAttribute(): string
    {
        if (!$this->image) {
            return asset('images/courts/court_1.jpg');
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        // 1. Direct path in public
        if (file_exists(public_path($this->image))) {
            return asset($this->image);
        }

        // 2. In public/images/
        if (file_exists(public_path('images/' . $this->image))) {
            return asset('images/' . $this->image);
        }

        // 3. In public/images/courts/
        if (file_exists(public_path('images/courts/' . $this->image))) {
            return asset('images/courts/' . $this->image);
        }

        // 4. In public/image/
        if (file_exists(public_path('image/' . $this->image))) {
            return asset('image/' . $this->image);
        }

        // 5. In public/image/courts/
        if (file_exists(public_path('image/courts/' . $this->image))) {
            return asset('image/courts/' . $this->image);
        }

        // 6. In storage/
        if (file_exists(public_path('storage/' . $this->image))) {
            return asset('storage/' . $this->image);
        }

        // 7. Base filename fallback
        $base = basename($this->image);
        if (file_exists(public_path('images/courts/' . $base))) {
            return asset('images/courts/' . $base);
        }
        if (file_exists(public_path('image/' . $base))) {
            return asset('image/' . $base);
        }
        if (file_exists(public_path('images/' . $base))) {
            return asset('images/' . $base);
        }

        return asset('images/courts/court_1.jpg');
    }
}
