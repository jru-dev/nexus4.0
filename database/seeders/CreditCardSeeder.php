<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CreditCard;

class CreditCardSeeder extends Seeder
{
    public function run(): void
    {
        $cards = [
            // INTERBANK - 3 tarjetas
            [
                'card_number' => '4532 1234 5678 9012',
                'cardholder_name' => 'JUAN CARLOS PEREZ',
                'expiry_date' => '12/2027',
                'cvv' => '123',
                'balance' => 1000.00,
                'credit_limit' => 5000.00,
                'bank_name' => 'Interbank',
                'card_type' => 'Visa',
                'card_color' => '#00a651', // Verde Interbank
                'currency' => 'PEN'
            ],
            [
                'card_number' => '5555 4444 3333 2222',
                'cardholder_name' => 'MARIA LOPEZ GARCIA',
                'expiry_date' => '08/2026',
                'cvv' => '456',
                'balance' => 1000.00,
                'credit_limit' => 7500.00,
                'bank_name' => 'Interbank',
                'card_type' => 'Mastercard',
                'card_color' => '#1565c0', // Azul Interbank
                'currency' => 'PEN'
            ],
            [
                'card_number' => '3782 822463 10005',
                'cardholder_name' => 'CARLOS RAMIREZ TORRES',
                'expiry_date' => '03/2028',
                'cvv' => '789',
                'balance' => 1000.00,
                'credit_limit' => 10000.00,
                'bank_name' => 'Interbank',
                'card_type' => 'American Express',
                'card_color' => '#2e7d32', // Verde oscuro Interbank
                'currency' => 'PEN'
            ],

            // BCP - 3 tarjetas
            [
                'card_number' => '4111 1111 1111 1111',
                'cardholder_name' => 'ANA SOFIA MENDOZA',
                'expiry_date' => '06/2027',
                'cvv' => '321',
                'balance' => 1000.00,
                'credit_limit' => 4500.00,
                'bank_name' => 'BCP',
                'card_type' => 'Visa',
                'card_color' => '#1976d2', // Azul BCP
                'currency' => 'PEN'
            ],
            [
                'card_number' => '5105 1051 0510 5100',
                'cardholder_name' => 'RICARDO VARGAS SILVA',
                'expiry_date' => '11/2026',
                'cvv' => '654',
                'balance' => 1000.00,
                'credit_limit' => 6000.00,
                'bank_name' => 'BCP',
                'card_type' => 'Mastercard',
                'card_color' => '#0d47a1', // Azul oscuro BCP
                'currency' => 'PEN'
            ],
            [
                'card_number' => '3715 123456 78901',
                'cardholder_name' => 'LUCIA FERNANDEZ CASTRO',
                'expiry_date' => '09/2025',
                'cvv' => '987',
                'balance' => 1000.00,
                'credit_limit' => 8500.00,
                'bank_name' => 'BCP',
                'card_type' => 'American Express',
                'card_color' => '#1565c0', // Azul medio BCP
                'currency' => 'PEN'
            ],
        ];

        foreach ($cards as $card) {
            CreditCard::create($card);
        }
    }
}