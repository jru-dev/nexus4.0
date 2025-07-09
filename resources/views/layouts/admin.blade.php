<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Admin')</title>
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard-v2.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-games.css') }}">
    @yield('head')
</head>
<body>
    <header class="admin-header">
        <div class="admin-logo">
            <h1>🎮 NEXUS ADMIN <span class="admin-badge">ADMINISTRADOR</span></h1>
        </div>
        <div class="header-actions">
            <span class="welcome-text">Hola, {{ auth()->user()->name }}</span>
            <a href="{{ route('home') }}" class="btn btn-home">Ver Tienda</a>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-logout">Cerrar Sesión</button>
            </form>
        </div>
    </header>
    <div class="admin-container">
        <aside class="admin-sidebar">
            <nav class="admin-nav">
                <a href="{{ route('admin.dashboard') }}" class="nav-item">
                    <span class="nav-icon">📊</span>
                    <span class="nav-text">Dashboard</span>
                </a>
                <a href="{{ route('admin.games') }}" class="nav-item">
                    <span class="nav-icon">🎮</span>
                    <span class="nav-text">Gestionar Juegos</span>
                </a>
            </nav>
        </aside>
        <main class="admin-main">
            @yield('content')
        </main>
    </div>
    @yield('scripts')
</body>
</html>
