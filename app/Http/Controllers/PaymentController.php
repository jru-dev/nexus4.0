<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CreditCard;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Game;
use App\Models\UserLibrary;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Mostrar formulario de pago
     */
    public function showCartPaymentForm()
    {
        try {
            $user = Auth::user();
            $cart = $user->getActiveCart();
            $cartItems = $cart->items()->with(['game.category'])->get();
            
            if ($cartItems->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Tu carrito está vacío');
            }

            // Calcular totales correctamente
            $subtotal = $cartItems->sum(function($item) {
                return $item->price * $item->quantity;
            });
            
            $igv = $subtotal * 0.18; // 18% de IGV
            $total = $subtotal + $igv; // Total = subtotal + IGV

            // Obtener tarjetas disponibles
            $interbankCards = CreditCard::active()->byBank('Interbank')->get();
            $bcpCards = CreditCard::active()->byBank('BCP')->get();

            return view('payment.cart-form', compact('cartItems', 'subtotal', 'igv', 'total', 'interbankCards', 'bcpCards'));
            
        } catch (\Exception $e) {
            Log::error('Error al mostrar formulario de pago del carrito: ' . $e->getMessage());
            return redirect()->route('cart.index')->with('error', 'Error al cargar el formulario de pago');
        }
    }


    /**
     * Procesar el pago - VALIDACIÓN CORREGIDA
     */
    public function processPayment(Request $request)
    {
        // CAMBIO PRINCIPAL: Usar array para reglas de validación con regex
        $request->validate([
            'game_id' => 'required|exists:games,id',
            'card_number' => 'required|string',
            'cardholder_name' => [
                'required',
                'string',
                'max:255'
            ],
            'expiry_date' => [
                'required',
                'string',
                'regex:/^(0[1-9]|1[0-2])\/\d{4}$/'
            ],
            'cvv' => [
                'required',
                'string',
                'min:3',
                'max:4'
            ],
        ], [
            'card_number.required' => 'El número de tarjeta es obligatorio',
            'cardholder_name.required' => 'El nombre del titular es obligatorio',
            'expiry_date.required' => 'La fecha de expiración es obligatoria',
            'expiry_date.regex' => 'La fecha debe tener el formato MM/YYYY',
            'cvv.required' => 'El CVV es obligatorio',
        ]);

        try {
            DB::beginTransaction();

            $game = Game::findOrFail($request->game_id);
            $user = Auth::user();

            // Verificar nuevamente que no tenga el juego
            if ($user->ownsGame($request->game_id)) {
                throw new \Exception('Ya tienes este juego en tu biblioteca');
            }

            // Buscar la tarjeta que coincida con los datos ingresados
            $card = $this->findMatchingCard(
                $request->card_number,
                $request->expiry_date,
                $request->cvv,
                $request->cardholder_name
            );

            if (!$card) {
                throw new \Exception('Los datos de la tarjeta son incorrectos');
            }

            // Verificar que la tarjeta puede hacer el pago
            if (!$card->canMakePayment($game->price)) {
                throw new \Exception('Fondos insuficientes en la tarjeta');
            }

            // Procesar el pago
            $paymentResult = $card->processPayment($game->price, "Compra de {$game->title}");

            // Crear la orden
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $game->price,
                'status' => 'completed',
                'payment_method' => 'credit_card',
                'payment_status' => 'paid',
                'payment_details' => [
                    'card_type' => $card->card_type,
                    'bank_name' => $card->bank_name,
                    'last_four' => substr(str_replace(' ', '', $card->card_number), -4),
                    'transaction_id' => $paymentResult['transaction_id']
                ],
                'billing_details' => [
                    'cardholder_name' => $card->cardholder_name,
                    'billing_address' => 'Lima, Perú' // Simplificado
                ],
                'completed_at' => now(),
            ]);

            // Crear el item de la orden
            OrderItem::create([
                'order_id' => $order->id,
                'game_id' => $game->id,
                'game_title' => $game->title,
                'price' => $game->price,
                'quantity' => 1,
            ]);

            // *** PASO CRÍTICO: Agregar el juego a la biblioteca del usuario ***
            UserLibrary::create([
                'user_id' => $user->id,
                'game_id' => $game->id,
                'purchased_at' => now(),
                'purchase_price' => $game->price,
                'hours_played' => 0,
                'status' => 'not_played',
                'is_favorite' => false,
            ]);

            // *** IMPORTANTE: Eliminar el juego del carrito si estaba allí ***
            $cart = $user->getActiveCart();
            $cartItem = $cart->items()->where('game_id', $game->id)->first();
            if ($cartItem) {
                $cartItem->delete();
                Log::info("Juego {$game->title} eliminado del carrito después de la compra");
            }

            DB::commit();
            
            Log::info("Compra completada exitosamente: Usuario {$user->id} compró {$game->title}");

            return redirect()->route('payment.success', $order->id)->with('success', 'Compra realizada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en procesamiento de pago: ' . $e->getMessage());
            
            return back()->withInput()->withErrors(['payment' => $e->getMessage()]);
        }
    }

    /**
     * Buscar tarjeta que coincida con los datos ingresados
     */
    private function findMatchingCard($cardNumber, $expiryDate, $cvv, $holderName)
    {
        try {
            $cards = CreditCard::active()->get();

            foreach ($cards as $card) {
                if ($card->validateCardData($cardNumber, $expiryDate, $cvv, $holderName)) {
                    return $card;
                }
            }

            return null;
            
        } catch (\Exception $e) {
            Log::error('Error al buscar tarjeta: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Página de éxito
     */
    public function paymentSuccess($orderId)
    {
        try {
            $order = Order::with(['items.game', 'user'])
                         ->where('id', $orderId)
                         ->where('user_id', Auth::id())
                         ->firstOrFail();

            return view('payment.success', compact('order'));
            
        } catch (\Exception $e) {
            Log::error('Error al mostrar página de éxito: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Orden no encontrada');
        }
    }

    /**
     * API para obtener información de tarjetas por banco (para el frontend)
     */
    public function getCardsByBank($bank)
    {
        try {
            $cards = CreditCard::active()
                              ->byBank($bank)
                              ->get()
                              ->map(function ($card) {
                                  return [
                                      'id' => $card->id,
                                      'masked_number' => $card->getMaskedCardNumber(),
                                      'card_type' => $card->card_type,
                                      'bank_name' => $card->bank_name,
                                      'available_credit' => $card->getFormattedAvailableCredit(),
                                      'icon' => $card->getCardIcon(),
                                      'color' => $card->card_color
                                  ];
                              });

            return response()->json($cards);
            
        } catch (\Exception $e) {
            Log::error('Error al obtener tarjetas por banco: ' . $e->getMessage());
            
            return response()->json([]);
        }
    }

    public function initiateYapePayment(Request $request)
    {
        $request->validate([
            'total_amount' => 'required|numeric|min:1'
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $cart = $user->getActiveCart();
            $cartItems = $cart->items()->with('game')->get();

            if ($cartItems->isEmpty()) {
                throw new \Exception('El carrito está vacío');
            }

            // Calcular total
            $totalAmount = $cartItems->sum(function($item) {
                return $item->price * $item->quantity;
            });

            // Crear orden pendiente
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_method' => 'yape',
                'payment_status' => 'pending',
                'payment_details' => [
                    'method' => 'yape_qr',
                    'qr_generated_at' => now()->toISOString()
                ],
                'billing_details' => [
                    'user_name' => $user->name,
                    'user_email' => $user->email
                ]
            ]);

            // Crear items de orden
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'game_id' => $cartItem->game->id,
                    'game_title' => $cartItem->game->title,
                    'price' => $cartItem->price,
                    'quantity' => $cartItem->quantity,
                ]);
            }

            // Generar QR de Yape
            $yapeData = $this->generateYapeQR($order);

            DB::commit();

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'qr_data' => $yapeData,
                'total_amount' => $totalAmount,
                'redirect_url' => route('payment.yape.show', $order->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en pago Yape: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

        /**
         * NUEVO: Mostrar página de pago Yape con QR
         */
    public function showYapePayment($orderId)
        {
            try {
                $order = Order::with(['items.game', 'user'])
                            ->where('id', $orderId)
                            ->where('user_id', Auth::id())
                            ->where('payment_method', 'yape')
                            ->firstOrFail();

                // Si ya está pagada, redirigir a éxito
                if ($order->status === 'completed') {
                    return redirect()->route('payment.success', $order->id);
                }

                $yapeData = $this->generateYapeQR($order);

                return view('payment.yape-qr', compact('order', 'yapeData'));
                
            } catch (\Exception $e) {
                Log::error('Error al mostrar pago Yape: ' . $e->getMessage());
                return redirect()->route('cart.index')->with('error', 'Orden no encontrada');
            }
        }

        /**
         * NUEVO: Confirmar pago Yape (simulado)
         */
    public function confirmYapePayment(Request $request, $orderId)
        {
            $request->validate([
                'transaction_code' => 'required|string|min:6|max:20'
            ]);

            try {
                DB::beginTransaction();

                $order = Order::where('id', $orderId)
                            ->where('user_id', Auth::id())
                            ->where('payment_method', 'yape')
                            ->where('status', 'pending')
                            ->firstOrFail();

                $user = Auth::user();

                // Simular validación de código de transacción
                if (!$this->validateYapeTransaction($request->transaction_code)) {
                    throw new \Exception('Código de transacción inválido');
                }

                // Actualizar orden como completada
                $order->update([
                    'status' => 'completed',
                    'payment_status' => 'paid',
                    'completed_at' => now(),
                    'transaction_reference' => $request->transaction_code,
                    'payment_details' => array_merge($order->payment_details ?? [], [
                        'transaction_code' => $request->transaction_code,
                        'confirmed_at' => now()->toISOString()
                    ])
                ]);

                // Agregar juegos a biblioteca
                foreach ($order->items as $item) {
                    if (!$user->ownsGame($item->game_id)) {
                        UserLibrary::create([
                            'user_id' => $user->id,
                            'game_id' => $item->game_id,
                            'purchased_at' => now(),
                            'purchase_price' => $item->price,
                            'hours_played' => 0,
                            'status' => 'not_played',
                            'is_favorite' => false,
                        ]);
                    }
                }

                // Limpiar carrito
                $user->getActiveCart()->items()->delete();

                DB::commit();
                
                Log::info("Pago Yape confirmado: Orden {$order->id} - Usuario {$user->id}");

                return response()->json([
                    'success' => true,
                    'message' => 'Pago confirmado exitosamente',
                    'redirect_url' => route('payment.success', $order->id)
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error al confirmar pago Yape: ' . $e->getMessage());
                
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
        }

        /**
         * NUEVO: Verificar estado de orden Yape
         */
    public function checkYapeStatus($orderId)
        {
            try {
                $order = Order::where('id', $orderId)
                            ->where('user_id', Auth::id())
                            ->firstOrFail();

                return response()->json([
                    'success' => true,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'completed_at' => $order->completed_at,
                    'can_retry' => $order->status === 'pending'
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Orden no encontrada'
                ], 404);
            }
        }

        /**
         * NUEVO: Generar datos del QR de Yape
         */
    private function generateYapeQR($order)
        {
            // Número de Yape de destino
            $yapePhoneNumber = '935592953'; // Tu número Yape
            
            // Generar QR data para Yape con formato específico
            $qrData = [
                'merchant' => 'NEXUS GAMES',
                'phone' => $yapePhoneNumber,
                'amount' => number_format($order->total_amount, 2, '.', ''),
                'currency' => 'PEN',
                'reference' => $order->order_number,
                'description' => "Compra Nexus Games - {$order->items->count()} juego(s)",
                'expiry' => now()->addMinutes(15)->toISOString(),
                'qr_string' => $this->generateYapeQRString($order, $yapePhoneNumber)
            ];

            return $qrData;
        }

        /**
         * NUEVO: Generar string para QR
         */
    private function generateYapeQRString($order, $phoneNumber)
        {
            // Formato específico de Yape para QR con número de teléfono
            // Este es el formato que Yape reconoce para pagos directos
            $yapeData = [
                'type' => 'yape_payment',
                'phone' => $phoneNumber,
                'amount' => $order->total_amount,
                'currency' => 'PEN',
                'concept' => "Nexus Games - Orden {$order->order_number}",
                'reference' => $order->order_number
            ];
            
            // Codificar en formato que Yape puede interpretar
            return 'yape://payment?' . http_build_query($yapeData);
        }

        /**
         * NUEVO: Validar código de transacción Yape (simulado)
         */
    private function validateYapeTransaction($code)
        {
            // Simulación: aceptar códigos que empiecen con "YPE" seguido de números
            return preg_match('/^YPE\d{6,}$/', strtoupper($code));
        }

        /**
         * NUEVO: Cancelar orden Yape
         */
    public function cancelYapeOrder($orderId)
        {
            try {
                $order = Order::where('id', $orderId)
                            ->where('user_id', Auth::id())
                            ->where('status', 'pending')
                            ->firstOrFail();

                $order->update([
                    'status' => 'cancelled',
                    'payment_status' => 'cancelled'
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Orden cancelada',
                    'redirect_url' => route('cart.index')
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo cancelar la orden'
                ], 500);
            }
        }



    /**
     * Verificar disponibilidad de fondos (AJAX)
     */
    public function checkCardFunds(Request $request)
    {
        // VALIDACIÓN CORREGIDA para AJAX
        $request->validate([
            'card_number' => 'required|string',
            'amount' => 'required|numeric|min:0'
        ]);

        try {
            $card = CreditCard::active()
                             ->where('card_number', $request->card_number)
                             ->first();

            if (!$card) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tarjeta no encontrada'
                ]);
            }

            $canPay = $card->canMakePayment($request->amount);

            return response()->json([
                'success' => true,
                'can_pay' => $canPay,
                'available_credit' => $card->getFormattedAvailableCredit(),
                'message' => $canPay ? 'Fondos suficientes' : 'Fondos insuficientes'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al verificar fondos: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar fondos'
            ]);
        }
    }

    /**
     * Procesar compra desde carrito (múltiples juegos)
     */
    public function processCartPayment(Request $request)
    {
        // VALIDACIÓN CORREGIDA para carrito
        $request->validate([
            'card_number' => 'required|string',
            'cardholder_name' => [
                'required',
                'string',
                'max:255'
            ],
            'expiry_date' => [
                'required',
                'string',
                'regex:/^(0[1-9]|1[0-2])\/\d{4}$/'
            ],
            'cvv' => [
                'required',
                'string',
                'min:3',
                'max:4'
            ],
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $cart = $user->getActiveCart();
            $cartItems = $cart->items()->with('game')->get();

            if ($cartItems->isEmpty()) {
                throw new \Exception('El carrito está vacío');
            }

            // Calcular total
            $totalAmount = $cartItems->sum(function($item) {
                return $item->price * $item->quantity;
            });

            // Buscar tarjeta
            $card = $this->findMatchingCard(
                $request->card_number,
                $request->expiry_date,
                $request->cvv,
                $request->cardholder_name
            );

            if (!$card) {
                throw new \Exception('Los datos de la tarjeta son incorrectos');
            }

            if (!$card->canMakePayment($totalAmount)) {
                throw new \Exception('Fondos insuficientes en la tarjeta');
            }

            // Procesar pago
            $paymentResult = $card->processPayment($totalAmount, "Compra de {$cartItems->count()} juegos");

            // Crear orden
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $totalAmount,
                'status' => 'completed',
                'payment_method' => 'credit_card',
                'payment_status' => 'paid',
                'payment_details' => [
                    'card_type' => $card->card_type,
                    'bank_name' => $card->bank_name,
                    'last_four' => substr(str_replace(' ', '', $card->card_number), -4),
                    'transaction_id' => $paymentResult['transaction_id']
                ],
                'billing_details' => [
                    'cardholder_name' => $card->cardholder_name,
                    'billing_address' => 'Lima, Perú'
                ],
                'completed_at' => now(),
            ]);

            // Crear items de orden y agregar a biblioteca
            foreach ($cartItems as $cartItem) {
                // Crear item de orden
                OrderItem::create([
                    'order_id' => $order->id,
                    'game_id' => $cartItem->game->id,
                    'game_title' => $cartItem->game->title,
                    'price' => $cartItem->price,
                    'quantity' => $cartItem->quantity,
                ]);

                // Agregar a biblioteca si no lo tiene ya
                if (!$user->ownsGame($cartItem->game->id)) {
                    UserLibrary::create([
                        'user_id' => $user->id,
                        'game_id' => $cartItem->game->id,
                        'purchased_at' => now(),
                        'purchase_price' => $cartItem->price,
                        'hours_played' => 0,
                        'status' => 'not_played',
                        'is_favorite' => false,
                    ]);
                }
            }

            // Limpiar carrito
            $cart->items()->delete();

            DB::commit();
            
            Log::info("Compra de carrito completada: Usuario {$user->id} compró {$cartItems->count()} juegos");

            return redirect()->route('payment.success', $order->id)
                           ->with('success', 'Compra realizada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en pago de carrito: ' . $e->getMessage());
            
            return back()->withInput()->withErrors(['payment' => $e->getMessage()]);
        }
    }

    /**
     * Refund - Reembolso (para funcionalidad futura)
     */
    public function refund(Order $order)
    {
        try {
            // Verificar que la orden pertenezca al usuario
            if ($order->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para reembolsar esta orden'
                ], 403);
            }

            // Verificar que se pueda reembolsar (menos de 24 horas)
            if ($order->completed_at->diffInHours(now()) > 24) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden reembolsar compras realizadas en las últimas 24 horas'
                ], 400);
            }

            DB::beginTransaction();

            // Actualizar orden
            $order->update([
                'status' => 'refunded',
                'payment_status' => 'refunded'
            ]);

            // Eliminar juegos de la biblioteca
            foreach ($order->items as $item) {
                UserLibrary::where('user_id', $order->user_id)
                          ->where('game_id', $item->game_id)
                          ->delete();
            }

            // Aquí se procesaría el reembolso real a la tarjeta
            // Por ahora es simulado

            DB::commit();
            
            Log::info("Reembolso procesado para orden {$order->id}");

            return response()->json([
                'success' => true,
                'message' => 'Reembolso procesado exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en reembolso: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el reembolso'
            ], 500);
        }
    }
}