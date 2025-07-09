<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pagar con Yape - Nexus</title>
    <!-- Usar API de QR gratuita como respaldo -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js" 
            onload="console.log('QRCode library loaded')" 
            onerror="console.log('Primary QR library failed')"></script>
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

        .yape-container {
            max-width: 600px;
            margin: 0 auto;
            background: rgba(0, 0, 0, 0.8);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .yape-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .yape-title {
            font-size: 2.5em;
            margin-bottom: 10px;
            color: #752F8B;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .order-info {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .order-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .order-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 1.3em;
            color: #752F8B;
        }

        .qr-section {
            text-align: center;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            color: #333;
        }

        .qr-title {
            color: #752F8B;
            font-size: 1.5em;
            font-weight: bold;
            margin-bottom: 20px;
        }

        #qr-code {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .qr-instructions {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #752F8B;
        }

        .qr-steps {
            list-style: none;
            padding: 0;
        }

        .qr-steps li {
            padding: 8px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .step-number {
            background: #752F8B;
            color: white;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9em;
            font-weight: bold;
        }

        .timer-section {
            background: rgba(231, 76, 60, 0.1);
            border: 1px solid #e74c3c;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 30px;
            text-align: center;
        }

        .timer {
            font-size: 1.5em;
            font-weight: bold;
            color: #e74c3c;
        }

        .confirmation-section {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 25px;
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
            text-transform: uppercase;
        }

        .form-input:focus {
            outline: none;
            border-color: #752F8B;
            background: rgba(255, 255, 255, 0.15);
        }

        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin: 5px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #752F8B, #9C4AA0);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(117, 47, 139, 0.4);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .btn-danger {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }

        .status-message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }

        .status-success {
            background: rgba(39, 174, 96, 0.2);
            border: 1px solid #27ae60;
            color: #27ae60;
        }

        .status-error {
            background: rgba(231, 76, 60, 0.2);
            border: 1px solid #e74c3c;
            color: #e74c3c;
        }

        .status-warning {
            background: rgba(243, 156, 18, 0.2);
            border: 1px solid #f39c12;
            color: #f39c12;
        }

        .game-item {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 8px;
            border-left: 3px solid #752F8B;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .hidden {
            display: none;
        }

        @media (max-width: 768px) {
            .yape-container {
                padding: 20px;
            }

            .yape-title {
                font-size: 2em;
                flex-direction: column;
                gap: 10px;
            }

            .order-row {
                flex-direction: column;
                gap: 5px;
            }

            .qr-section {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="yape-container">
        <div class="yape-header">
            <h1 class="yape-title">
                <span>📱</span>
                <span>Pagar con Yape</span>
            </h1>
        </div>

        <!-- Información de la orden -->
        <div class="order-info">
            <h3 style="color: #752F8B; margin-bottom: 15px;">📋 Resumen de tu Orden</h3>
            
            <div class="order-row">
                <span>Orden #:</span>
                <span>{{ $order->order_number }}</span>
            </div>
            
            <div class="order-row">
                <span>Juegos:</span>
                <span>{{ $order->items->count() }} juego{{ $order->items->count() != 1 ? 's' : '' }}</span>
            </div>

            <!-- Lista de juegos -->
            <div style="margin: 15px 0;">
                @foreach($order->items as $item)
                    <div class="game-item">
                        <span>🎮 {{ $item->game_title }}</span>
                        <span>S/ {{ number_format($item->price, 2) }}</span>
                    </div>
                @endforeach
            </div>
            
            <div class="order-row">
                <span>TOTAL A PAGAR:</span>
                <span>S/ {{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>

        <!-- Timer de expiración -->
        <div class="timer-section">
            <p>⏰ Este QR expira en: <span class="timer" id="countdown-timer">15:00</span></p>
        </div>

        <!-- Sección del QR -->
        <div class="qr-section">
            <h3 class="qr-title">📱 Escanea este QR con tu app Yape</h3>
            
            <div id="qr-code"></div>
            
            <div class="qr-instructions">
                <ol class="qr-steps">
                    <li>
                        <span class="step-number">1</span>
                        <span>Abre tu aplicación Yape</span>
                    </li>
                    <li>
                        <span class="step-number">2</span>
                        <span>Toca "Escanear QR" o "Yapear"</span>
                    </li>
                    <li>
                        <span class="step-number">3</span>
                        <span>Escanea este código QR</span>
                    </li>
                    <li>
                        <span class="step-number">5</span>
                        <span>Confirma el pago por S/ {{ number_format($order->total_amount, 2) }}</span>
                    </li>
                    <li>
                        <span class="step-number">6</span>
                        <span>Ingresa el código de transacción aquí abajo</span>
                    </li>
                </ol>
            </div>
        </div>

        <!-- Mensajes de estado -->
        <div id="status-messages"></div>

        <!-- Sección de confirmación -->
        <div class="confirmation-section">
            <h3 style="color: #752F8B; margin-bottom: 15px;">✅ Confirmar Pago</h3>
            <p style="margin-bottom: 20px; color: #ccc;">
                Después de realizar el pago, ingresa el código de transacción que aparece en tu app Yape:
            </p>
            
            <div class="form-group">
                <label class="form-label">🔢 Código de Transacción Yape</label>
                <input type="text" id="transaction-code" class="form-input" 
                       placeholder="Ej: YPE123456789"
                       maxlength="20">
                <small style="color: #ccc; font-size: 0.9em;">
                    Formato: YPE seguido de números (ej: YPE123456789)
                </small>
            </div>

            <div style="text-align: center; margin-top: 20px;">
                <button class="btn btn-primary" onclick="confirmPayment()">
                    ✅ Confirmar Pago
                </button>
                
            </div>
        </div>

        <!-- Botón para volver -->
        <div style="text-align: center;">
            <a href="{{ route('cart.index') }}" class="btn btn-secondary">
                ← Volver al Carrito
            </a>
        </div>
    </div>

    <script>
        const orderId = {{ $order->id }};
        const expiryTime = 15 * 60; // 15 minutos en segundos
        let timeLeft = expiryTime;
        let qrGenerated = false;
        
        // Función de respaldo para cargar QR
        function loadQRBackup() {
            console.log('Cargando QR de respaldo...');
            const script = document.createElement('script');
            script.src = 'https://unpkg.com/qrcode@1.5.3/build/qrcode.min.js';
            script.onload = function() {
                console.log('QR library loaded from backup');
                if (!qrGenerated) {
                    generateQRCode();
                }
            };
            script.onerror = function() {
                console.error('Error loading QR library');
                showQRFallback();
            };
            document.head.appendChild(script);
        }

        // Verificar si QRCode está disponible
        function checkQRAvailable() {
            return typeof QRCode !== 'undefined';
        }

        // Generar QR al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            // Esperar un poco más para que todo cargue
            setTimeout(() => {
                generateQRCode();
            }, 1000);
            
            startCountdown();
            
            // Verificar estado cada 30 segundos
            setInterval(checkStatus, 30000);
        });

        function generateQRCode() {
            if (qrGenerated) return; // Evitar generar múltiples veces
            
            const qrData = @json($yapeData);
            console.log('Intentando generar QR con datos:', qrData);
            
            // Método 1: Intentar con librería QRCode si está disponible
            if (typeof QRCode !== 'undefined') {
                console.log('Usando librería QRCode.js');
                try {
                    QRCode.toCanvas(document.createElement('canvas'), qrData.qr_string, {
                        width: 256,
                        height: 256,
                        margin: 2,
                        color: {
                            dark: '#752F8B',
                            light: '#FFFFFF'
                        },
                        errorCorrectionLevel: 'M'
                    }, function (error, canvas) {
                        if (error) {
                            console.error('Error con QRCode.js:', error);
                            generateQRWithAPI();
                        } else {
                            const qrContainer = document.getElementById('qr-code');
                            qrContainer.innerHTML = '';
                            qrContainer.appendChild(canvas);
                            qrGenerated = true;
                            console.log('QR generado exitosamente con QRCode.js');
                        }
                    });
                } catch (error) {
                    console.error('Exception con QRCode.js:', error);
                    generateQRWithAPI();
                }
            } else {
                console.log('QRCode.js no disponible, usando API');
                generateQRWithAPI();
            }
        }

        // Método 2: Usar API externa para generar QR
        function generateQRWithAPI() {
            const qrData = @json($yapeData);
            const qrContainer = document.getElementById('qr-code');
            
            // Usar qr-server.com API (gratuita)
            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=256x256&color=752F8B&bgcolor=FFFFFF&data=${encodeURIComponent(qrData.qr_string)}`;
            
            console.log('Generando QR con API externa');
            
            const img = new Image();
            img.onload = function() {
                qrContainer.innerHTML = '';
                qrContainer.appendChild(img);
                qrGenerated = true;
                console.log('QR generado exitosamente con API');
            };
            img.onerror = function() {
                console.error('Error cargando QR desde API');
                generateQRWithCanvas();
            };
            img.src = qrUrl;
            img.style.borderRadius = '10px';
            img.alt = 'Código QR para pago Yape';
        }

        // Método 3: Generar QR simple con Canvas (último recurso)
        function generateQRWithCanvas() {
            const qrData = @json($yapeData);
            const qrContainer = document.getElementById('qr-code');
            
            console.log('Generando QR simple con Canvas');
            
            // Crear un QR visual simple
            const canvas = document.createElement('canvas');
            canvas.width = 256;
            canvas.height = 256;
            const ctx = canvas.getContext('2d');
            
            // Fondo blanco
            ctx.fillStyle = '#FFFFFF';
            ctx.fillRect(0, 0, 256, 256);
            
            // Borde púrpura
            ctx.strokeStyle = '#752F8B';
            ctx.lineWidth = 4;
            ctx.strokeRect(2, 2, 252, 252);
            
            // Patrón simple de QR (decorativo)
            ctx.fillStyle = '#752F8B';
            
            // Esquinas
            drawQRCorner(ctx, 20, 20);
            drawQRCorner(ctx, 196, 20);
            drawQRCorner(ctx, 20, 196);
            
            // Patrón central
            for (let i = 0; i < 15; i++) {
                for (let j = 0; j < 15; j++) {
                    if ((i + j) % 3 === 0) {
                        ctx.fillRect(80 + i * 8, 80 + j * 8, 6, 6);
                    }
                }
            }
            
            qrContainer.innerHTML = '';
            qrContainer.appendChild(canvas);
            qrGenerated = true;
            console.log('QR simple generado con Canvas');
        }

        // Función auxiliar para dibujar esquinas del QR
        function drawQRCorner(ctx, x, y) {
            ctx.fillStyle = '#752F8B';
            // Cuadrado exterior
            ctx.fillRect(x, y, 40, 40);
            // Cuadrado interior blanco
            ctx.fillStyle = '#FFFFFF';
            ctx.fillRect(x + 8, y + 8, 24, 24);
            // Cuadrado central púrpura
            ctx.fillStyle = '#752F8B';
            ctx.fillRect(x + 14, y + 14, 12, 12);
        }

        function showQRFallback() {
            const qrData = @json($yapeData);
            const qrContainer = document.getElementById('qr-code');
            
            qrContainer.innerHTML = `
                <div style="padding: 30px; border: 2px dashed #752F8B; border-radius: 10px; text-align: center; background: #f8f9fa;">
                    <h4 style="color: #752F8B; margin-bottom: 15px;">📱 Datos del Pago Yape</h4>
                    <div style="background: #752F8B; color: white; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                        <p style="margin: 5px 0; font-size: 1.2em;"><strong>📞 Número Yape:</strong></p>
                        <p style="margin: 5px 0; font-size: 1.5em; font-weight: bold;">935 592 953</p>
                    </div>
                    <div style="background: white; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                        <p style="margin: 5px 0; color: #333;"><strong>💰 Monto:</strong> S/ ${qrData.amount}</p>
                        <p style="margin: 5px 0; color: #333;"><strong>📝 Referencia:</strong> ${qrData.reference}</p>
                        <p style="margin: 5px 0; color: #333;"><strong>🏪 Comercio:</strong> ${qrData.merchant}</p>
                    </div>
                    <p style="color: #666; font-size: 0.9em; margin-bottom: 15px;">
                        Abre tu app Yape → "Yapear" → Ingresa <strong>935592953</strong> → Envía S/ ${qrData.amount}
                    </p>
                    <button onclick="generateQRCode()" style="margin: 5px; padding: 8px 15px; background: #752F8B; color: white; border: none; border-radius: 5px; cursor: pointer;">
                        🔄 Reintentar QR
                    </button>
                    <button onclick="generateQRWithAPI()" style="margin: 5px; padding: 8px 15px; background: #27ae60; color: white; border: none; border-radius: 5px; cursor: pointer;">
                        🌐 Usar API Externa
                    </button>
                </div>
            `;
            console.log('Mostrando fallback de datos manuales con número Yape');
        }

        function startCountdown() {
            const timer = document.getElementById('countdown-timer');
            
            const countdownInterval = setInterval(() => {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                
                timer.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                
                if (timeLeft <= 0) {
                    clearInterval(countdownInterval);
                    timer.textContent = '00:00';
                    showMessage('⏰ El QR ha expirado. Genera uno nuevo.', 'error');
                    
                    // Deshabilitar botones
                    document.getElementById('transaction-code').disabled = true;
                    document.querySelector('button[onclick="confirmPayment()"]').disabled = true;
                }
                
                timeLeft--;
            }, 1000);
        }

        function confirmPayment() {
            const transactionCode = document.getElementById('transaction-code').value.trim();
            
            if (!transactionCode) {
                showMessage('❌ Por favor ingresa el código de transacción', 'error');
                return;
            }
            
            if (!transactionCode.match(/^YPE\d{6,}$/i)) {
                showMessage('❌ El código debe empezar con YPE seguido de números', 'error');
                return;
            }
            
            // Mostrar loading
            const button = event.target;
            const originalText = button.textContent;
            button.textContent = '⏳ Verificando...';
            button.disabled = true;
            
            fetch(`/pagar/yape/${orderId}/confirmar`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    transaction_code: transactionCode
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('✅ ' + data.message, 'success');
                    
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 2000);
                } else {
                    throw new Error(data.message || 'Error al confirmar el pago');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('❌ ' + error.message, 'error');
                
                // Restaurar botón
                button.textContent = originalText;
                button.disabled = false;
            });
        }

        function showMessage(message, type) {
            const messagesContainer = document.getElementById('status-messages');
            const messageDiv = document.createElement('div');
            messageDiv.className = `status-message status-${type}`;
            messageDiv.textContent = message;
            
            messagesContainer.innerHTML = '';
            messagesContainer.appendChild(messageDiv);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (messageDiv.parentNode) {
                    messageDiv.parentNode.removeChild(messageDiv);
                }
            }, 5000);
        }

        // Solo números y letras en código de transacción
        document.addEventListener('DOMContentLoaded', function() {
            const transactionInput = document.getElementById('transaction-code');
            if (transactionInput) {
                transactionInput.addEventListener('input', function(e) {
                    e.target.value = e.target.value.replace(/[^a-zA-Z0-9]/g, '').toUpperCase();
                });
            }
        });
    </script>
</body>
</html>