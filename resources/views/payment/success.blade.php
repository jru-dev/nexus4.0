<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compra Exitosa - Nexus</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .success-container {
            max-width: 600px;
            width: 100%;
            background: rgba(0, 0, 0, 0.8);
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .success-icon {
            font-size: 5em;
            margin-bottom: 20px;
            animation: bounce 1s ease-in-out;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-30px);
            }
            60% {
                transform: translateY(-15px);
            }
        }

        .success-title {
            font-size: 2.5em;
            margin-bottom: 20px;
            color: #27ae60;
            font-weight: bold;
        }

        .success-message {
            font-size: 1.3em;
            margin-bottom: 30px;
            color: #ccc;
            line-height: 1.5;
        }

        .order-details {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .order-title {
            color: #5a4fcf;
            font-size: 1.5em;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .order-info {
            display: grid;
            gap: 15px;
            text-align: left;
        }

        .order-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .order-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 1.2em;
            color: #27ae60;
        }

        .order-label {
            color: #ccc;
        }

        .order-value {
            color: white;
            font-weight: 600;
        }

        .game-item {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            border-left: 3px solid #5a4fcf;
        }

        .game-title {
            font-size: 1.2em;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .game-price {
            color: #27ae60;
            font-weight: bold;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
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
            min-width: 200px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #5a4fcf, #4a3fcf);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(90, 79, 207, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #27ae60, #219a52);
            color: white;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.4);
        }

        .btn-tertiary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn-tertiary:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .confetti {
            position: absolute;
            width: 10px;
            height: 10px;
            background: #5a4fcf;
            animation: confetti-fall 3s linear infinite;
        }

        @keyframes confetti-fall {
            0% {
                transform: translateY(-100vh) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }

        .nexus-logo {
            position: absolute;
            top: 20px;
            left: 20px;
            background: linear-gradient(45deg, #e74c3c, #c0392b);
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: bold;
            color: white;
            text-decoration: none;
            font-size: 1.2em;
        }

        @media (max-width: 768px) {
            .success-container {
                padding: 30px 20px;
            }

            .success-title {
                font-size: 2em;
            }

            .success-message {
                font-size: 1.1em;
            }

            .action-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                max-width: 300px;
            }

            .order-row {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <!-- Logo de Nexus -->
    <a href="{{ route('home') }}" class="nexus-logo">NEXUS</a>

    <div class="success-container">
        <!-- Icono de éxito -->
        <div class="success-icon">🎉</div>
        
        <!-- Título -->
        <h1 class="success-title">¡COMPRA EXITOSA!</h1>
        
        <!-- Mensaje -->
        <p class="success-message">
            Tu compra se ha procesado correctamente. Los juegos ya están disponibles en tu biblioteca.
        </p>

        <!-- Detalles de la orden -->
        <div class="order-details">
            <h2 class="order-title">📋 Detalles de tu Compra</h2>
            
            <div class="order-info">
                <div class="order-row">
                    <span class="order-label">Número de Orden:</span>
                    <span class="order-value">#{{ $order->order_number }}</span>
                </div>
                
                <div class="order-row">
                    <span class="order-label">Fecha:</span>
                    <span class="order-value">{{ $order->completed_at->format('d/m/Y H:i') }}</span>
                </div>
                
                <div class="order-row">
                    <span class="order-label">Método de Pago:</span>
                    <span class="order-value">
                        {{ $order->payment_details['card_type'] ?? 'Tarjeta' }} 
                        **** {{ $order->payment_details['last_four'] ?? 'XXXX' }}
                    </span>
                </div>
                
                <!-- Juegos comprados -->
                <div style="margin: 20px 0;">
                    <strong style="color: #5a4fcf;">Juegos Adquiridos:</strong>
                    @foreach($order->items as $item)
                        <div class="game-item">
                            <div class="game-title">🎮 {{ $item->game_title }}</div>
                            <div class="game-price">S/ {{ number_format($item->price, 2) }}</div>
                        </div>
                    @endforeach
                </div>
                
                <div class="order-row">
                    <span class="order-label">TOTAL PAGADO:</span>
                    <span class="order-value">S/ {{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="action-buttons">
            <a href="{{ route('library.index') }}" class="btn btn-primary">
                📚 Ver mi Biblioteca
            </a>
            
            <a href="{{ route('home') }}" class="btn btn-secondary">
                🛒 Seguir Comprando
            </a>
            
            <a href="{{ route('profile.index') }}" class="btn btn-tertiary">
                👤 Mi Perfil
            </a>
        </div>
    </div>

    <!-- Efecto de confetti -->
    <script>
        // Crear efecto de confetti
        function createConfetti() {
            const colors = ['#5a4fcf', '#27ae60', '#e74c3c', '#f39c12', '#9b59b6'];
            
            for (let i = 0; i < 50; i++) {
                setTimeout(() => {
                    const confetti = document.createElement('div');
                    confetti.className = 'confetti';
                    confetti.style.left = Math.random() * 100 + 'vw';
                    confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                    confetti.style.animationDelay = Math.random() * 3 + 's';
                    confetti.style.animationDuration = (Math.random() * 3 + 2) + 's';
                    
                    document.body.appendChild(confetti);
                    
                    // Eliminar después de la animación
                    setTimeout(() => {
                        confetti.remove();
                    }, 5000);
                }, i * 100);
            }
        }

        // Iniciar confetti al cargar
        document.addEventListener('DOMContentLoaded', function() {
            createConfetti();
            
            // Repetir confetti cada 10 segundos
            setInterval(createConfetti, 10000);
        });

        // Sonido de éxito (opcional)
        try {
            const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+Tyt2QdBjqU2fHL');
            audio.volume = 0.3;
            audio.play().catch(() => {}); // Ignorar errores de autoplay
        } catch (e) {
            // Navegador no soporta audio
        }
    </script>
</body>
</html>