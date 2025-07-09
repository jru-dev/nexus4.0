<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Editar Perfil - Nexus</title>
    <link rel="stylesheet" href="{{ asset('css/profile-edit.css') }}">
</head>
<body>
    <!-- Header Principal -->
    <header class="main-header">
        <nav class="nav-menu">
            <a href="{{ route('home') }}" class="nav-item">TIENDA</a>
            <a href="{{ route('library.index') }}" class="nav-item">BIBLIOTECA</a>
            <a href="{{ route('forum.index') }}" class="nav-item">COMUNIDAD</a>
            <a href="{{ route('profile.index') }}" class="nav-item active">PERFIL</a>
        </nav>
        
        <div class="user-section">
            <div class="cart-icon">
                🛒
                <span class="cart-count">0</span>
            </div>
            <div class="nexus-logo">
                <span class="logo-text">NEXUS</span>
            </div>
        </div>
    </header>

    <div class="edit-profile-container">
        <!-- Mensaje de éxito -->
        @if(session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif

        <!-- Formulario de Edición -->
        <div class="edit-form-card">
            <form action="{{ route('profile.update.custom') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                
                <!-- Sección de Foto de Perfil -->
                <div class="form-section">
                    <h3>🖼️ Foto de Perfil</h3>
                    <div class="photo-upload-section">
                        <div class="current-photo">
                            <div class="photo-preview" id="photoPreview">
                                @if($user->profile_image)
                                    <img src="{{ asset($user->profile_image) }}" alt="Foto de perfil">
                                @else
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                @endif
                            </div>
                            <p style="color: #ccc; font-size: 0.9em;">Foto actual</p>
                        </div>
                        
                        <div class="photo-upload">
                            <div class="form-group">
                                <label class="form-label">Subir nueva foto</label>
                                <label class="file-input-wrapper">
                                    📁 Seleccionar archivo
                                    <input type="file" name="profile_image" accept="image/*" id="photoInput">
                                </label>
                                <div class="file-info">
                                    <p>Formatos: JPG, PNG, WEBP</p>
                                    <p>Tamaño máximo: 2MB</p>
                                    <p id="selectedFile"></p>
                                </div>
                                @error('profile_image')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de Información Personal -->
                <div class="form-section">
                    <h3>👤 Información Personal</h3>
                    
                    <div class="form-group">
                        <label class="form-label">Nombre de usuario</label>
                        <input type="text" name="name" class="form-input" 
                               value="{{ old('name', $user->name) }}" 
                               placeholder="Tu nombre de usuario"
                               required>
                        @error('name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Biografía</label>
                        <textarea name="bio" class="form-input form-textarea" 
                                  placeholder="Cuéntanos sobre ti... ¿Cuáles son tus juegos favoritos?">{{ old('bio', $user->bio) }}</textarea>
                        @error('bio')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Botones -->
                <div class="form-buttons">
                    <a href="{{ route('profile.index') }}" class="cancel-btn">Cancelar</a>
                    <button type="submit" class="save-btn">💾 Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Preview de la imagen seleccionada
        document.getElementById('photoInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('photoPreview');
            const fileInfo = document.getElementById('selectedFile');
            
            if (file) {
                // Mostrar nombre del archivo
                fileInfo.textContent = `Archivo seleccionado: ${file.name}`;
                
                // Crear preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Vista previa">`;
                };
                reader.readAsDataURL(file);
            } else {
                fileInfo.textContent = '';
            }
        });
    </script>
</body>
</html>