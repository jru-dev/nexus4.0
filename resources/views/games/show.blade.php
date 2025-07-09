<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $game->title }} - Nexus</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/game-detail.css') }}">
    <link rel="stylesheet" href="{{ asset('css/game-reviews.css') }}">
</head>
<body>
    <!-- Header Principal - Dos niveles -->
    <header class="main-header">
        <!-- Nivel Superior -->
        <div class="header-top">
            <nav class="main-nav">
                <a href="{{ route('home') }}" class="nav-item active">TIENDA</a>
                @auth
                    <a href="{{ route('library.index') }}" class="nav-item">BIBLIOTECA</a>
                    <a href="{{ route('forum.index') }}" class="nav-item">COMUNIDAD</a>
                    <a href="{{ route('profile.index') }}" class="nav-item">PERFIL</a>
                @else
                    <a href="{{ route('register') }}" class="nav-item">REGISTRARSE</a>
                    <a href="{{ route('forum.index') }}" class="nav-item">COMUNIDAD</a>
                @endauth
            </nav>
            
            <div class="header-top-right">
                @auth
                    <!-- Usuario logueado -->
                    <a href="{{ route('cart.index') }}" class="cart-icon" title="Ver carrito">
                        🛒
                        <span class="cart-count" id="cartCount">
                            {{ auth()->user()->getActiveCart()->getTotalItems() }}
                        </span>
                    </a>
                    
                    <span style="color: #ccc;">{{ auth()->user()->name }}</span>
                    
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="logout-btn" style="background: #e74c3c; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;">
                            Cerrar Sesión
                        </button>
                    </form>
                @else
                    <!-- Usuario visitante -->
                    <a href="{{ route('login') }}" class="login-btn" style="background: #27ae60; color: white; padding: 12px 25px; border-radius: 8px; text-decoration: none; font-weight: bold; transition: all 0.3s; text-transform: uppercase;">
                         Iniciar Sesión
                    </a>
                @endauth
                
                <div class="nexus-logo">
                    <span class="logo-text">NEXUS</span>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="{{ route('home') }}">Inicio</a> / 
            <a href="{{ route('category.show', $game->category->slug) }}">{{ $game->category->name }}</a> / 
            <span>{{ $game->title }}</span>
        </div>

        <!-- Layout Principal Horizontal -->
        <div class="game-main-layout">
            <!-- Lado Izquierdo - Imagen Principal y Screenshots -->
            <div class="game-left-section">
                <!-- Imagen Principal -->
                <div class="game-hero-image">
                    <img src="{{ asset($game->image_url) }}" alt="{{ $game->title }}" class="hero-main-image">
                </div>
                
                <!-- Screenshots Debajo -->
                <div class="screenshots-gallery">
                    @if($game->screenshots && count($game->screenshots) > 0)
                        @foreach($game->screenshots as $screenshot)
                            <div class="screenshot-thumb">
                                <img src="{{ asset($screenshot) }}" alt="Screenshot" class="screenshot-image">
                            </div>
                        @endforeach
                    @else
                        @for($i = 1; $i <= 4; $i++)
                            <div class="screenshot-thumb">
                                <img src="{{ asset($game->image_url) }}" alt="Screenshot {{ $i }}" class="screenshot-image">
                            </div>
                        @endfor
                    @endif
                </div>
            </div>

            <!-- Lado Derecho - Información del Juego -->
            <div class="game-right-section">
                <!-- Título Principal -->
                <h1 class="game-main-title">{{ strtoupper($game->title) }}</h1>
                
                <!-- Descripción -->
                <p class="game-main-description">{{ $game->description }}</p>
                
                <!-- Categoría -->
                <div class="game-category-badge">{{ strtoupper($game->category->name) }}</div>
                
                <!-- Estado de Disponibilidad -->
                <div class="availability-status">YA DISPONIBLE</div>
                
                <!-- Precio del Juego -->
                <div class="game-price-section">
                    <div class="price-display">
                        <span class="current-price">S/ {{ number_format($game->price, 2) }}</span>
                        @if($game->price > 50)
                            <span class="original-price">S/ {{ number_format($game->price * 1.25, 2) }}</span>
                        @endif
                    </div>
                </div>
                
                <!-- Botón de Acción Principal -->
                <div class="main-action-section">
                    @auth
                        @if(auth()->user()->ownsGame($game->id))
                            <a href="{{ route('library.index') }}" class="main-action-btn owned">
                                En tu Biblioteca
                            </a>
                        @else
                            <button class="main-action-btn add-cart" data-game-id="{{ $game->id }}">
                                Agregar al Carrito
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="main-action-btn login-required">
                            Iniciar Sesión para Comprar
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Sección Inferior - Reseñas y Especificaciones lado a lado -->
        <div class="bottom-layout">
            <!-- Lado Izquierdo - Reseñas -->
            <div class="reviews-main-section">
                @auth
                    @if(auth()->user()->ownsGame($game->id))
                        @php
                            $userReview = \App\Models\Review::where('user_id', auth()->id())->where('game_id', $game->id)->first();
                            $canReview = !$userReview;
                        @endphp
                        
                        @if($canReview)
                            <!-- Escribir Reseña - Solo usuarios que tienen el juego -->
                            <div class="write-review-section">
                                <div class="review-input-area">
                                    <!-- Avatar del Usuario -->
                                    <div class="user-avatar-container">
                                        @if(auth()->user()->profile_image)
                                            <img src="{{ asset(auth()->user()->profile_image) }}" 
                                                 alt="{{ auth()->user()->name }}" 
                                                 class="user-avatar">
                                        @else
                                            <div class="user-avatar user-avatar-initial">
                                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="review-input-container">
                                        <div class="reviewer-name-display">
                                            {{ auth()->user()->name }}
                                        </div>
                                        
                                        <!-- Formulario de Reseña -->
                                        <form id="reviewForm">
                                            <input type="hidden" name="game_id" value="{{ $game->id }}">
                                            
                                            <!-- Título de la reseña -->
                                            <input type="text" 
                                                   name="title" 
                                                   placeholder="Título de tu reseña..." 
                                                   class="review-title-input"
                                                   maxlength="255" 
                                                   required>
                                            
                                            <!-- Rating con estrellas -->
                                            <div class="rating-section">
                                                <span class="rating-label">Calificación:</span>
                                                <div class="star-rating">
                                                    <input type="radio" name="rating" value="1" id="star1" required>
                                                    <label for="star1">★</label>
                                                    <input type="radio" name="rating" value="2" id="star2">
                                                    <label for="star2">★</label>
                                                    <input type="radio" name="rating" value="3" id="star3">
                                                    <label for="star3">★</label>
                                                    <input type="radio" name="rating" value="4" id="star4">
                                                    <label for="star4">★</label>
                                                    <input type="radio" name="rating" value="5" id="star5">
                                                    <label for="star5">★</label>
                                                </div>
                                            </div>
                                            
                                            <!-- Contenido de la reseña -->
                                            <textarea name="content" 
                                                      placeholder="Escribe aquí tu reseña sobre {{ $game->title }}..." 
                                                      class="review-textarea"
                                                      rows="4" 
                                                      maxlength="2000" 
                                                      required></textarea>
                                            
                                            <div class="review-actions">
                                                <div class="review-rating-buttons">
                                                    <label class="rating-btn recommended">
                                                        <input type="radio" name="recommendation" value="recommended" required>
                                                        <span>👍 Recomendado</span>
                                                    </label>
                                                    <label class="rating-btn not-recommended">
                                                        <input type="radio" name="recommendation" value="not_recommended">
                                                        <span>👎 No recomendado</span>
                                                    </label>
                                                </div>
                                                
                                                <button type="submit" class="publish-review-btn">
                                                    📝 Publicar Reseña
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Mostrar reseña del usuario si ya existe -->
                            <div class="user-existing-review">
                                <h3>Tu Reseña</h3>
                                <div class="review-card user-review">
                                    <div class="review-status-badge {{ $userReview->recommendation }}">
                                        {{ $userReview->getRecommendationIcon() }} {{ $userReview->getRecommendationText() }}
                                    </div>
                                    <div class="review-content">
                                        <h4 class="review-title">{{ $userReview->title }}</h4>
                                        <div class="review-rating">{{ $userReview->getStarsDisplay() }}</div>
                                        <div class="review-text">{{ $userReview->content }}</div>
                                        <div class="review-meta">
                                            <span class="review-date">{{ $userReview->getTimeAgo() }}</span>
                                            <div class="review-actions-user">
                                                <button class="delete-review-btn" onclick="deleteReview({{ $userReview->id }})">
                                                    🗑️ Eliminar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <!-- Mensaje para usuarios que no tienen el juego -->
                        <div class="write-review-section login-prompt">
                            <div class="login-prompt-icon">🎮</div>
                            <h3 class="login-prompt-title">¿Quieres escribir una reseña?</h3>
                            <p class="login-prompt-text">Necesitas tener este juego en tu biblioteca para poder escribir una reseña</p>
                        </div>
                    @endif
                @else
                    <!-- Mensaje para usuarios no logueados -->
                    <div class="write-review-section login-prompt">
                        <div class="login-prompt-icon">✏️</div>
                        <h3 class="login-prompt-title">¿Quieres escribir una reseña?</h3>
                        <p class="login-prompt-text">Inicia sesión para compartir tu opinión sobre este juego</p>
                        <a href="{{ route('login') }}" class="login-prompt-btn">
                            🔐 Iniciar Sesión
                        </a>
                    </div>
                @endauth

                <!-- Título de Reseñas -->
                <div class="reviews-title-section">
                    <h3>📋 Reseñas de la Comunidad</h3>
                    <div class="reviews-stats" id="reviewsStats">
                        <span class="total-reviews">Cargando...</span>
                        <span class="recommendation-percentage">-</span>
                    </div>
                </div>

                <!-- Lista de Reseñas -->
                <div class="reviews-list" id="reviewsList">
                    <div class="loading-reviews">
                        <p>Cargando reseñas...</p>
                    </div>
                </div>

                <!-- Paginación -->
                <div class="reviews-pagination" id="reviewsPagination" style="display: none;">
                    <!-- Se llenará dinámicamente -->
                </div>
            </div>

            <!-- Lado Derecho - Especificaciones y Más -->
            <div class="sidebar-info">
                

                <!-- Requerimientos del Sistema -->
                <div class="sidebar-section">
                    <h4 class="sidebar-title">Requerimientos del Sistema</h4>
                    <div class="requirements-compact">
                        <div class="req-tabs-compact">
                            <button class="req-tab-compact active" onclick="showRequirementsCompact('minimum')">Mínimos</button>
                            <button class="req-tab-compact" onclick="showRequirementsCompact('recommended')">Recomendados</button>
                        </div>
                        
                        @if($game->system_requirements)
                            <div id="minimum-compact" class="req-content-compact active">
                                @if(isset($game->system_requirements['minimum']))
                                    @foreach($game->system_requirements['minimum'] as $key => $value)
                                        <div class="req-line">
                                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            
                            <div id="recommended-compact" class="req-content-compact">
                                @if(isset($game->system_requirements['recommended']))
                                    @foreach($game->system_requirements['recommended'] as $key => $value)
                                        <div class="req-line">
                                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}
                                        </div>
                                    @endforeach
                                @else
                                    @foreach($game->system_requirements['minimum'] as $key => $value)
                                        <div class="req-line">
                                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }} (Mejorado)
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Juegos Similares -->
                <div class="sidebar-section">
                    <h4 class="sidebar-title">Juegos Similares</h4>
                    <div class="similar-games-list">
                        @if($relatedGames->count() > 0)
                            @foreach($relatedGames->take(3) as $related)
                                <a href="{{ route('game.show', $related->slug) }}" class="similar-game-item">
                                    <img src="{{ asset($related->image_url) }}" alt="{{ $related->title }}" class="similar-game-thumb">
                                    <div class="similar-game-info">
                                        <div class="similar-game-title">{{ $related->title }}</div>
                                        <div class="similar-game-price">S/ {{ number_format($related->price, 2) }}</div>
                                    </div>
                                </a>
                            @endforeach
                        @else
                            <div class="no-similar-games">
                                <p>No hay juegos similares disponibles</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Variables globales
        const gameId = {{ $game->id }};
        let currentPage = 1;

        // Cargar reseñas al inicializar la página
        document.addEventListener('DOMContentLoaded', function() {
            loadReviews();
            loadReviewStats();
        });

        // Cargar reseñas - FUNCIÓN MEJORADA
        async function loadReviews(page = 1) {
            try {
                const response = await fetch(`/api/reviews/game/${gameId}?page=${page}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                console.log('Respuesta del servidor:', data); // Para debug
                
                if (data.success && data.reviews && data.reviews.data) {
                    displayReviews(data.reviews.data);
                    updatePagination(data.reviews);
                } else {
                    // Si no hay éxito pero tampoco error, mostrar mensaje por defecto
                    displayNoReviews();
                }
            } catch (error) {
                console.error('Error al cargar reseñas:', error);
                displayErrorMessage();
            }
        }

        // Mostrar reseñas - FUNCIÓN MEJORADA
        function displayReviews(reviews) {
            const container = document.getElementById('reviewsList');
            
            if (!container) {
                console.error('No se encontró el contenedor de reseñas');
                return;
            }
            
            if (!reviews || reviews.length === 0) {
                displayNoReviews();
                return;
            }

            container.innerHTML = reviews.map(review => `
                <div class="review-card">
                    <div class="review-status-badge ${review.recommendation}">
                        ${review.recommendation === 'recommended' ? '👍 RECOMENDADO' : '👎 NO RECOMENDADO'}
                    </div>
                    <div class="review-content">
                        <h4 class="review-title">${escapeHtml(review.title)}</h4>
                        <div class="review-rating">${getStarsHtml(review.rating)}</div>
                        <div class="review-text">${escapeHtml(review.content)}</div>
                        <div class="reviewer-info-bottom">
  
                            <span class="reviewer-name">${escapeHtml(review.user.name)}</span>
                            <span class="review-date">${formatDate(review.created_at)}</span>
                            ${review.user_id !== (window.currentUserId || null) ? `
                                <button class="helpful-btn" onclick="markHelpful(${review.id})">
                                    👍 Útil (${review.helpful_votes || 0})
                                </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // Mostrar mensaje cuando no hay reseñas
        function displayNoReviews() {
            const container = document.getElementById('reviewsList');
            if (container) {
                container.innerHTML = `
                    <div class="no-reviews">
                        <h4>No hay reseñas aún</h4>
                        <p>Sé el primero en escribir una reseña para este juego</p>
                    </div>
                `;
            }
        }

        // Mostrar mensaje de error
        function displayErrorMessage() {
            const container = document.getElementById('reviewsList');
            if (container) {
                container.innerHTML = `
                    <div class="error-reviews">
                        <h4>Error al cargar reseñas</h4>
                        <p>No se pudieron cargar las reseñas en este momento. Inténtalo más tarde.</p>
                        <button onclick="loadReviews()" class="retry-btn">Reintentar</button>
                    </div>
                `;
            }
        }

        // Cargar estadísticas de reseñas - FUNCIÓN MEJORADA
        async function loadReviewStats() {
            try {
                const response = await fetch(`/api/reviews/game/${gameId}/stats`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                console.log('Estadísticas:', data); // Para debug
                
                const statsElement = document.getElementById('reviewsStats');
                if (statsElement && data.success !== false) {
                    const total = data.total_reviews || 0;
                    const recommendationPercentage = data.recommendation_percentage || 0;
                    
                    statsElement.innerHTML = `
                        <span class="total-reviews">${total} reseña${total !== 1 ? 's' : ''}</span>
                        <span class="recommendation-percentage">${recommendationPercentage}% recomendado</span>
                    `;
                }
            } catch (error) {
                console.error('Error loading stats:', error);
                const statsElement = document.getElementById('reviewsStats');
                if (statsElement) {
                    statsElement.innerHTML = `
                        <span class="total-reviews">0 reseñas</span>
                        <span class="recommendation-percentage">-% recomendado</span>
                    `;
                }
            }
        }

        // Enviar reseña - FUNCIÓN MEJORADA
        const reviewForm = document.getElementById('reviewForm');
        if (reviewForm) {
            reviewForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const submitBtn = this.querySelector('.publish-review-btn');
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Publicando...';
                submitBtn.disabled = true;
                
                const formData = new FormData(this);
                const data = Object.fromEntries(formData);
                
                try {
                    const response = await fetch(`/api/reviews/game/${gameId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(data)
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        showNotification('¡Reseña publicada exitosamente!', 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showNotification(result.message || 'Error al publicar reseña', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showNotification('Error al publicar reseña', 'error');
                } finally {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                }
            });
        }

        // Marcar reseña como útil - FUNCIÓN MEJORADA
        async function markHelpful(reviewId) {
            try {
                const response = await fetch(`/api/reviews/${reviewId}/helpful`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification('Marcado como útil', 'success');
                    loadReviews(currentPage); // Recargar reseñas
                } else {
                    showNotification(result.message || 'Error al procesar la acción', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Error al procesar la acción', 'error');
            }
        }

        // Funciones auxiliares
        function getStarsHtml(rating) {
            let stars = '';
            for (let i = 1; i <= 5; i++) {
                stars += i <= rating ? '★' : '☆';
            }
            return stars;
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function updatePagination(paginationData) {
            const container = document.getElementById('reviewsPagination');
            
            if (!container || !paginationData || paginationData.last_page <= 1) {
                if (container) container.style.display = 'none';
                return;
            }
            
            container.style.display = 'block';
            // Implementar paginación aquí si es necesario
        }

        function showNotification(message, type = 'info') {
            // Crear notificación temporal
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                border-radius: 5px;
                color: white;
                z-index: 10000;
                font-weight: bold;
                ${type === 'success' ? 'background-color: #27ae60;' : 'background-color: #e74c3c;'}
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 3000);
        }

        // Mostrar requerimientos
        function showRequirementsCompact(type) {
            document.querySelectorAll('.req-content-compact').forEach(content => {
                content.classList.remove('active');
            });
            
            document.querySelectorAll('.req-tab-compact').forEach(tab => {
                tab.classList.remove('active');
            });
            
            const targetContent = document.getElementById(type + '-compact');
            if (targetContent) {
                targetContent.classList.add('active');
            }
            
            if (event && event.target) {
                event.target.classList.add('active');
            }
        }

        async function deleteReview(reviewId) {
            // Mostrar modal de confirmación
            showDeleteConfirmation(reviewId);
        }

        // Mostrar modal de confirmación
        function showDeleteConfirmation(reviewId) {
            // Crear modal si no existe
            let modal = document.getElementById('deleteConfirmationModal');
            if (!modal) {
                modal = createDeleteModal();
                document.body.appendChild(modal);
            }
            
            // Configurar el botón de confirmación
            const confirmBtn = modal.querySelector('.confirm-delete-btn');
            const cancelBtn = modal.querySelector('.cancel-delete-btn');
            
            // Remover event listeners previos
            const newConfirmBtn = confirmBtn.cloneNode(true);
            const newCancelBtn = cancelBtn.cloneNode(true);
            confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
            cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);
            
            // Agregar nuevos event listeners
            newConfirmBtn.addEventListener('click', () => {
                confirmDeleteReview(reviewId);
                hideDeleteConfirmation();
            });
            
            newCancelBtn.addEventListener('click', hideDeleteConfirmation);
            
            // Mostrar modal
            modal.classList.add('show');
            
            // Cerrar con ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    hideDeleteConfirmation();
                }
            });
            
            // Cerrar clickeando fuera del modal
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    hideDeleteConfirmation();
                }
            });
        }

        // Crear el modal de confirmación
        function createDeleteModal() {
            const modal = document.createElement('div');
            modal.id = 'deleteConfirmationModal';
            modal.className = 'delete-confirmation-modal';
            modal.innerHTML = `
                <div class="delete-modal-content">
                    <div class="delete-modal-icon">🗑️</div>
                    <h3 class="delete-modal-title">¿Eliminar reseña?</h3>
                    <p class="delete-modal-text">
                        Esta acción no se puede deshacer. Tu reseña será eliminada permanentemente.
                    </p>
                    <div class="delete-modal-actions">
                        <button class="confirm-delete-btn">Sí, eliminar</button>
                        <button class="cancel-delete-btn">Cancelar</button>
                    </div>
                </div>
            `;
            return modal;
        }

        // Ocultar modal de confirmación
        function hideDeleteConfirmation() {
            const modal = document.getElementById('deleteConfirmationModal');
            if (modal) {
                modal.classList.remove('show');
                // Remover event listeners
                document.removeEventListener('keydown', hideDeleteConfirmation);
            }
        }

        // Confirmar eliminación de reseña
        async function confirmDeleteReview(reviewId) {
            try {
                const response = await fetch(`/api/reviews/${reviewId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification('Reseña eliminada exitosamente', 'success');
                    
                    // Recargar la página después de 1.5 segundos
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showNotification(result.message || 'Error al eliminar la reseña', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Error al eliminar la reseña', 'error');
            }
        }



        // Efectos en los botones de rating del formulario
        document.addEventListener('DOMContentLoaded', function() {
            // Rating de estrellas
            const stars = document.querySelectorAll('.star-rating input');
            const labels = document.querySelectorAll('.star-rating label');
            
            stars.forEach((star, index) => {
                star.addEventListener('change', function() {
                    labels.forEach((label, labelIndex) => {
                        label.style.color = labelIndex <= index ? '#ffd700' : '#ccc';
                    });
                });
            });

            // Botones de recomendación
            document.querySelectorAll('.rating-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.rating-btn').forEach(b => b.classList.remove('selected'));
                    this.classList.add('selected');
                });
            });
        });

        // Agregar ID del usuario actual para validaciones
        @auth
            window.currentUserId = {{ auth()->id() }};
        @endauth
    </script>
    <script src="{{ asset('js/cart.js') }}"></script>
</body>
</html>