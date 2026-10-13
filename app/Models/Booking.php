<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'court_id',
        'booking_date',
        'start_time',
        'end_time',
        'total_hours',
        'court_price',
        'equipment_price',
        'grand_total',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'total_hours' => 'decimal:2',
            'court_price' => 'decimal:2',
            'equipment_price' => 'decimal:2',
            'grand_total' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function court(): BelongsTo
    {
        return $this->belongsTo(Court::class)->withTrashed();
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function equipmentRentals(): HasMany
    {
        return $this->hasMany(EquipmentRental::class);
    }

    public function playerMatch(): HasOne
    {
        return $this->hasOne(PlayerMatch::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }
}
