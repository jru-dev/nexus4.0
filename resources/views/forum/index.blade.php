<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Comunidad - Nexus</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <style>
        /* ===== HEADER DE DOS NIVELES (copiado de home.css) ===== */
        .main-header {
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-top {
            background: rgba(0, 0, 0, 0.9);
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #444;
        }

        .main-nav {
            display: flex;
            gap: 30px;
        }

        .nav-item {
            color: #ccc;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            transition: all 0.3s;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.95em;
        }

        .nav-item:hover, .nav-item.active {
            background: #5a4fcf;
            color: white;
        }

        .header-top-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .cart-icon {
            position: relative;
            color: #ccc;
            font-size: 20px;
            cursor: pointer;
            transition: color 0.3s;
        }

        .cart-icon:hover {
            color: white;
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .nexus-logo {
            background: linear-gradient(45deg, #e74c3c, #c0392b);
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: bold;
        }

        .logo-text {
            color: white;
            font-size: 1.2em;
        }

        .header-bottom {
            background: rgba(0, 0, 0, 0.8);
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #333;
        }

        .category-nav {
            display: flex;
            align-items: center;
        }

        .category-btn {
            color: #ccc;
            text-decoration: none;
            padding: 15px 25px;
            transition: all 0.3s;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9em;
            letter-spacing: 0.5px;
            border-bottom: 3px solid transparent;
        }

        .category-btn:hover, .category-btn.active {
            background: rgba(90, 79, 207, 0.3);
            color: white;
            border-bottom-color: #5a4fcf;
        }

        .header-search {
            position: relative;
        }

        .search-form-header {
            display: flex;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s;
        }

        .search-form-header:focus-within {
            background: rgba(255, 255, 255, 0.2);
            border-color: #5a4fcf;
        }

        .search-input-header {
            background: transparent;
            border: none;
            color: white;
            padding: 10px 15px;
            font-size: 0.9em;
            width: 180px;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .search-input-header::placeholder {
            color: rgba(255, 255, 255, 0.7);
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .search-input-header:focus {
            outline: none;
            width: 220px;
            transition: width 0.3s;
        }

        .search-btn-header {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 10px 12px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1em;
        }

        .search-btn-header:hover {
            background: #5a4fcf;
        }

        /* Estilos específicos para el foro */
        .forum-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .forum-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: rgba(0, 0, 0, 0.6);
            padding: 25px;
            border-radius: 15px;
        }

        .forum-title {
            font-size: 2.5em;
            color: #5a4fcf;
            font-weight: bold;
        }

        .create-post-btn {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
            text-transform: uppercase;
            font-size: 1em;
        }

        .create-post-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.4);
        }

        .forum-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(0, 0, 0, 0.4);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #5a4fcf;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #ccc;
            font-size: 0.9em;
        }

        .posts-section {
            background: rgba(0, 0, 0, 0.4);
            border-radius: 15px;
            padding: 25px;
        }

        .section-title {
            color: white;
            font-size: 1.5em;
            margin-bottom: 20px;
            border-bottom: 2px solid #5a4fcf;
            padding-bottom: 10px;
        }

        .post-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .post-card:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
            border-color: #5a4fcf;
        }

        .post-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .post-title {
            font-size: 1.3em;
            font-weight: bold;
            color: white;
            text-decoration: none;
            margin-bottom: 8px;
            display: block;
        }

        .post-title:hover {
            color: #5a4fcf;
        }

        .post-author {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #ccc;
            font-size: 0.9em;
        }

        .author-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, #5a4fcf, #4a3fcf);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 0.8em;
        }

        .post-meta {
            display: flex;
            gap: 15px;
            color: #999;
            font-size: 0.85em;
        }

        .post-content {
            color: #ddd;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .post-image {
            max-width: 200px;
            border-radius: 8px;
            margin-top: 10px;
        }

        .post-stats {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .post-interactions {
            display: flex;
            gap: 15px;
        }

        .interaction-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 8px 15px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9em;
            transition: all 0.3s;
        }

        .interaction-btn:hover {
            background: #5a4fcf;
            border-color: #5a4fcf;
        }

        .last-activity {
            color: #999;
            font-size: 0.85em;
        }

        .empty-forum {
            text-align: center;
            padding: 60px 20px;
            color: #ccc;
        }

        .empty-icon {
            font-size: 4em;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .pagination-wrapper {
            margin-top: 30px;
            display: flex;
            justify-content: center;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .forum-header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }

            .forum-title {
                font-size: 2em;
            }

            .post-header {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }

            .post-stats {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .post-interactions {
                width: 100%;
                justify-content: space-between;
            }
        }
    </style>
</head>
<body>
    <!-- Header Principal - Dos niveles -->
    <header class="main-header">
        <!-- Nivel Superior -->
        <div class="header-top">
            <nav class="main-nav">
                <a href="{{ route('home') }}" class="nav-item">TIENDA</a>
                @auth
                    <a href="{{ route('library.index') }}" class="nav-item">BIBLIOTECA</a>
                    <a href="{{ route('forum.index') }}" class="nav-item active">COMUNIDAD</a>
                    <a href="{{ route('profile.index') }}" class="nav-item">PERFIL</a>
                @else
                    <a href="{{ route('register') }}" class="nav-item">REGISTRARSE</a>
                    <a href="{{ route('forum.index') }}" class="nav-item active">COMUNIDAD</a>
                @endauth
            </nav>
            
            <div class="header-top-right">
 
                
                <div class="nexus-logo">
                    <span class="logo-text">NEXUS</span>
                </div>
            </div>
        </div>

    </header>

    <div class="forum-container">
        <!-- Mensajes de éxito/error -->
        @if(session('success'))
            <div style="background: rgba(39, 174, 96, 0.2); border: 1px solid #27ae60; color: #27ae60; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background: rgba(231, 76, 60, 0.2); border: 1px solid #e74c3c; color: #e74c3c; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                {{ session('error') }}
            </div>
        @endif

        <!-- Header del Foro -->
        <div class="forum-header">
            <div>
                <h1 class="forum-title">💬 COMUNIDAD NEXUS</h1>
                <p style="color: #ccc; margin: 0;">Comparte, discute y conecta con otros gamers</p>
            </div>
            @auth
                <a href="{{ route('forum.create') }}" class="create-post-btn">
                    ✏️ Crear Post
                </a>
            @else
                <a href="{{ route('login') }}" class="create-post-btn">
                    🔐 Iniciar Sesión
                </a>
            @endauth
        </div>

        <!-- Lista de Posts -->
        <div class="posts-section">
            <h2 class="section-title"> Discusiones Recientes</h2>

            @if($posts->count() > 0)
                @foreach($posts as $post)
                    <div class="post-card">
                        <div class="post-header">
                            <div>
                                <a href="{{ route('forum.show', $post->id) }}" class="post-title">
                                    {{ $post->title }}
                                </a>
                                <div class="post-author">
                                    @if($post->user->profile_image)
                                        <img src="{{ asset($post->user->profile_image) }}" 
                                             alt="{{ $post->user->name }}" 
                                             class="author-avatar">
                                    @else
                                        <div class="author-avatar">
                                            {{ strtoupper(substr($post->user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <span>{{ $post->user->name }}</span>
                                    <span>•</span>
                                    <span>{{ $post->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div class="post-meta">
                                <span>👁️ {{ $post->views }}</span>
                                <span>💬 {{ $post->replies_count }}</span>
                            </div>
                        </div>

                        <div class="post-content">
                            {{ Str::limit($post->content, 200) }}
                            
                            @if($post->images && count($post->images) > 0)
                                <div style="margin-top: 10px;">
                                    <img src="{{ asset($post->images[0]) }}" 
                                         alt="Imagen del post" 
                                         class="post-image">
                                </div>
                            @endif
                        </div>

                        <div class="post-stats">
                            <div class="post-interactions">
                                <a href="{{ route('forum.show', $post->id) }}" class="interaction-btn">
                                     Ver Discusión
                                </a>
                                @auth
                                    @if($post->user_id === auth()->id() || auth()->user()->isAdmin())
                                        <form method="POST" action="{{ route('forum.destroy', $post->id) }}" 
                                              style="display: inline;"
                                              onsubmit="return confirm('¿Estás seguro de eliminar este post?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="interaction-btn" style="background: rgba(231, 76, 60, 0.2); border-color: #e74c3c;">
                                                Eliminar
                                            </button>
                                        </form>
                                    @endif
                                @endauth
                            </div>
                            <div class="last-activity">
                                Última actividad: {{ $post->last_activity->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Paginación -->
                <div class="pagination-wrapper">
                    {{ $posts->links() }}
                </div>
            @else
                <div class="empty-forum">
                    <div class="empty-icon">💬</div>
                    <h3>¡Sé el primero en iniciar una conversación!</h3>
                    <p>La comunidad está esperando tu primer post. Comparte algo interesante con otros gamers.</p>
                    @auth
                        <a href="{{ route('forum.create') }}" class="create-post-btn" style="margin-top: 20px; display: inline-block;">
                            ✏️ Crear Primer Post
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="create-post-btn" style="margin-top: 20px; display: inline-block;">
                            🔐 Iniciar Sesión para Participar
                        </a>
                    @endauth
                </div>
            @endif
        </div>
    </div>
</body>
</html>