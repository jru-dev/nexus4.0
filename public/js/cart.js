class NexusCart {
    constructor() {
        this.init();
    }

    init() {
        // Configurar CSRF token para todas las peticiones AJAX
        const token = document.querySelector('meta[name="csrf-token"]');
        if (token) {
            // Configurar token para fetch requests
            this.csrfToken = token.getAttribute('content');
        }

        // Event listeners
        this.setupEventListeners();
        
        // Actualizar contador al cargar página
        this.updateCartCount();
    }

    setupEventListeners() {
        // Botones de agregar al carrito - SELECTOR CORREGIDO
        document.addEventListener('click', (e) => {
            // Verificar múltiples selectores para los botones de carrito
            if (e.target.matches('.add-to-cart-btn') || 
                e.target.matches('.offer-btn.cart') || 
                e.target.matches('[data-game-id]') ||
                e.target.closest('.add-to-cart-btn') ||
                e.target.closest('.offer-btn.cart')) {
                
                e.preventDefault();
                e.stopPropagation();
                
                // Buscar el game-id en el elemento clickeado o su padre
                const button = e.target.closest('[data-game-id]') || e.target;
                const gameId = button.dataset.gameId || button.getAttribute('data-game-id');
                
                console.log('Botón clickeado, Game ID:', gameId); // Debug
                
                if (gameId) {
                    this.addToCart(gameId);
                } else {
                    console.error('No se encontró game-id en el botón clickeado');
                }
            }
        });

        // Botones de eliminar del carrito
        document.addEventListener('click', (e) => {
            if (e.target.matches('.remove-btn') || e.target.closest('.remove-btn')) {
                e.preventDefault();
                const button = e.target.closest('.remove-btn') || e.target;
                const itemId = button.dataset.itemId || button.getAttribute('data-item-id');
                if (itemId) {
                    this.removeFromCart(itemId);
                }
            }
        });

        // Botón limpiar carrito
        document.addEventListener('click', (e) => {
            if (e.target.matches('.clear-cart-btn') || e.target.closest('.clear-cart-btn')) {
                e.preventDefault();
                this.clearCart();
            }
        });
    }

    async addToCart(gameId) {
        try {
            console.log('Agregando al carrito, Game ID:', gameId); // Debug
            
            // Mostrar loading
            this.showLoading('Agregando al carrito...');

            const response = await fetch(`/carrito/agregar/${gameId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            });

            console.log('Response status:', response.status); // Debug

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('Response data:', data); // Debug

            if (data.success) {
                this.showNotification(data.message, 'success');
                this.updateCartCount(data.cart_count);
                
                // Animar el botón
                const button = document.querySelector(`[data-game-id="${gameId}"]`);
                if (button) {
                    this.animateButton(button, 'added');
                }
            } else {
                this.showNotification(data.message || 'Error al agregar al carrito', 'error');
            }

        } catch (error) {
            console.error('Error al agregar al carrito:', error);
            this.showNotification('Error al agregar el juego al carrito', 'error');
        } finally {
            this.hideLoading();
        }
    }

    async removeFromCart(itemId) {
        if (!confirm('¿Estás seguro de que quieres eliminar este juego del carrito?')) {
            return;
        }

        try {
            this.showLoading('Eliminando del carrito...');

            const response = await fetch(`/carrito/eliminar/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification(data.message, 'success');
                this.updateCartCount(data.cart_count);
                
                // Eliminar elemento del DOM si estamos en la página del carrito
                const cartItem = document.getElementById(`cart-item-${itemId}`);
                if (cartItem) {
                    cartItem.style.opacity = '0';
                    cartItem.style.transform = 'translateX(-100%)';
                    setTimeout(() => cartItem.remove(), 300);
                }

                // Si no hay más items, recargar para mostrar estado vacío
                if (data.cart_count === 0) {
                    setTimeout(() => location.reload(), 1000);
                }
            } else {
                this.showNotification(data.message || 'Error al eliminar del carrito', 'error');
            }

        } catch (error) {
            console.error('Error al eliminar del carrito:', error);
            this.showNotification('Error al eliminar el juego del carrito', 'error');
        } finally {
            this.hideLoading();
        }
    }

    async clearCart() {
        if (!confirm('¿Estás seguro de que quieres eliminar todos los juegos del carrito?')) {
            return;
        }

        try {
            this.showLoading('Limpiando carrito...');

            const response = await fetch('/carrito/limpiar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification(data.message, 'success');
                this.updateCartCount(0);
                setTimeout(() => location.reload(), 1000);
            } else {
                this.showNotification(data.message || 'Error al limpiar carrito', 'error');
            }

        } catch (error) {
            console.error('Error al limpiar carrito:', error);
            this.showNotification('Error al limpiar el carrito', 'error');
        } finally {
            this.hideLoading();
        }
    }

    async updateCartCount(count = null) {
        if (count !== null) {
            this.setCartCount(count);
            return;
        }

        try {
            const response = await fetch('/carrito/contador', {
                headers: {
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            
            if (data.success) {
                this.setCartCount(data.cart_count);
            }
        } catch (error) {
            console.error('Error al obtener contador del carrito:', error);
        }
    }

    setCartCount(count) {
        const cartCountElements = document.querySelectorAll('.cart-count, #cartCount');
        cartCountElements.forEach(element => {
            element.textContent = count;
            
            // Animación del contador
            element.style.transform = 'scale(1.3)';
            element.style.color = '#e74c3c';
            setTimeout(() => {
                element.style.transform = 'scale(1)';
                element.style.color = '';
            }, 200);
        });
    }

    animateButton(button, type) {
        const originalText = button.textContent;
        const originalBg = button.style.backgroundColor;

        if (type === 'added') {
            button.textContent = '✓ Agregado';
            button.style.backgroundColor = '#27ae60';
            button.disabled = true;

            setTimeout(() => {
                button.textContent = originalText;
                button.style.backgroundColor = originalBg;
                button.disabled = false;
            }, 2000);
        }
    }

    showNotification(message, type = 'info') {
        // Eliminar notificaciones existentes
        const existingNotifications = document.querySelectorAll('.cart-notification');
        existingNotifications.forEach(n => n.remove());

        const notification = document.createElement('div');
        notification.className = 'cart-notification';
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
            min-width: 300px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            ${type === 'success' ? 'background: linear-gradient(135deg, #27ae60, #2ecc71);' : ''}
            ${type === 'error' ? 'background: linear-gradient(135deg, #e74c3c, #c0392b);' : ''}
            ${type === 'info' ? 'background: linear-gradient(135deg, #3498db, #2980b9);' : ''}
        `;
        
        notification.innerHTML = `
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 1.2em;">
                    ${type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️'}
                </span>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animar entrada
        setTimeout(() => notification.style.transform = 'translateX(0)', 100);
        
        // Auto-eliminar después de 4 segundos
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }, 4000);
    }

    showLoading(message = 'Cargando...') {
        this.hideLoading(); // Asegurar que no hay loading previo
        
        const loading = document.createElement('div');
        loading.id = 'cart-loading';
        loading.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 20000;
            color: white;
            font-size: 1.2em;
            font-weight: bold;
        `;
        
        loading.innerHTML = `
            <div style="text-align: center;">
                <div style="margin-bottom: 15px;">
                    <div style="border: 4px solid #f3f3f3; border-top: 4px solid #5a4fcf; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto;"></div>
                </div>
                <div>${message}</div>
            </div>
            <style>
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            </style>
        `;
        
        document.body.appendChild(loading);
    }

    hideLoading() {
        const loading = document.getElementById('cart-loading');
        if (loading) {
            loading.remove();
        }
    }

    // Método para checkout
    async proceedToCheckout() {
        try {
            this.showLoading('Preparando checkout...');
            
            // Redirigir al carrito para confirmar items
            window.location.href = '/carrito';
            
        } catch (error) {
            console.error('Error en checkout:', error);
            this.showNotification('Error al proceder al checkout', 'error');
        } finally {
            this.hideLoading();
        }
    }
}

// Funciones globales para compatibilidad
window.addToCart = function(gameId) {
    if (window.nexusCart) {
        window.nexusCart.addToCart(gameId);
    } else {
        console.error('NexusCart no está inicializado');
    }
}

window.removeFromCart = function(itemId) {
    if (window.nexusCart) {
        window.nexusCart.removeFromCart(itemId);
    }
}

window.clearCart = function() {
    if (window.nexusCart) {
        window.nexusCart.clearCart();
    }
}

window.proceedToCheckout = function() {
    if (window.nexusCart) {
        window.nexusCart.proceedToCheckout();
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('Inicializando NexusCart...'); // Debug
    window.nexusCart = new NexusCart();
    console.log('NexusCart inicializado:', window.nexusCart); // Debug
});

// Exportar para uso como módulo si es necesario
if (typeof module !== 'undefined' && module.exports) {
    module.exports = NexusCart;
}