<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'game_id',
        'title',
        'content',
        'images',
        'type',
        'is_pinned',
        'is_locked',
        'views',
        'replies_count',
        'last_activity',
    ];

    protected $casts = [
        'images' => 'array',
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'last_activity' => 'datetime',
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

    public function replies()
    {
        return $this->hasMany(ForumReply::class);
    }

    // Scopes
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByGame($query, $gameId)
    {
        return $query->where('game_id', $gameId);
    }

    // Métodos auxiliares
    public function incrementViews()
    {
        $this->increment('views');
    }

    public function updateLastActivity()
    {
        $this->update(['last_activity' => now()]);
    }

    public function canReply()
    {
        return !$this->is_locked;
    }
}