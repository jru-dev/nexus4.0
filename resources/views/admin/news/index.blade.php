<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Noticias - Nexus Admin</title>
    <link rel="stylesheet" href="{{ asset('css/admin-games.css') }}">
</head>
<body>
    <!-- Header del Admin -->
    <header class="admin-header">
        <div class="admin-logo">
            <h1>📰 GESTIÓN DE NOTICIAS</h1>
        </div>
        
        <div class="header-actions">
            <span>Hola, {{ auth()->user()->name }}</span>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-dashboard">Dashboard</a>
            <a href="{{ route('home') }}" class="btn btn-home">Ver Tienda</a>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
            </form>
        </div>
    </header>

    <div class="container">
        <!-- Header de la página -->
        <div class="page-header">
            <h1 class="page-title">Gestión de Noticias</h1>
            <a href="{{ route('admin.news.create') }}" class="btn btn-primary">
                ➕ Nueva Noticia
            </a>
        </div>

        <!-- Mensajes de éxito -->
        @if(session('success'))
            <div class="alert alert-success">
                <span class="alert-icon">✅</span>
                {{ session('success') }}
            </div>
        @endif

        <!-- Lista de noticias -->
        <div class="news-section">
            @if(isset($news) && $news->count() > 0)
                <div class="news-grid">
                    @foreach($news as $item)
                        <div class="news-card">
                            <div class="news-content">
                                <div class="news-header">
                                    <h3 class="news-title">{{ $item->title }}</h3>
                                    <div class="news-date">{{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}</div>
                                </div>
                                
                                <div class="news-excerpt">
                                    {{ Str::limit($item->content, 100) }}
                                </div>
                                
                                <div class="news-meta">
                                    <span class="news-created">Creado {{ $item->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            
                            <div class="news-actions">
                                <a href="{{ route('admin.news.edit', $item) }}" class="btn-sm btn-edit">
                                    Editar
                                </a>
                                <button onclick="deleteNews({{ $item->id }}, '{{ $item->title }}')" class="btn-sm btn-delete">
                                     Eliminar
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Paginación -->
                @if($news->hasPages())
                    <div class="pagination-wrapper">
                        {{ $news->links() }}
                    </div>
                @endif
            @else
                <div class="no-news">
                    <div class="empty-icon">📰</div>
                    <h3>No hay noticias registradas</h3>
                    <p>Crea la primera noticia para tu tienda</p>
                    <a href="{{ route('admin.news.create') }}" class="btn-primary">
                        Crear Primera Noticia
                    </a>
                </div>
            @endif
        </div>
    </div>

    <script>
        function deleteNews(id, title) {
            if (confirm(`¿Estás seguro de que quieres eliminar "${title}"? Esta acción no se puede deshacer.`)) {
                // Crear formulario para envío
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/news/${id}`;
                
                // Token CSRF
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                
                // Método DELETE
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                
                form.appendChild(csrfInput);
                form.appendChild(methodInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        // Mostrar notificación de éxito si es necesario
        @if(session('success'))
            showNotification('{{ session('success') }}', 'success');
        @endif
        
        @if(session('error'))
            showNotification('{{ session('error') }}', 'error');
        @endif

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.innerHTML = `
                <div class="notification-content">
                    <span class="notification-icon">${type === 'success' ? '✅' : '❌'}</span>
                    <span class="notification-message">${message}</span>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.add('show');
            }, 100);
            
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            }, 4000);
        }
    </script>

    <style>
        /* Estilos base del body */
        body {
            background: #1a1625;
            color: white;
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        /* Container principal */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Estilos específicos para noticias */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #374151;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: white;
            margin: 0;
        }

        .btn.btn-primary {
            background: #6366f1;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn.btn-primary:hover {
            background: #4f46e5;
            transform: translateY(-2px);
        }

        .alert {
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #065f46;
            border: 1px solid #10b981;
            color: #d1fae5;
        }

        .alert-icon {
            font-size: 1.2rem;
        }

        .news-section {
            background: #2d3748;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }

        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 24px;
        }

        .news-card {
            background: #374151;
            border: 1px solid #4b5563;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .news-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.4);
            border-color: #6366f1;
        }

        .news-image {
            height: 180px;
            overflow: hidden;
            background: #1f2937;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .news-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .news-placeholder {
            font-size: 3rem;
            color: #6b7280;
        }

        .news-content {
            padding: 20px;
        }

        .news-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .news-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: white;
            margin: 0;
            line-height: 1.4;
            flex: 1;
            margin-right: 12px;
        }

        .news-date {
            background: #4f46e5;
            color: white;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
            white-space: nowrap;
        }

        .news-excerpt {
            color: #d1d5db;
            line-height: 1.5;
            margin-bottom: 12px;
            font-size: 0.95rem;
        }

        .news-meta {
            font-size: 0.85rem;
            color: #9ca3af;
            margin-bottom: 16px;
        }

        .news-actions {
            display: flex;
            gap: 8px;
            padding: 16px 20px;
            background: #1f2937;
            border-top: 1px solid #4b5563;
        }

        .btn-sm {
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-edit {
            background: #f59e0b;
            color: white;
        }

        .btn-edit:hover {
            background: #d97706;
        }

        .btn-delete {
            background: #dc2626;
            color: white;
        }

        .btn-delete:hover {
            background: #b91c1c;
        }

        .no-news {
            text-align: center;
            padding: 60px 20px;
            color: white;
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.6;
        }

        .no-news h3 {
            color: #d1d5db;
            margin-bottom: 12px;
        }

        .no-news p {
            color: #9ca3af;
            margin-bottom: 24px;
        }

        .pagination-wrapper {
            margin-top: 30px;
            display: flex;
            justify-content: center;
        }

        /* Notificaciones */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #374151;
            border-radius: 8px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.3);
            padding: 16px 20px;
            z-index: 1000;
            transform: translateX(400px);
            transition: transform 0.3s ease;
        }

        .notification.show {
            transform: translateX(0);
        }

        .notification.success {
            border-left: 4px solid #10b981;
        }

        .notification.error {
            border-left: 4px solid #ef4444;
        }

        .notification-content {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .notification-icon {
            font-size: 1.2rem;
        }

        .notification-message {
            font-weight: 500;
            color: white;
        }
    </style>
</body>
</html>