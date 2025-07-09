<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Métodos auxiliares
    public function getTotalItems()
    {
        return $this->items()->sum('quantity');
    }

    // MÉTODO CORREGIDO: Calcular precio total
    public function getTotalPrice()
    {
        return $this->items()->get()->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }

    public function addGame($game, $quantity = 1)
    {
        // Verificar si el juego ya está en el carrito
        $existingItem = $this->items()->where('game_id', $game->id)->first();
        
        if ($existingItem) {
            $existingItem->update(['quantity' => $existingItem->quantity + $quantity]);
            return $existingItem;
        }
        
        return $this->items()->create([
            'game_id' => $game->id,
            'quantity' => $quantity,
            'price' => $game->price,
        ]);
    }

    public function removeGame($gameId)
    {
        return $this->items()->where('game_id', $gameId)->delete();
    }

    public function clearCart()
    {
        return $this->items()->delete();
    }
}