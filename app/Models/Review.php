<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'game_id',
        'rating',
        'title',
        'content',
        'is_approved',
        'helpful_votes',
        'recommendation', // añadir este campo
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'rating' => 'integer',
        'helpful_votes' => 'integer',
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
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    public function scopeRecommended($query)
    {
        return $query->where('recommendation', 'recommended');
    }

    public function scopeNotRecommended($query)
    {
        return $query->where('recommendation', 'not_recommended');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Métodos auxiliares
    public function getStarsDisplay()
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    public function isRecommended()
    {
        return $this->recommendation === 'recommended';
    }

    public function getRecommendationText()
    {
        return $this->recommendation === 'recommended' ? 'RECOMENDADO' : 'NO RECOMENDADO';
    }

    public function getRecommendationIcon()
    {
        return $this->recommendation === 'recommended' ? '👍' : '👎';
    }

    public function getTimeAgo()
    {
        return $this->created_at->diffForHumans();
    }

    // Verificar si el usuario puede escribir una reseña para este juego
    public static function canUserReview($userId, $gameId)
    {
        // El usuario debe tener el juego en su biblioteca
        $ownsGame = \App\Models\UserLibrary::where('user_id', $userId)
                                          ->where('game_id', $gameId)
                                          ->exists();

        if (!$ownsGame) {
            return false;
        }

        // No debe tener ya una reseña para este juego
        $hasReview = self::where('user_id', $userId)
                        ->where('game_id', $gameId)
                        ->exists();

        return !$hasReview;
    }

    // Método para incrementar votos útiles
    public function incrementHelpful()
    {
        $this->increment('helpful_votes');
    }
}