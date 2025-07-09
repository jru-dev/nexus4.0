<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Nexus Gaming</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <div class="register-container">
        <!-- Left Side - Gaming Image -->
        <div class="image-section">
            <div class="image-content">
                <h1>¡Bienvenido<br>de vuelta,<br>gamer!</h1>
                <p>¿Nuevo en Nexus? Únete a nuestra comunidad gaming y descubre un mundo lleno de aventuras esperándote.</p>
                <a href="{{ route('register') }}" class="login-link">Registrarse</a>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="form-section">
            <h2>Iniciar Sesión</h2>
            
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div class="form-group">
                    <input type="email" 
                           name="email" 
                           class="form-input" 
                           placeholder="Correo Electrónico" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus>
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group">
                    <input type="password" 
                           name="password" 
                           class="form-input" 
                           placeholder="Contraseña" 
                           required>
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" class="register-btn">
                    Iniciar Sesión
                </button>
            </form>
        </div>
    </div>

</body>
</html>