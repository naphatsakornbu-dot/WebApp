<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_match_id',
        'user_id',
        'status',
        'message',
    ];

    public function playerMatch(): BelongsTo
    {
        return $this->belongsTo(PlayerMatch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
