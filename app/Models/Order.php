<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'total_amount',
        'status',
        'payment_method',
        'payment_status',
        'payment_details',
        'yape_qr_code',
        'transaction_reference',
        'billing_details',
        'completed_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'payment_details' => 'array',
        'billing_details' => 'array',
        'completed_at' => 'datetime',
    ];

    // Generar número de orden automáticamente
    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'NX-' . strtoupper(Str::random(8));
            }
        });
    }

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Métodos auxiliares
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'payment_status' => 'paid',
            'completed_at' => now(),
        ]);
        
        // Agregar juegos a la biblioteca del usuario
        foreach ($this->items as $item) {
            UserLibrary::firstOrCreate([
                'user_id' => $this->user_id,
                'game_id' => $item->game_id,
            ], [
                'purchased_at' => now(),
                'purchase_price' => $item->price,
                'hours_played' => 0,
                'status' => 'not_played',
                'is_favorite' => false,
            ]);
        }
    }

    public function getFormattedTotal()
    {
        return 'S/ ' . number_format($this->total_amount, 2);
    }

    // *** NUEVOS MÉTODOS PARA YAPE ***
    
    /**
     * Verificar si la orden es de tipo Yape
     */
    public function isYapePayment()
    {
        return $this->payment_method === 'yape';
    }

    /**
     * Verificar si la orden está expirada (para Yape)
     */
    public function isExpired()
    {
        if (!$this->isYapePayment() || $this->status !== 'pending') {
            return false;
        }

        // Las órdenes Yape expiran en 15 minutos
        return $this->created_at->addMinutes(15)->isPast();
    }

    /**
     * Obtener tiempo restante para expiración (en segundos)
     */
    public function getTimeToExpiry()
    {
        if (!$this->isYapePayment() || $this->status !== 'pending') {
            return 0;
        }

        $expiryTime = $this->created_at->addMinutes(15);
        return now()->diffInSeconds($expiryTime, false);
    }

    /**
     * Marcar orden como expirada
     */
    public function markAsExpired()
    {
        if ($this->isYapePayment() && $this->status === 'pending') {
            $this->update([
                'status' => 'cancelled',
                'payment_status' => 'expired'
            ]);
        }
    }

    /**
     * Generar datos para QR de Yape con número específico
     */
    public function generateYapeQR()
    {
        if (!$this->isYapePayment()) {
            return null;
        }

        // Número de Yape de destino (tu número)
        $yapePhoneNumber = '935592953';

        $qrData = [
            'merchant' => 'NEXUS GAMES',
            'phone' => $yapePhoneNumber,
            'amount' => number_format($this->total_amount, 2, '.', ''),
            'currency' => 'PEN',
            'reference' => $this->order_number,
            'description' => "Compra Nexus Games - {$this->items->count()} juego(s)",
            'expiry' => $this->created_at->addMinutes(15)->toISOString(),
            'qr_string' => $this->generateYapeQRString($yapePhoneNumber)
        ];

        // Guardar en la base de datos
        $this->update(['yape_qr_code' => json_encode($qrData)]);

        return $qrData;
    }

    /**
     * Generar string específico para QR de Yape
     */
    private function generateYapeQRString($phoneNumber)
    {
        // Formato que Yape puede interpretar para abrir la app directamente
        $yapeData = [
            'type' => 'yape_payment',
            'phone' => $phoneNumber,
            'amount' => $this->total_amount,
            'currency' => 'PEN',
            'concept' => "Nexus Games - Orden {$this->order_number}",
            'reference' => $this->order_number
        ];
        
        // URL scheme de Yape para abrir la app directamente
        return 'yape://payment?' . http_build_query($yapeData);
    }

    /**
     * Obtener datos del QR guardados
     */
    public function getYapeQRData()
    {
        if (!$this->yape_qr_code) {
            return $this->generateYapeQR();
        }

        return json_decode($this->yape_qr_code, true);
    }

    /**
     * Confirmar pago Yape
     */
    public function confirmYapePayment($transactionCode)
    {
        if (!$this->isYapePayment() || $this->status !== 'pending') {
            throw new \Exception('Esta orden no puede ser confirmada');
        }

        if ($this->isExpired()) {
            throw new \Exception('Esta orden ha expirado');
        }

        // Validar formato del código
        if (!preg_match('/^YPE\d{6,}$/i', $transactionCode)) {
            throw new \Exception('Código de transacción inválido');
        }

        // Actualizar orden
        $this->update([
            'status' => 'completed',
            'payment_status' => 'paid',
            'completed_at' => now(),
            'transaction_reference' => $transactionCode,
            'payment_details' => array_merge($this->payment_details ?? [], [
                'transaction_code' => $transactionCode,
                'confirmed_at' => now()->toISOString()
            ])
        ]);

        // Agregar juegos a biblioteca
        $this->markAsCompleted();

        return true;
    }

    /**
     * Cancelar orden
     */
    public function cancel($reason = 'user_cancelled')
    {
        if ($this->status === 'completed') {
            throw new \Exception('No se puede cancelar una orden completada');
        }

        $this->update([
            'status' => 'cancelled',
            'payment_status' => 'cancelled',
            'payment_details' => array_merge($this->payment_details ?? [], [
                'cancelled_at' => now()->toISOString(),
                'cancel_reason' => $reason
            ])
        ]);
    }

    /**
     * Obtener estado para mostrar al usuario
     */
    public function getStatusDisplay()
    {
        $statuses = [
            'pending' => [
                'text' => 'Pendiente',
                'icon' => '⏳',
                'color' => '#f39c12'
            ],
            'completed' => [
                'text' => 'Completado',
                'icon' => '✅',
                'color' => '#27ae60'
            ],
            'cancelled' => [
                'text' => 'Cancelado',
                'icon' => '❌',
                'color' => '#e74c3c'
            ],
            'processing' => [
                'text' => 'Procesando',
                'icon' => '🔄',
                'color' => '#3498db'
            ]
        ];

        return $statuses[$this->status] ?? [
            'text' => 'Desconocido',
            'icon' => '❓',
            'color' => '#95a5a6'
        ];
    }

    /**
     * Obtener método de pago formateado
     */
    public function getPaymentMethodDisplay()
    {
        $methods = [
            'credit_card' => [
                'text' => 'Tarjeta de Crédito',
                'icon' => '💳'
            ],
            'yape' => [
                'text' => 'Yape',
                'icon' => '📱'
            ]
        ];

        return $methods[$this->payment_method] ?? [
            'text' => 'Otro',
            'icon' => '💰'
        ];
    }

    // Método existente actualizado
    public function generateYapeQROld()
    {
        // Aquí puedes integrar con una librería para generar QR
        // Por ahora, simulamos la generación
        $qrData = [
            'amount' => $this->total_amount,
            'reference' => $this->order_number,
            'merchant' => 'Nexus Games'
        ];
        
        $this->update(['yape_qr_code' => json_encode($qrData)]);
        return $qrData;
    }
}