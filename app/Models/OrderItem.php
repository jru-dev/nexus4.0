<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'game_id',
        'game_title',
        'price',
        'quantity',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    // Relaciones
    public function order()
    {
        return $this->belongsTo(Order::class);
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