<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLibrary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'game_id',
        'purchased_at',
        'purchase_price',
        'hours_played',
        'status',
        'is_favorite',
    ];

    protected $casts = [
        'purchased_at' => 'datetime',
        'purchase_price' => 'decimal:2',
        'is_favorite' => 'boolean',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    // Scopes
    public function scopeFavorites($query)
    {
        return $query->where('is_favorite', true);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Métodos auxiliares
    public function addPlayTime($hours)
    {
        $this->increment('hours_played', $hours);
    }

    public function getFormattedPlayTime()
    {
        $hours = $this->hours_played;
        if ($hours < 1) {
            return 'Menos de 1 hora';
        }
        return $hours . ' hora' . ($hours > 1 ? 's' : '');
    }
}