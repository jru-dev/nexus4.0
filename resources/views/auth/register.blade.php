<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registro - Nexus Gaming</title>
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
</head>
<body>
    <div class="register-container">
        <!-- Left Side - Gaming Image -->
        <div class="image-section">
            <div class="image-content">
                <h1>¡Hola, gamer!<br>Tu aventura<br>comienza aquí.</h1>
                <p>Si tienes una cuenta, inicia sesión aquí y sumérgete en el mundo gaming que te espera.</p>
                <a href="{{ route('login') }}" class="login-link">Iniciar sesión</a>
            </div>
        </div>

        <!-- Right Side - Registration Form -->
        <div class="form-section">
            <h2>Regístrate aquí</h2>
            
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div class="form-group">
                    <input type="text" 
                           name="name" 
                           class="form-input" 
                           placeholder="Nombre" 
                           value="{{ old('name') }}" 
                           required>
                    @error('name')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email -->
                <div class="form-group">
                    <input type="email" 
                           name="email" 
                           class="form-input" 
                           placeholder="Correo Electrónico" 
                           value="{{ old('email') }}" 
                           required>
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

                <!-- Confirm Password -->
                <div class="form-group">
                    <input type="password" 
                           name="password_confirmation" 
                           class="form-input" 
                           placeholder="Confirmar Contraseña" 
                           required>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="register-btn">
                    Registrarse
                </button>
            </form>
        </div>
    </div>
    <script src="{{ asset('js/register.js') }}"></script>
</body>
</html>