<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlayerMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'slots_needed',
        'slots_filled',
        'skill_level',
        'details',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'slots_needed' => 'integer',
            'slots_filled' => 'integer',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class)->withTrashed();
    }

    public function members(): HasMany
    {
        return $this->hasMany(MatchMember::class);
    }

    public function getRemainingSlotsAttribute(): int
    {
        return max(0, $this->slots_needed - $this->slots_filled);
    }
}
