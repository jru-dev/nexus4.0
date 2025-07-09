<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class CreditCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_number',
        'cardholder_name',
        'expiry_date',
        'cvv',
        'balance',
        'credit_limit',
        'bank_name',
        'card_type',
        'card_color',
        'is_active',
        'currency'
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'credit_limit' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByBank($query, $bank)
    {
        return $query->where('bank_name', $bank);
    }

    public function scopeByCardType($query, $type)
    {
        return $query->where('card_type', $type);
    }

    // Accessors y Mutators
    protected function cardNumber(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value,
            set: fn (string $value) => str_replace(' ', '', $value),
        );
    }

    // Métodos auxiliares
    public function getFormattedBalance()
    {
        return 'S/ ' . number_format($this->balance, 2);
    }

    public function getFormattedCreditLimit()
    {
        return 'S/ ' . number_format($this->credit_limit, 2);
    }

    public function getAvailableCredit()
    {
        return $this->credit_limit - $this->balance;
    }

    public function getFormattedAvailableCredit()
    {
        return 'S/ ' . number_format($this->getAvailableCredit(), 2);
    }

    public function getMaskedCardNumber()
    {
        $number = str_replace(' ', '', $this->card_number);
        return '**** **** **** ' . substr($number, -4);
    }

    public function getCardIcon()
    {
        return match($this->card_type) {
            'Visa' => '💳',
            'Mastercard' => '💳',
            'American Express' => '💎',
            default => '💳'
        };
    }

    public function getBankLogo()
    {
        return match($this->bank_name) {
            'Interbank' => '🏦',
            'BCP' => '🏛️',
            default => '🏦'
        };
    }

    // Validaciones de transacciones
    public function canMakePayment($amount)
    {
        return $this->is_active && $this->getAvailableCredit() >= $amount;
    }

    public function processPayment($amount, $description = null)
    {
        if (!$this->canMakePayment($amount)) {
            throw new \Exception('Fondos insuficientes o tarjeta inactiva');
        }

        $this->increment('balance', $amount);
        
        // Aquí podrías registrar la transacción en una tabla de historial
        return [
            'success' => true,
            'transaction_id' => 'TXN-' . strtoupper(uniqid()),
            'amount' => $amount,
            'new_balance' => $this->fresh()->balance,
            'available_credit' => $this->fresh()->getAvailableCredit(),
            'description' => $description ?? 'Compra en Nexus Games'
        ];
    }

    // Validar fecha de expiración
    public function isExpired()
    {
        $expiry = \Carbon\Carbon::createFromFormat('m/Y', $this->expiry_date);
        return $expiry->isPast();
    }

    // Validar CVV (simulado)
    public function validateCVV($cvv)
    {
        return $this->cvv === $cvv;
    }

    // Validar datos completos de la tarjeta
    public function validateCardData($cardNumber, $expiry, $cvv, $holderName)
    {
        $cleanCardNumber = str_replace(' ', '', $cardNumber);
        $cleanStoredNumber = str_replace(' ', '', $this->card_number);
        
        return $cleanStoredNumber === $cleanCardNumber &&
               $this->expiry_date === $expiry &&
               $this->cvv === $cvv &&
               strtoupper($this->cardholder_name) === strtoupper($holderName) &&
               !$this->isExpired();
    }
}