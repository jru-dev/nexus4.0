<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tu Carrito de Compras - Nexus</title>
    <link rel="stylesheet" href="{{ asset('css/cart.css') }}">
</head>
<body>
    <!-- Header Principal -->
    <header class="main-header">
        <nav class="nav-menu">
            <a href="{{ route('home') }}" class="nav-item">TIENDA</a>
            <a href="{{ route('library.index') }}" class="nav-item">BIBLIOTECA</a>
            <a href="#" class="nav-item">COMUNIDAD</a>
            <a href="{{ route('profile.index') }}" class="nav-item">PERFIL</a>
        </nav>
        
        <div class="header-right">
            <div class="cart-icon">
                🛒
                <span class="cart-count" id="cartCount">{{ $cartItems->count() ?? 0 }}</span>
            </div>
            <span>{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn">Cerrar Sesión</button>
            </form>
            <div class="nexus-logo">
                <span class="logo-text">NEXUS</span>
            </div>
        </div>
    </header>

    <div class="cart-container">
        <h1 class="cart-title">Tu Carrito de Compras</h1>

        @if(isset($cartItems) && $cartItems->count() > 0)
            <!-- Items del Carrito -->
            <div class="cart-items">
                @foreach($cartItems as $item)
                    <div class="cart-item" id="cart-item-{{ $item->id }}">
                        <img src="{{ asset($item->game->image_url) }}" 
                             alt="{{ $item->game->title }}" 
                             class="game-image"
                             onerror="this.src='https://via.placeholder.com/120x80/333/ccc?text=No+Image'">
                        
                        <div class="game-info">
                            <h3 class="game-title">{{ strtoupper($item->game->title) }}</h3>
                            <p class="game-category">{{ ucfirst($item->game->category->name) }}</p>
                            <div class="game-price">S/ {{ number_format($item->price, 2) }}</div>
                        </div>
                        
                        <button class="remove-btn" onclick="removeFromCart({{ $item->id }})">
                            Eliminar
                        </button>
                    </div>
                @endforeach
            </div>

            <!-- Resumen de Compra CORREGIDO -->
            <div class="cart-summary">
                <h3 class="summary-title">Resumen de Compra</h3>
                
                <!-- Mostrar cantidad de juegos -->
                <div class="summary-row">
                    <span>Productos ({{ $cartItems->count() }} juego{{ $cartItems->count() != 1 ? 's' : '' }}):</span>
                    <span>S/ {{ number_format($subtotal ?? 0, 2) }}</span>
                </div>
                
                <!-- IGV CORREGIDO - Calcular el 18% -->
                <div class="summary-row">
                    <span>IGV (18%):</span>
                    <span>S/ {{ number_format(($subtotal ?? 0) * 0.18, 2) }}</span>
                </div>
                
                <!-- Total CORREGIDO -->
                <div class="summary-row total">
                    <span>Total a Pagar:</span>
                    <span>S/ {{ number_format(($subtotal ?? 0) + (($subtotal ?? 0) * 0.18), 2) }}</span>
                </div>
                
                <!-- BOTÓN CORREGIDO - Redirigir al pago del carrito completo -->
                <button class="checkout-btn" onclick="proceedToCheckout()">
                    🛒 Proceder al Pago ({{ $cartItems->count() }})
                </button>
                
                <button class="checkout-btn" onclick="clearCart()" style="background: #e74c3c; margin-top: 10px;">
                    🗑️ Limpiar Carrito
                </button>
            </div>
        @else
            <!-- Carrito Vacío -->
            <div class="empty-cart">
                <div class="empty-icon">🛒</div>
                <h2>Tu carrito está vacío</h2>
                <p>¡Explora nuestra tienda y agrega algunos juegos increíbles!</p>
                <a href="{{ route('home') }}" class="browse-btn">Explorar Tienda</a>
            </div>
        @endif
    </div>

    <script>
        function removeFromCart(itemId) {
            if (confirm('¿Estás seguro de que quieres eliminar este juego del carrito?')) {
                fetch(`/carrito/eliminar/${itemId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Eliminar el elemento del DOM
                        document.getElementById(`cart-item-${itemId}`).remove();
                        
                        // Actualizar contador
                        updateCartCount(data.cart_count);
                        
                        // Mostrar notificación
                        showNotification(data.message, 'success');
                        
                        // Si no hay más items, recargar página para mostrar estado vacío
                        if (data.cart_count === 0) {
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            // Recalcular totales en tiempo real
                            updateCartTotals();
                        }
                    } else {
                        showNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error al eliminar el juego del carrito', 'error');
                });
            }
        }

        function clearCart() {
            if (confirm('¿Estás seguro de que quieres eliminar todos los juegos del carrito?')) {
                fetch('/carrito/limpiar', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error al limpiar el carrito', 'error');
                });
            }
        }

        // FUNCIÓN CORREGIDA - Proceder al checkout del carrito completo
        function proceedToCheckout() {
            // Verificar que hay items en el carrito
            const cartItems = document.querySelectorAll('.cart-item');
            
            if (cartItems.length === 0) {
                alert('Tu carrito está vacío');
                return;
            }
            
            // CORRECCIÓN: Usar la ruta correcta
            window.location.href = '{{ route("payment.cart.form") }}';
        }

        function updateCartCount(count) {
            const cartCountElement = document.getElementById('cartCount');
            if (cartCountElement) {
                cartCountElement.textContent = count;
                
                // Animación del contador
                cartCountElement.style.transform = 'scale(1.3)';
                setTimeout(() => {
                    cartCountElement.style.transform = 'scale(1)';
                }, 200);
            }
        }

        // NUEVA FUNCIÓN - Actualizar totales en tiempo real
        function updateCartTotals() {
            const cartItems = document.querySelectorAll('.cart-item');
            let subtotal = 0;
            
            cartItems.forEach(item => {
                const priceText = item.querySelector('.game-price').textContent;
                const price = parseFloat(priceText.replace('S/ ', '').replace(',', ''));
                subtotal += price;
            });
            
            const igv = subtotal * 0.18;
            const total = subtotal + igv;
            
            // Actualizar la vista
            const summaryRows = document.querySelectorAll('.summary-row');
            if (summaryRows.length >= 3) {
                summaryRows[0].querySelector('span:last-child').textContent = `S/ ${subtotal.toFixed(2)}`;
                summaryRows[1].querySelector('span:last-child').textContent = `S/ ${igv.toFixed(2)}`;
                summaryRows[2].querySelector('span:last-child').textContent = `S/ ${total.toFixed(2)}`;
            }
            
            // Actualizar contador en el botón
            const remainingItems = cartItems.length;
            const checkoutBtn = document.querySelector('.checkout-btn');
            if (checkoutBtn && remainingItems > 0) {
                checkoutBtn.textContent = `🛒 Proceder al Pago (${remainingItems} juego${remainingItems != 1 ? 's' : ''})`;
            }
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                border-radius: 8px;
                color: white;
                font-weight: bold;
                z-index: 10000;
                transform: translateX(100%);
                transition: transform 0.3s ease;
                ${type === 'success' ? 'background: #27ae60;' : 'background: #e74c3c;'}
            `;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => notification.style.transform = 'translateX(0)', 100);
            
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => document.body.removeChild(notification), 300);
            }, 3000);
        }
    </script>
</body>
</html>