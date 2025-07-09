<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Crear Post - Comunidad Nexus</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <style>
        /* Estilos específicos para crear post */
        .create-post-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .page-header {
            background: rgba(0, 0, 0, 0.6);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }

        .page-title {
            font-size: 2.5em;
            color: #5a4fcf;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .page-subtitle {
            color: #ccc;
            font-size: 1.1em;
        }

        .back-btn {
            background: #6c757d;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            transition: all 0.3s;
        }

        .back-btn:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .create-form-card {
            background: rgba(0, 0, 0, 0.6);
            border-radius: 15px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            display: block;
            color: white;
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 1.1em;
        }

        .form-input {
            width: 100%;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid #555;
            border-radius: 8px;
            color: white;
            font-size: 1em;
            transition: all 0.3s;
        }

        .form-input:focus {
            outline: none;
            border-color: #5a4fcf;
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 10px rgba(90, 79, 207, 0.3);
        }

        .form-input::placeholder {
            color: #ccc;
        }

        .form-textarea {
            min-height: 200px;
            resize: vertical;
            font-family: inherit;
            line-height: 1.6;
        }

        .image-upload-section {
            border: 2px dashed #555;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
            position: relative;
        }

        .image-upload-section:hover {
            border-color: #5a4fcf;
            background: rgba(90, 79, 207, 0.05);
        }

        .image-upload-section.has-file {
            border-color: #27ae60;
            background: rgba(39, 174, 96, 0.05);
        }

        .upload-icon {
            font-size: 3em;
            margin-bottom: 15px;
            color: #999;
        }

        .upload-text {
            color: #ccc;
            font-size: 1.1em;
            margin-bottom: 10px;
        }

        .upload-hint {
            color: #999;
            font-size: 0.9em;
        }

        .file-input {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .image-preview {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            margin-top: 15px;
        }

        .remove-image-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 10px;
            font-size: 0.9em;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            justify-content: flex-end;
        }

        .submit-btn {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
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
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.4);
        }

        .submit-btn:disabled {
            background: #666;
            cursor: not-allowed;
            transform: none;
        }

        .cancel-btn {
            background: #6c757d;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
        }

        .cancel-btn:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .error-message {
            color: #e74c3c;
            font-size: 0.9em;
            margin-top: 5px;
            background: rgba(231, 76, 60, 0.1);
            padding: 8px 12px;
            border-radius: 6px;
            border-left: 3px solid #e74c3c;
        }

        .character-count {
            text-align: right;
            color: #999;
            font-size: 0.85em;
            margin-top: 5px;
        }

        .character-count.warning {
            color: #f39c12;
        }

        .character-count.danger {
            color: #e74c3c;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .create-post-container {
                padding: 20px 15px;
            }

            .create-form-card {
                padding: 20px;
            }

            .page-title {
                font-size: 2em;
            }

            .form-actions {
                flex-direction: column;
            }

            .submit-btn, .cancel-btn {
                width: 100%;
                text-align: center;
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
                    
                @endauth
                
                <div class="nexus-logo">
                    <span class="logo-text">NEXUS</span>
                </div>
            </div>
        </div>
    </header>

    <div class="create-post-container">
        <!-- Botón de regreso -->
        <a href="{{ route('forum.index') }}" class="back-btn">
            ← Volver al Foro
        </a>

        <!-- Header de la página -->
        <div class="page-header">
            <h1 class="page-title">✏️ CREAR NUEVO POST</h1>
            <p class="page-subtitle">Comparte algo interesante con la comunidad</p>
        </div>

        <!-- Formulario -->
        <div class="create-form-card">
            <form action="{{ route('forum.store') }}" method="POST" enctype="multipart/form-data" id="createPostForm">
                @csrf

                <!-- Título del Post -->
                <div class="form-group">
                    <label class="form-label">📝 Título del Post</label>
                    <input type="text" 
                           name="title" 
                           class="form-input" 
                           placeholder="Escribe un título llamativo para tu post..."
                           value="{{ old('title') }}"
                           maxlength="255"
                           required
                           id="titleInput">
                    @error('title')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <div class="character-count" id="titleCount">0 / 255</div>
                </div>

                <!-- Contenido del Post -->
                <div class="form-group">
                    <label class="form-label">💬 Contenido</label>
                    <textarea name="content" 
                              class="form-input form-textarea" 
                              placeholder="¿Qué quieres compartir con la comunidad? Puedes hablar sobre juegos, experiencias, preguntas, o cualquier tema relacionado con gaming..."
                              required
                              maxlength="5000"
                              id="contentInput">{{ old('content') }}</textarea>
                    @error('content')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <div class="character-count" id="contentCount">0 / 5000</div>
                </div>

                <!-- Subir Imagen (Opcional) -->
                <div class="form-group">
                    <label class="form-label">🖼️ Imagen (Opcional)</label>
                    <div class="image-upload-section" id="imageUploadSection">
                        <input type="file" 
                               name="image" 
                               accept="image/*" 
                               class="file-input"
                               id="imageInput">
                        <div class="upload-content" id="uploadContent">
                            <div class="upload-icon">📁</div>
                            <div class="upload-text">Haz clic para seleccionar una imagen</div>
                            <div class="upload-hint">JPG, PNG o WEBP • Máximo 2MB</div>
                        </div>
                        <div class="image-preview-container" id="previewContainer" style="display: none;">
                            <img id="imagePreview" class="image-preview" alt="Vista previa">
                            <button type="button" class="remove-image-btn" id="removeImageBtn">
                                🗑️ Quitar imagen
                            </button>
                        </div>
                    </div>
                    @error('image')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Botones de Acción -->
                <div class="form-actions">
                    <a href="{{ route('forum.index') }}" class="cancel-btn">
                        Cancelar
                    </a>
                    <button type="submit" class="submit-btn" id="submitBtn">
                        🚀 Publicar Post
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Contador de caracteres para título
        const titleInput = document.getElementById('titleInput');
        const titleCount = document.getElementById('titleCount');

        titleInput.addEventListener('input', function() {
            const length = this.value.length;
            titleCount.textContent = `${length} / 255`;
            
            if (length > 200) {
                titleCount.classList.add('warning');
            } else {
                titleCount.classList.remove('warning');
            }
            
            if (length >= 255) {
                titleCount.classList.add('danger');
            } else {
                titleCount.classList.remove('danger');
            }
        });

        // Contador de caracteres para contenido
        const contentInput = document.getElementById('contentInput');
        const contentCount = document.getElementById('contentCount');

        contentInput.addEventListener('input', function() {
            const length = this.value.length;
            contentCount.textContent = `${length} / 5000`;
            
            if (length > 4000) {
                contentCount.classList.add('warning');
            } else {
                contentCount.classList.remove('warning');
            }
            
            if (length >= 5000) {
                contentCount.classList.add('danger');
            } else {
                contentCount.classList.remove('danger');
            }
        });

        // Manejo de imagen
        const imageInput = document.getElementById('imageInput');
        const uploadSection = document.getElementById('imageUploadSection');
        const uploadContent = document.getElementById('uploadContent');
        const previewContainer = document.getElementById('previewContainer');
        const imagePreview = document.getElementById('imagePreview');
        const removeImageBtn = document.getElementById('removeImageBtn');

        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                // Validar tamaño (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('La imagen no puede ser mayor a 2MB');
                    this.value = '';
                    return;
                }

                // Validar tipo
                if (!file.type.startsWith('image/')) {
                    alert('Por favor selecciona un archivo de imagen válido');
                    this.value = '';
                    return;
                }

                // Mostrar preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    uploadContent.style.display = 'none';
                    previewContainer.style.display = 'block';
                    uploadSection.classList.add('has-file');
                };
                reader.readAsDataURL(file);
            }
        });

        // Quitar imagen
        removeImageBtn.addEventListener('click', function() {
            imageInput.value = '';
            uploadContent.style.display = 'block';
            previewContainer.style.display = 'none';
            uploadSection.classList.remove('has-file');
        });

        // Validación del formulario
        const form = document.getElementById('createPostForm');
        const submitBtn = document.getElementById('submitBtn');

        form.addEventListener('submit', function(e) {
            const title = titleInput.value.trim();
            const content = contentInput.value.trim();

            if (!title || !content) {
                e.preventDefault();
                alert('Por favor completa todos los campos obligatorios');
                return;
            }

            if (title.length > 255) {
                e.preventDefault();
                alert('El título no puede tener más de 255 caracteres');
                return;
            }

            if (content.length > 5000) {
                e.preventDefault();
                alert('El contenido no puede tener más de 5000 caracteres');
                return;
            }

            // Deshabilitar botón para evitar doble envío
            submitBtn.disabled = true;
            submitBtn.textContent = '🚀 Publicando...';
        });

        // Inicializar contadores
        titleInput.dispatchEvent(new Event('input'));
        contentInput.dispatchEvent(new Event('input'));
    </script>
</body>
</html>