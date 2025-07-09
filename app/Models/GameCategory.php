<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GameCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    // Generar slug automáticamente
    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    // Relaciones
    public function games()
    {
        return $this->hasMany(Game::class, 'category_id');
    }

    // Scope para categorías con juegos activos
    public function scopeWithActiveGames($query)
    {
        return $query->whereHas('games', function ($q) {
            $q->where('is_active', true);
        });
    }
}