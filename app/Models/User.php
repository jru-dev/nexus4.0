<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_image',
        'bio',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Verificar si es admin
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    // Relaciones
    public function cart()
    {
        return $this->hasOne(Cart::class)->where('status', 'active');
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function forumPosts()
    {
        return $this->hasMany(ForumPost::class);
    }

    public function forumReplies()
    {
        return $this->hasMany(ForumReply::class);
    }

    public function library()
    {
        return $this->hasMany(UserLibrary::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Verificar si tiene un juego en su biblioteca
    public function ownsGame($gameId)
    {
        return $this->library()->where('game_id', $gameId)->exists();
    }

    // MÉTODO CORREGIDO: Obtener carrito activo o crear uno nuevo
    public function getActiveCart()
    {
        // Buscar carrito activo existente
        $activeCart = $this->carts()->where('status', 'active')->first();
        
        // Si no existe, crear uno nuevo
        if (!$activeCart) {
            $activeCart = $this->carts()->create([
                'status' => 'active'
            ]);
        }
        
        return $activeCart;
    }
}