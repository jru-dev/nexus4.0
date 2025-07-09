<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pagar Carrito - Nexus</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #2c1810 0%, #1a0e2e 50%, #0f051a 100%);
            color: white;
            min-height: 100vh;
            padding: 20px;
        }

        .payment-container {
            max-width: 900px;
            margin: 0 auto;
            background: rgba(0, 0, 0, 0.8);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
        }

        .payment-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .payment-title {
            font-size: 2.5em;
            margin-bottom: 10px;
            color: #5a4fcf;
        }

        .cart-summary-section {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .cart-items-list {
            margin-bottom: 20px;
        }

        .cart-item-payment {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 10px;
            margin-bottom: 10px;
            border-left: 3px solid #5a4fcf;
        }

        .cart-item-image {
            width: 80px;
            height: 50px;
            object-fit: cover;
            border-radius: 6px;
        }

        .cart-item-details {
            flex: 1;
        }

        .cart-item-title {
            font-size: 1.1em;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .cart-item-developer {
            color: #ccc;
            font-size: 0.9em;
        }

        .cart-item-price {
            font-size: 1.2em;
            color: #27ae60;
            font-weight: bold;
        }

        .payment-totals {
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            padding-top: 20px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 1.1em;
        }

        .total-row.final {
            font-size: 1.4em;
            font-weight: bold;
            color: #27ae60;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            padding-top: 15px;
            margin-top: 15px;
        }

        /* Selector de método de pago */
        .payment-method-selector {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            justify-content: center;
        }

        .payment-method-option {
            flex: 1;
            max-width: 300px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }

        .payment-method-option:hover {
            border-color: #5a4fcf;
            background: rgba(90, 79, 207, 0.1);
        }

        .payment-method-option.active {
            border-color: #5a4fcf;
            background: rgba(90, 79, 207, 0.2);
            box-shadow: 0 0 20px rgba(90, 79, 207, 0.3);
        }

        .payment-method-icon {
            font-size: 3em;
            margin-bottom: 10px;
        }

        .payment-method-title {
            font-size: 1.3em;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .payment-method-description {
            color: #ccc;
            font-size: 0.9em;
        }

        .payment-form {
            background: rgba(255, 255, 255, 0.05);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #ddd;
        }

        .form-input {
            width: 100%;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid #555;
            border-radius: 8px;
            color: white;
            font-size: 1em;
        }

        .form-input:focus {
            outline: none;
            border-color: #5a4fcf;
            background: rgba(255, 255, 255, 0.15);
        }

        .form-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 15px;
        }

        .pay-btn {
            width: 100%;
            border: none;
            padding: 18px;
            border-radius: 10px;
            font-size: 1.3em;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }

        .pay-btn-card {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
        }

        .pay-btn-yape {
            background: linear-gradient(135deg, #752F8B, #9C4AA0);
            color: white;
        }

        .pay-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(39, 174, 96, 0.4);
        }

        .back-btn {
            background: #6c757d;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
        }

        .error-message {
            background: rgba(231, 76, 60, 0.2);
            border: 1px solid #e74c3c;
            color: #e74c3c;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .items-count-badge {
            background: #5a4fcf;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 20px;
        }

        .yape-info {
            background: rgba(117, 47, 139, 0.1);
            border: 1px solid #752F8B;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .yape-steps {
            list-style: none;
            padding: 0;
        }

        .yape-steps li {
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .yape-steps li:last-child {
            border-bottom: none;
        }

        .hidden {
            display: none;
        }

        @media (max-width: 768px) {
            .payment-method-selector {
                flex-direction: column;
            }
            
            .cart-item-payment {
                flex-direction: column;
                text-align: center;
            }
            
            .cart-item-image {
                width: 100%;
                height: 120px;
            }
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="payment-header">
            <h1 class="payment-title">💳 Pagar Carrito</h1>
            <div class="items-count-badge">
                {{ $cartItems->count() }} juego{{ $cartItems->count() != 1 ? 's' : '' }} en tu carrito
            </div>
        </div>

        <a href="{{ route('cart.index') }}" class="back-btn">← Volver al carrito</a>

        <!-- Resumen del Carrito -->
        <div class="cart-summary-section">
            <h3 style="color: #5a4fcf; margin-bottom: 20px; font-size: 1.5em;">🛒 Resumen de tu Compra</h3>
            
            <!-- Lista de Juegos -->
            <div class="cart-items-list">
                @foreach($cartItems as $item)
                    <div class="cart-item-payment">
                        <img src="{{ asset($item->game->image_url) }}" 
                             alt="{{ $item->game->title }}" 
                             class="cart-item-image"
                             onerror="this.src='https://via.placeholder.com/80x50/333/ccc?text=No+Image'">
                        
                        <div class="cart-item-details">
                            <div class="cart-item-title">{{ $item->game->title }}</div>
                            <div class="cart-item-developer">{{ $item->game->developer }}</div>
                        </div>
                        
                        <div class="cart-item-price">S/ {{ number_format($item->price, 2) }}</div>
                    </div>
                @endforeach
            </div>

            <!-- Totales -->
            <div class="payment-totals">
                <div class="total-row">
                    <span>Subtotal ({{ $cartItems->count() }} juego{{ $cartItems->count() != 1 ? 's' : '' }}):</span>
                    <span>S/ {{ number_format($subtotal ?? 0, 2) }}</span>
                </div>
                
                <div class="total-row">
                    <span>IGV (18%):</span>
                    <span>S/ {{ number_format($igv ?? 0, 2) }}</span>
                </div>
                
                <div class="total-row final">
                    <span>TOTAL A PAGAR:</span>
                    <span>S/ {{ number_format($total ?? 0, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Selector de Método de Pago -->
        <div class="payment-method-selector">
            <div class="payment-method-option active" onclick="selectPaymentMethod('card')" id="method-card">
                <div class="payment-method-icon">💳</div>
                <div class="payment-method-title">Tarjeta de Crédito</div>
                <div class="payment-method-description">Visa, Mastercard, AmEx</div>
            </div>
            
            <div class="payment-method-option" onclick="selectPaymentMethod('yape')" id="method-yape">
                <div class="payment-method-icon">📱</div>
                <div class="payment-method-title">Yape</div>
                <div class="payment-method-description">Pago con QR desde tu celular</div>
            </div>
        </div>

        <!-- Mostrar errores -->
        @if($errors->any())
            <div class="error-message">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulario de pago con Tarjeta -->
        <div id="card-payment-form">
            <form action="{{ route('payment.cart.process') }}" method="POST" class="payment-form">
                @csrf

                <div class="form-group">
                    <label class="form-label">💳 Número de Tarjeta</label>
                    <input type="text" name="card_number" class="form-input" 
                           placeholder="1234 5678 9012 3456" 
                           required
                           maxlength="19"
                           value="{{ old('card_number') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">👤 Nombre del Titular</label>
                    <input type="text" name="cardholder_name" class="form-input" 
                           placeholder="JUAN CARLOS PEREZ"
                           required
                           value="{{ old('cardholder_name') }}">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">📅 Fecha de Expiración</label>
                        <input type="text" name="expiry_date" class="form-input" 
                               placeholder="MM/YYYY"
                               required
                               maxlength="7"
                               value="{{ old('expiry_date') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">🔒 CVV</label>
                        <input type="text" name="cvv" class="form-input" 
                               placeholder="123"
                               required
                               maxlength="4"
                               value="{{ old('cvv') }}">
                    </div>
                </div>

                <button type="submit" class="pay-btn pay-btn-card">
                    💳 Pagar con Tarjeta S/ {{ number_format($total ?? 0, 2) }}
                </button>
            </form>

            <!-- Información de tarjetas de prueba -->
            <div style="margin-top: 30px; padding: 20px; background: rgba(255, 255, 255, 0.05); border-radius: 10px;">
                <h4 style="color: #5a4fcf; margin-bottom: 15px;">🧪 Tarjetas de Prueba Disponibles:</h4>
                <p style="color: #ccc; margin-bottom: 10px;"><strong>Interbank:</strong></p>
                <ul style="color: #ddd; margin-left: 20px; margin-bottom: 15px;">
                    <li>4532 1234 5678 9012 - JUAN CARLOS PEREZ - 12/2027 - 123</li>
                    <li>5555 4444 3333 2222 - MARIA LOPEZ GARCIA - 08/2026 - 456</li>
                </ul>
                <p style="color: #ccc; margin-bottom: 10px;"><strong>BCP:</strong></p>
                <ul style="color: #ddd; margin-left: 20px;">
                    <li>4111 1111 1111 1111 - ANA SOFIA MENDOZA - 06/2027 - 321</li>
                    <li>5105 1051 0510 5100 - RICARDO VARGAS SILVA - 11/2026 - 654</li>
                </ul>
            </div>
        </div>

        <!-- Formulario de pago con Yape -->
        <div id="yape-payment-form" class="hidden">
            <div class="payment-form">
                <div class="yape-info">
                    <h3 style="color: #752F8B; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                        📱 Pagar con Yape
                    </h3>
                    <p style="margin-bottom: 15px; color: #ddd;">
                        Genera un código QR para pagar desde tu aplicación Yape de forma rápida y segura.
                    </p>
                    
                    <div style="background: rgba(255, 255, 255, 0.05); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <strong style="color: #752F8B;">Pasos para pagar:</strong>
                        <ol class="yape-steps" style="margin-top: 10px; margin-left: 20px; color: #ccc;">
                            <li>1. Haz clic en "Generar QR de Yape"</li>
                            <li>2. Escanea el QR con tu app Yape</li>
                            <li>3. Confirma el pago en tu celular</li>
                            <li>4. Ingresa el código de transacción aquí</li>
                        </ol>
                    </div>
                </div>

                <button type="button" class="pay-btn pay-btn-yape" onclick="generateYapeQR()">
                    📱 Generar QR de Yape - S/ {{ number_format($total ?? 0, 2) }}
                </button>
            </div>
        </div>
    </div>

    <script>
        let selectedPaymentMethod = 'card';
        
        function selectPaymentMethod(method) {
            selectedPaymentMethod = method;
            
            // Actualizar opciones visuales
            document.querySelectorAll('.payment-method-option').forEach(option => {
                option.classList.remove('active');
            });
            document.getElementById(`method-${method}`).classList.add('active');
            
            // Mostrar/ocultar formularios
            const cardForm = document.getElementById('card-payment-form');
            const yapeForm = document.getElementById('yape-payment-form');
            
            if (method === 'card') {
                cardForm.classList.remove('hidden');
                yapeForm.classList.add('hidden');
            } else if (method === 'yape') {
                cardForm.classList.add('hidden');
                yapeForm.classList.remove('hidden');
            }
        }

        function generateYapeQR() {
            const totalAmount = {{ $total ?? 0 }};
            
            // Mostrar loading
            const button = event.target;
            const originalText = button.textContent;
            button.textContent = '⏳ Generando QR...';
            button.disabled = true;
            
            fetch('{{ route("payment.yape.initiate") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    total_amount: totalAmount
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirigir a la página del QR
                    window.location.href = data.redirect_url;
                } else {
                    throw new Error(data.message || 'Error al generar QR');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al generar el QR de Yape: ' + error.message);
                
                // Restaurar botón
                button.textContent = originalText;
                button.disabled = false;
            });
        }

        // Formatear número de tarjeta
        document.querySelector('input[name="card_number"]').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '').replace(/\D/g, '');
            let formattedValue = value.replace(/(.{4})/g, '$1 ').trim();
            if (formattedValue.length > 19) formattedValue = formattedValue.substr(0, 19);
            e.target.value = formattedValue;
        });

        // Formatear fecha de expiración
        document.querySelector('input[name="expiry_date"]').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 6);
            }
            e.target.value = value;
        });

        // Solo números en CVV
        document.querySelector('input[name="cvv"]').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });

        // Mostrar resumen al cargar
        console.log('Procesando pago para {{ $cartItems->count() }} juego(s)');
        console.log('Total: S/ {{ number_format($total ?? 0, 2) }}');
    </script>
</body>
</html>