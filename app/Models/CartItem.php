<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'game_id',
        'quantity',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    // Relaciones
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    // Métodos auxiliares
    public function getSubtotal()
    {
        return $this->price * $this->quantity;
    }

    public function getFormattedSubtotal()
    {
        return 'S/ ' . number_format($this->getSubtotal(), 2);
    }
}