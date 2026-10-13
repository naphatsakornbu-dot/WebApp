<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipments';

    protected $fillable = [
        'name',
        'category',
        'rental_price',
        'total_stock',
        'available_stock',
        'image',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'rental_price' => 'decimal:2',
            'total_stock' => 'integer',
            'available_stock' => 'integer',
        ];
    }

    public function equipmentRentals(): HasMany
    {
        return $this->hasMany(EquipmentRental::class);
    }
}
