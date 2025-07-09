<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Nexus - Tienda de Videojuegos</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
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
        <!-- Sección de Bienvenida -->
        <div class="welcome-section">
            <h1>BIENVENIDO A NEXUS, {{ auth()->check() ? strtoupper(auth()->user()->name) : 'VISITANTE' }}</h1>
        </div>

        <!-- Filtros de Categorías + Buscador en la misma línea -->
        <div class="category-search-section">
            <div class="category-filters">
                <a href="{{ route('home') }}" class="category-btn active">TODOS</a>
                @if(isset($categories) && $categories->count() > 0)
                    @foreach($categories as $category)
                        <a href="{{ route('category.show', $category->slug) }}" class="category-btn">{{ strtoupper($category->name) }}</a>
                    @endforeach
                @else
                    <a href="/categoria/accion" class="category-btn">ACCIÓN</a>
                    <a href="/categoria/terror" class="category-btn">TERROR</a>
                    <a href="/categoria/supervivencia" class="category-btn">SUPERVIVENCIA</a>
                    <a href="/categoria/aventura" class="category-btn">AVENTURA</a>
                    <a href="/categoria/estrategia" class="category-btn">ESTRATEGIA</a>
                @endif
            </div>
            
            <!-- Buscador al lado de las categorías -->
            <div class="header-search">
                <form action="{{ route('search') }}" method="GET" class="search-form-header">
                    <input type="text" 
                        name="q" 
                        class="search-input-header" 
                        placeholder="BUSCAR"
                        autocomplete="off">
                    <button type="submit" class="search-btn-header">🔍</button>
                </form>
            </div>
        </div>

        <!-- Hero + Carrusel -->
        <div class="hero-carousel-section">
            <!-- Juegos Destacados (Navegables) -->
            <div class="hero-game">
                @if(isset($carouselGames) && $carouselGames->count() > 0)
                    @foreach($carouselGames->take(5) as $index => $game)
                        <div class="hero-slide {{ $index === 0 ? 'active' : '' }}" data-slide="{{ $index }}">
                            <!-- HACER CLICKEABLE LA IMAGEN COMPLETA -->
                            <a href="{{ route('game.show', $game->slug) }}" class="hero-link">
                                <img src="{{ asset($game->image_url) }}" alt="{{ $game->title }}" class="hero-image">
                            </a>
                        </div>
                    @endforeach
                @else
                    @for($i = 0; $i < 5; $i++)
                        <div class="hero-slide {{ $i === 0 ? 'active' : '' }}" data-slide="{{ $i }}">
                            <a href="#" class="hero-link">
                                <img src="https://picsum.photos/800/400?random={{ $i + 20 }}" alt="Juego Destacado {{ $i + 1 }}" class="hero-image">
                                <div class="hero-content">
                                    <h2 class="hero-title">JUEGO DESTACADO {{ $i + 1 }}</h2>
                                    <p class="hero-description">Descubre los mejores juegos en Nexus</p>
                                    <span class="availability-badge">YA DISPONIBLE</span>
                                </div>
                            </a>
                        </div>
                    @endfor
                @endif

                <!-- Navegación del Hero -->
                <button class="hero-nav hero-prev" onclick="changeHeroSlide(-1)">‹</button>
                <button class="hero-nav hero-next" onclick="changeHeroSlide(1)">›</button>

                <!-- Dots del Hero -->
                <div class="hero-dots" id="heroDots">
                    @for($i = 0; $i < 5; $i++)
                        <span class="hero-dot {{ $i === 0 ? 'active' : '' }}" onclick="currentHeroSlide({{ $i }})"></span>
                    @endfor
                </div>
            </div>

            <!-- Sección Lateral MODIFICADA -->
            <div class="carousel-section">
                <h3 class="game-title-header" id="currentGameTitle">
                    @if(isset($carouselGames) && $carouselGames->count() > 0)
                        {{ strtoupper($carouselGames->first()->title) }}
                    @else
                        JUEGO DESTACADO 1
                    @endif
                </h3>
                
                <div class="carousel-images" id="currentGameScreenshots">
                    @if(isset($carouselGames) && $carouselGames->count() > 0)
                        @foreach($carouselGames->first()->screenshots as $screenshot)
                            <div class="carousel-item">
                                <img src="{{ asset($screenshot) }}" alt="Screenshot" class="carousel-image">
                            </div>
                        @endforeach
                    @else
                        @for($i = 1; $i <= 4; $i++)
                            <div class="carousel-item">
                                <img src="https://picsum.photos/200/100?random={{ $i + 30 }}" alt="Screenshot {{ $i }}" class="carousel-image">
                            </div>
                        @endfor
                    @endif
                </div>
                
                <div class="game-info">
                    <div class="availability-text">YA DISPONIBLE</div>
                    <div class="game-price" id="currentGamePrice">
                        @if(isset($carouselGames) && $carouselGames->count() > 0)
                            S/ {{ number_format($carouselGames->first()->price, 2) }}
                        @else
                            S/ 59.99
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Ofertas Especiales - SECCIÓN CORREGIDA -->
        <section class="special-offers">
            <h2 class="section-title">OFERTAS ESPECIALES</h2>
            <div class="offers-grid" id="gamesContainer">
                @if(isset($specialOffers) && $specialOffers->count() > 0)
                    @foreach($specialOffers as $offer)
                        <div class="offer-card">
                            <span class="discount-badge">-{{ rand(20, 50) }}%</span>
                            <a href="{{ route('game.show', $offer->slug) }}">
                                <img src="{{ asset($offer->image_url) }}" alt="{{ $offer->title }}" class="offer-image">
                            </a>
                            <div class="offer-content">
                                <h3>{{ $offer->title }}</h3>
                                <div class="price-section">
                                    <span class="original-price">S/ {{ number_format($offer->price * 1.33, 2) }}</span>
                                    <span class="current-price">S/ {{ number_format($offer->price, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    @for($i = 1; $i <= 3; $i++)
                        <div class="offer-card">
                            <span class="discount-badge">-{{ rand(20, 50) }}%</span>
                            <img src="https://picsum.photos/300/200?random={{ $i + 10 }}" alt="Oferta {{ $i }}" class="offer-image">
                            <div class="offer-content">
                                <h3>Juego en Oferta {{ $i }}</h3>
                                <div class="price-section">
                                    <span class="original-price">S/ 59.99</span>
                                    <span class="current-price">S/ 39.99</span>
                                </div>
                                <div class="offer-actions" style="margin-top: 15px;">
                                    <a href="{{ route('login') }}" class="offer-btn login">
                                        🔐 Iniciar Sesión
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endfor
                @endif
            </div>
        </section>

        <!-- Footer con Noticias y Reseñas -->
        <div class="footer-section">
          <!-- Noticias y actualizaciones -->
            <div class="news-section">
                <h2>Noticias y actualizaciones</h2>
                <br>        
                @forelse($news as $item)
                    <div class="news-item" style="margin-bottom: 24px; background: #222; padding: 16px; border-radius: 8px;">
                        <div class="news-date" style="color: #aaa; font-size: 0.95em;">
                            {{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}
                        </div>
                        <h3 style="margin: 8px 0 4px 0;">{{ $item->title }}</h3>
                        <p>{{ $item->content }}</p>
                        @if($item->image)
                            <img src="{{ asset($item->image) }}" alt="Imagen noticia" style="max-width:200px; margin-top:8px; border-radius:6px;">
                        @endif
                    </div>
                @empty
                    <div class="news-item">
                        <p>No hay noticias disponibles.</p>
                    </div>
                @endforelse
            </div>

            <div class="reviews-section">
                <h3 class="section-title">Reseñas rápidas</h3>
                <div class="review-item">
                    <div class="review-date">Subido hace 2 horas</div>
                    <h4>Cyberpunk 2077: Phantom Liberty</h4>
                    <p>Una expansión que redefine el juego base con una historia cautivadora.</p>
                    </div>
                <div class="review-item">
                    <div class="review-date">Subido hace 5 horas</div>
                    <h4>The Witcher 3: Wild Hunt</h4>
                    <p>Sigue siendo el mejor RPG de mundo abierto años después.</p>
                </div>
                <div class="review-item">
                    <div class="review-date">Subido hace 1 día</div>
                    <h4>Resident Evil 4 Remake</h4>
                    <p>Un remake perfecto que respeta el original mientras lo moderniza.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/cart.js') }}"></script>
    <script>
        let currentHeroIndex = 0;
        const totalHeroSlides = 5;
        
        // Datos de los juegos desde la base de datos
        const gameData = [
            @if(isset($carouselGames) && $carouselGames->count() > 0)
                @foreach($carouselGames->take(5) as $index => $game)
                {
                    title: "{{ strtoupper($game->title) }}",
                    price: "{{ number_format($game->price, 2) }}",
                    screenshots: [
                        @if($game->screenshots && count($game->screenshots) > 0)
                            @foreach(array_slice($game->screenshots, 0, 4) as $screenshot)
                                "{{ asset($screenshot) }}",
                            @endforeach
                        @else
                            "{{ asset($game->image_url) }}",
                            "{{ asset($game->image_url) }}",
                            "{{ asset($game->image_url) }}",
                            "{{ asset($game->image_url) }}"
                        @endif
                    ]
                }{{ $loop->last ? '' : ',' }}
                @endforeach
            @endif
        ];

        function showHeroSlide(index) {
            // Ocultar todos los slides
            document.querySelectorAll('.hero-slide').forEach(slide => {
                slide.classList.remove('active');
            });
            
            // Mostrar el slide actual
            const currentSlide = document.querySelector(`.hero-slide[data-slide="${index}"]`);
            if (currentSlide) {
                currentSlide.classList.add('active');
            }
            
            // Actualizar dots
            document.querySelectorAll('.hero-dot').forEach((dot, i) => {
                dot.classList.toggle('active', i === index);
            });
            
            // Actualizar información lateral
            updateSidePanel(index);
        }

        function updateSidePanel(index) {
            const game = gameData[index];
            if (!game) return;
            
            // Actualizar título
            document.getElementById('currentGameTitle').textContent = game.title;
            
            // Actualizar precio
            document.getElementById('currentGamePrice').textContent = `S/ ${game.price}`;
            
            // Actualizar screenshots
            const screenshotsContainer = document.getElementById('currentGameScreenshots');
            screenshotsContainer.innerHTML = '';
            
            game.screenshots.slice(0, 4).forEach(screenshot => {
                const itemDiv = document.createElement('div');
                itemDiv.className = 'carousel-item';
                itemDiv.innerHTML = `<img src="${screenshot}" alt="Screenshot" class="carousel-image">`;
                screenshotsContainer.appendChild(itemDiv);
            });
        }

        function changeHeroSlide(direction) {
            currentHeroIndex = (currentHeroIndex + direction + totalHeroSlides) % totalHeroSlides;
            showHeroSlide(currentHeroIndex);
        }

        function currentHeroSlide(index) {
            currentHeroIndex = index;
            showHeroSlide(currentHeroIndex);
        }

        // Auto-slide cada 8 segundos
        setInterval(() => {
            changeHeroSlide(1);
        }, 8000);

        // Inicializar
        document.addEventListener('DOMContentLoaded', function() {
            showHeroSlide(0);
        });
    </script>
</body>
</html>