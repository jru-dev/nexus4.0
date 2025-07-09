<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $category->name }} - Nexus</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/category.css') }}">
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
            <a href="{{ route('home') }}">Inicio</a> / <span>{{ $category->name }}</span>
        </div>

        <!-- Header de Categoría -->
        <div class="category-header">
            <h1>{{ strtoupper($category->name) }}</h1>
            <p>Descubre los mejores juegos de {{ strtolower($category->name) }}</p>
        </div>

        <!-- Filtros de Categorías -->
        <div class="category-filters">
            <a href="{{ route('home') }}" class="category-btn">TODOS</a>
            @foreach($categories as $cat)
                <a href="{{ route('category.show', $cat->slug) }}" 
                   class="category-btn {{ $cat->id == $category->id ? 'active' : '' }}">
                    {{ strtoupper($cat->name) }}
                </a>
            @endforeach
        </div>

        <!-- Información de resultados -->
        <div class="results-info">
            <div class="results-count">
                {{ $games->count() }} {{ $games->count() == 1 ? 'juego encontrado' : 'juegos encontrados' }}
            </div>
                <div class="sort-options">
                    <span>Ordenar por:</span>
                    <a href="{{ route('category.show', ['slug' => $category->slug, 'sort' => 'popular']) }}" 
                    class="sort-btn {{ ($sort ?? 'popular') == 'popular' ? 'active' : '' }}">
                        Populares
                    </a>
                    <a href="{{ route('category.show', ['slug' => $category->slug, 'sort' => 'price_asc']) }}" 
                    class="sort-btn {{ ($sort ?? '') == 'price_asc' ? 'active' : '' }}">
                        Precio: Menor a Mayor
                    </a>
                    <a href="{{ route('category.show', ['slug' => $category->slug, 'sort' => 'price_desc']) }}" 
                    class="sort-btn {{ ($sort ?? '') == 'price_desc' ? 'active' : '' }}">
                        Precio: Mayor a Menor
                    </a>
                    <a href="{{ route('category.show', ['slug' => $category->slug, 'sort' => 'newest']) }}" 
                    class="sort-btn {{ ($sort ?? '') == 'newest' ? 'active' : '' }}">
                        Más Recientes
                    </a>
                </div>
        </div>

        <!-- Grid de Juegos -->
        @if($games->count() > 0)
            <div class="games-grid">
                @foreach($games as $game)
                    <div class="game-card" onclick="window.location.href='{{ route('game.show', $game->slug) }}'">
                        <img src="{{ asset($game->image_url) }}" 
                             alt="{{ $game->title }}" 
                             class="game-image"
                             onerror="this.src='https://via.placeholder.com/300x200/333/ccc?text=No+Image'">
                        
                        <div class="game-content">
                            <h3 class="game-title">{{ $game->title }}</h3>
                            <p class="game-description">{{ $game->description }}</p>
                            
                            <div class="game-meta">
                                <span class="game-developer">{{ $game->developer }}</span>
                                <span class="game-rating">{{ $game->age_rating }}</span>
                            </div>
                            
                            <div class="game-price-section">
                                <!-- Para usuarios autenticados -->
                                @auth
                                    @if(auth()->user()->ownsGame($game->id))
                                        <a href="{{ route('library.index') }}" class="action-btn owned">
                                            En tu Biblioteca
                                        </a>
                                    @else
                                        <!-- BOTÓN CORREGIDO con data-game-id -->
                                        <button class="action-btn cart" data-game-id="{{ $game->id }}">
                                            🛒 Agregar al Carrito
                                        </button>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="action-btn login">
                                        Iniciar Sesión
                                    </a>
                                @endauth
                                
                                <div class="price-info">
                                    <span class="current-price">S/ {{ number_format($game->price, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="no-games">
                <h3>No hay juegos en esta categoría</h3>
                <p>Parece que aún no tenemos juegos disponibles en {{ strtolower($category->name) }}.</p>
                <a href="{{ route('home') }}" class="category-btn">Ver todos los juegos</a>
            </div>
        @endif
    </div>

    <script>
        function addToCart(gameId) {
            alert('Función de carrito en desarrollo para el juego ID: ' + gameId);
        }
    </script>
        <script>
            // Efecto de carga suave al cambiar ordenamiento
            document.querySelectorAll('.sort-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelector('.games-grid').classList.add('loading');
                });
            });

            // Mostrar contador actualizado
            const gamesCount = {{ $games->count() }};
            const sortType = '{{ $sort ?? "popular" }}';

            console.log(`Mostrando ${gamesCount} juegos ordenados por: ${sortType}`);
        </script>
        <script src="{{ asset('js/cart.js') }}"></script>
</body>
</html>