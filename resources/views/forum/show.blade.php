<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $post->title }} - Comunidad Nexus</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <style>
        /* Estilos específicos para la vista del post */
        .forum-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .breadcrumb {
            margin-bottom: 20px;
            color: #ccc;
            font-size: 0.9em;
        }

        .breadcrumb a {
            color: #5a4fcf;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .post-main {
            background: rgba(0, 0, 0, 0.6);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .post-title {
            font-size: 2.5em;
            color: white;
            margin-bottom: 20px;
            font-weight: bold;
            line-height: 1.2;
        }

        .post-author-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .author-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #5a4fcf, #4a3fcf);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2em;
        }

        .author-details {
            flex: 1;
        }

        .author-name {
            font-size: 1.2em;
            font-weight: bold;
            color: white;
            margin-bottom: 5px;
        }

        .post-date {
            color: #ccc;
            font-size: 0.9em;
        }

        .post-meta {
            display: flex;
            gap: 20px;
            color: #999;
            font-size: 0.9em;
        }

        .post-content {
            color: #ddd;
            line-height: 1.8;
            font-size: 1.1em;
            margin-bottom: 20px;
        }

        .post-image {
            max-width: 100%;
            border-radius: 10px;
            margin: 20px 0;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .post-actions {
            display: flex;
            gap: 15px;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .action-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9em;
            transition: all 0.3s;
            cursor: pointer;
        }

        .action-btn:hover {
            background: #5a4fcf;
            border-color: #5a4fcf;
        }

        .action-btn.danger {
            background: rgba(231, 76, 60, 0.2);
            border-color: #e74c3c;
        }

        .action-btn.danger:hover {
            background: #e74c3c;
        }

        /* Sección de respuestas */
        .replies-section {
            background: rgba(0, 0, 0, 0.4);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .section-title {
            color: white;
            font-size: 1.5em;
            margin-bottom: 20px;
            border-bottom: 2px solid #5a4fcf;
            padding-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .reply-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s;
        }

        .reply-card:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(90, 79, 207, 0.5);
        }

        .reply-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
        }

        .reply-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1em;
        }

        .reply-author {
            font-weight: bold;
            color: white;
            margin-bottom: 3px;
        }

        .reply-date {
            color: #ccc;
            font-size: 0.85em;
        }

        .reply-content {
            color: #ddd;
            line-height: 1.6;
            font-size: 1em;
        }

        /* Formulario de respuesta */
        .reply-form-section {
            background: rgba(0, 0, 0, 0.6);
            border-radius: 15px;
            padding: 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .reply-form-title {
            color: white;
            font-size: 1.3em;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            color: white;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .form-textarea {
            width: 100%;
            min-height: 120px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid #555;
            border-radius: 8px;
            color: white;
            padding: 15px;
            font-size: 1em;
            resize: vertical;
            font-family: inherit;
            line-height: 1.5;
            transition: all 0.3s;
        }

        .form-textarea:focus {
            outline: none;
            border-color: #5a4fcf;
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 10px rgba(90, 79, 207, 0.3);
        }

        .form-textarea::placeholder {
            color: #ccc;
        }

        .submit-btn {
            background: linear-gradient(135deg, #5a4fcf, #4a3fcf);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(90, 79, 207, 0.4);
        }

        .login-prompt {
            text-align: center;
            padding: 30px;
            background: rgba(0, 0, 0, 0.4);
            border: 2px dashed #555;
            border-radius: 10px;
            color: #ccc;
        }

        .login-prompt-btn {
            background: #27ae60;
            color: white;
            padding: 12px 25px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            margin-top: 15px;
            transition: all 0.3s;
        }

        .login-prompt-btn:hover {
            background: #219a52;
            transform: translateY(-2px);
        }

        .no-replies {
            text-align: center;
            padding: 40px;
            color: #ccc;
        }

        .error-message {
            background: rgba(231, 76, 60, 0.2);
            border: 1px solid #e74c3c;
            color: #e74c3c;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .success-message {
            background: rgba(39, 174, 96, 0.2);
            border: 1px solid #27ae60;
            color: #27ae60;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .forum-container {
                padding: 20px 15px;
            }

            .post-title {
                font-size: 2em;
            }

            .post-author-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .post-actions {
                flex-direction: column;
                gap: 10px;
            }

            .action-btn {
                text-align: center;
            }

            .reply-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
        }
    </style>
</head>
<body>
    <!-- Header Principal -->
    <header class="main-header">
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
                @auth
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

    <div class="forum-container">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="{{ route('home') }}">Inicio</a> / 
            <a href="{{ route('forum.index') }}">Comunidad</a> / 
            <span>{{ $post->title }}</span>
        </div>

        <!-- Mensajes de éxito/error -->
        @if(session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="error-message">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="error-message">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Post Principal -->
        <div class="post-main">
            <h1 class="post-title">{{ $post->title }}</h1>
            
            <div class="post-author-info">
                @if($post->user->profile_image)
                    <img src="{{ asset($post->user->profile_image) }}" 
                         alt="{{ $post->user->name }}" 
                         class="author-avatar">
                @else
                    <div class="author-avatar">
                        {{ strtoupper(substr($post->user->name, 0, 1)) }}
                    </div>
                @endif
                
                <div class="author-details">
                    <div class="author-name">{{ $post->user->name }}</div>
                    <div class="post-date">{{ $post->created_at->format('d/m/Y H:i') }}</div>
                </div>
                
                <div class="post-meta">
                    <span>👁️ {{ $post->views }} vistas</span>
                    <span>💬 {{ $post->replies_count }} respuestas</span>
                </div>
            </div>
            
            <div class="post-content">
                {!! nl2br(e($post->content)) !!}
                
                @if($post->images && count($post->images) > 0)
                    <div>
                        <img src="{{ asset($post->images[0]) }}" 
                             alt="Imagen del post" 
                             class="post-image">
                    </div>
                @endif
            </div>
            
            <div class="post-actions">
                <a href="{{ route('forum.index') }}" class="action-btn">
                    ← Volver al Foro
                </a>
                
                @auth
                    @if($post->user_id === auth()->id() || auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('forum.destroy', $post->id) }}" 
                              style="display: inline;"
                              onsubmit="return confirm('¿Estás seguro de eliminar este post? Esta acción no se puede deshacer.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn danger">
                                🗑️ Eliminar Post
                            </button>
                        </form>
                    @endif
                @endauth
            </div>
        </div>

        <!-- Sección de Respuestas -->
        <div class="replies-section">
            <h2 class="section-title">
                💬 Respuestas ({{ $replies->count() }})
            </h2>

            @if($replies->count() > 0)
                @foreach($replies as $reply)
                    <div class="reply-card">
                        <div class="reply-header">
                            @if($reply->user->profile_image)
                                <img src="{{ asset($reply->user->profile_image) }}" 
                                     alt="{{ $reply->user->name }}" 
                                     class="reply-avatar">
                            @else
                                <div class="reply-avatar">
                                    {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                                </div>
                            @endif
                            
                            <div>
                                <div class="reply-author">{{ $reply->user->name }}</div>
                                <div class="reply-date">{{ $reply->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        
                        <div class="reply-content">
                            {!! nl2br(e($reply->content)) !!}
                        </div>
                    </div>
                @endforeach
            @else
                <div class="no-replies">
                    <p>🤔 Aún no hay respuestas a este post.</p>
                    <p>¡Sé el primero en participar en la conversación!</p>
                </div>
            @endif
        </div>

        <!-- Formulario para Responder -->
        <div class="reply-form-section">
            @auth
                @if(!$post->is_locked)
                    <h3 class="reply-form-title">
                        ✏️ Escribe tu respuesta
                    </h3>
                    
                    <form action="{{ route('forum.reply', $post->id) }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label class="form-label">Tu respuesta</label>
                            <textarea name="content" 
                                      class="form-textarea" 
                                      placeholder="Comparte tu opinión sobre este tema..."
                                      required>{{ old('content') }}</textarea>
                        </div>
                        
                        <button type="submit" class="submit-btn">
                            💬 Publicar Respuesta
                        </button>
                    </form>
                @else
                    <div class="login-prompt">
                        <h3>🔒 Post Cerrado</h3>
                        <p>Este post ha sido cerrado y no se pueden agregar más respuestas.</p>
                    </div>
                @endif
            @else
                <div class="login-prompt">
                    <h3>🔐 Inicia Sesión para Participar</h3>
                    <p>Para escribir una respuesta necesitas estar registrado en Nexus.</p>
                    <a href="{{ route('login') }}" class="login-prompt-btn">
                        Iniciar Sesión
                    </a>
                </div>
            @endauth
        </div>
    </div>
</body>
</html>