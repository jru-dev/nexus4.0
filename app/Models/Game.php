<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'developer',
        'publisher',
        'release_date',
        'image_url',
        'screenshots',
        'system_requirements',
        'age_rating',
        'is_active',
        'category_id',
    ];

    protected $casts = [
        'release_date' => 'date',
        'screenshots' => 'array',
        'system_requirements' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    // Generar slug automáticamente
    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($game) {
            if (empty($game->slug)) {
                $game->slug = Str::slug($game->title);
            }
        });
    }

    // Relaciones
    public function category()
    {
        return $this->belongsTo(GameCategory::class, 'category_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function forumPosts()
    {
        return $this->hasMany(ForumPost::class);
    }

    public function userLibraries()
    {
        return $this->hasMany(UserLibrary::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    // Métodos auxiliares para reseñas
    public function getAverageRating()
    {
        return $this->reviews()->approved()->avg('rating') ?? 0;
    }

    public function getFormattedAverageRating()
    {
        return round($this->getAverageRating(), 1);
    }

    public function getTotalReviews()
    {
        return $this->reviews()->approved()->count();
    }

    public function getRecommendationPercentage()
    {
        $total = $this->getTotalReviews();
        if ($total === 0) return 0;
        
        $recommended = $this->reviews()->approved()->recommended()->count();
        return round(($recommended / $total) * 100, 1);
    }

    public function getRatingDistribution()
    {
        $total = $this->getTotalReviews();
        $distribution = [];
        
        for ($i = 1; $i <= 5; $i++) {
            $count = $this->reviews()->approved()->where('rating', $i)->count();
            $distribution[$i] = [
                'count' => $count,
                'percentage' => $total > 0 ? round(($count / $total) * 100, 1) : 0
            ];
        }
        
        return $distribution;
    }

    public function getRecentReviews($limit = 5)
    {
        return $this->reviews()
                   ->with('user')
                   ->approved()
                   ->orderBy('created_at', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public function getUserReview($userId)
    {
        return $this->reviews()->where('user_id', $userId)->first();
    }

    public function canUserReview($userId)
    {
        return Review::canUserReview($userId, $this->id);
    }

    public function getReviewsStats()
    {
        return [
            'total' => $this->getTotalReviews(),
            'average_rating' => $this->getFormattedAverageRating(),
            'recommendation_percentage' => $this->getRecommendationPercentage(),
            'rating_distribution' => $this->getRatingDistribution(),
            'recommended_count' => $this->reviews()->approved()->recommended()->count(),
            'not_recommended_count' => $this->reviews()->approved()->notRecommended()->count()
        ];
    }

    // Métodos existentes
    public function getFormattedPrice() 
    {
        return 'S/ ' . number_format($this->price, 2);
    }

    // NUEVOS MÉTODOS para filtros de edad
    public function canBeAccessedByAge($userAge)
    {
        $ageRestrictions = [
            'E' => 0,      // Para todos
            'E10+' => 10,  // 10 años o más
            'T' => 13,     // Adolescentes
            'M' => 17,     // Maduro
            'AO' => 18     // Solo adultos
        ];

        return $userAge >= ($ageRestrictions[$this->age_rating] ?? 0);
    }

    // Verificar si un usuario puede ver este juego
    public function isVisibleToUser($user = null)
    {
        // Si no está activo, solo los admins pueden verlo
        if (!$this->is_active) {
            return $user && $user->isAdmin();
        }

        // Si no hay usuario logueado, solo mostrar juegos E
        if (!$user) {
            return $this->age_rating === 'E';
        }

        // Verificar restricción de edad (asumir que el usuario tiene una edad)
        $userAge = $user->age ?? 18; // Default 18 si no tiene edad configurada
        return $this->canBeAccessedByAge($userAge);
    }

    // Método para obtener reseñas con paginación
    public function getReviewsPaginated($perPage = 10, $page = 1)
    {
        return $this->reviews()
                   ->with('user')
                   ->approved()
                   ->orderBy('created_at', 'desc')
                   ->paginate($perPage, ['*'], 'page', $page);
    }

    // Método helper para mostrar estrellas en templates
    public function getStarsDisplay()
    {
        $rating = $this->getFormattedAverageRating();
        $fullStars = floor($rating);
        $hasHalfStar = ($rating - $fullStars) >= 0.5;
        $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
        
        return [
            'full' => $fullStars,
            'half' => $hasHalfStar,
            'empty' => $emptyStars,
            'rating' => $rating
        ];
    }
}