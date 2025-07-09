@csrf
<div class="form-row">
    <div class="form-group">
        <label for="title">Título de la Noticia</label>
        <input type="text" 
               name="title" 
               id="title" 
               class="form-control" 
               value="{{ old('title', $news->title ?? '') }}" 
               required 
               maxlength="255"
               placeholder="Ej: Nueva actualización disponible para Cyberpunk 2077">
        @error('title')<span class="error">{{ $message }}</span>@enderror
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label for="date">Fecha de Publicación</label>
        <input type="date" 
               name="date" 
               id="date" 
               class="form-control" 
               value="{{ old('date', $news->date ?? date('Y-m-d')) }}" 
               required>
        @error('date')<span class="error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group">
        <label for="image">Imagen (Opcional)</label>
        <input type="file" 
               name="image" 
               id="image" 
               class="form-control image-input" 
               accept="image/*"
               onchange="previewImage(this)">
        @error('image')<span class="error">{{ $message }}</span>@enderror
        
        <!-- Preview de imagen actual o nueva -->
        <div id="image-preview-container" style="margin-top: 12px;">
            @if(!empty($news->image))
                <div class="current-image">
                    <img src="{{ asset($news->image) }}" alt="Imagen actual" class="image-preview">
                    <span class="image-label">Imagen actual</span>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="form-group">
    <label for="content">Contenido de la Noticia</label>
    <textarea name="content" 
              id="content" 
              class="form-control content-textarea" 
              rows="8" 
              required 
              maxlength="2000"
              placeholder="Escribe el contenido de la noticia aquí...">{{ old('content', $news->content ?? '') }}</textarea>
    <div class="character-count">
        <span id="char-count">0</span>/2000 caracteres
    </div>
    @error('content')<span class="error">{{ $message }}</span>@enderror
</div>

<style>
.form-row {
    display: flex;
    gap: 24px;
    margin-bottom: 0;
}

.form-group {
    flex: 1;
    display: flex;
    flex-direction: column;
    margin-bottom: 20px;
}

.form-group label {
    font-weight: 600;
    margin-bottom: 8px;
    color: #374151;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 1rem;
}

.form-control {
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 12px 16px;
    font-size: 1rem;
    background: #ffffff;
    color: #374151;
    transition: all 0.2s;
    font-family: inherit;
}

.form-control:focus {
    border-color: #6366f1;
    outline: none;
    background: #ffffff;
    color: #1f2937;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.content-textarea {
    resize: vertical;
    min-height: 120px;
    line-height: 1.6;
    color: #374151;
    background: #ffffff;
}

.content-textarea::placeholder {
    color: #9ca3af;
}

.image-input {
    background: #ffffff;
    color: #374151;
}

.image-input::file-selector-button {
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 8px 12px;
    margin-right: 12px;
    cursor: pointer;
    transition: background 0.2s;
}

.image-input::file-selector-button:hover {
    background: #e5e7eb;
}

.error {
    color: #ef4444;
    font-size: 0.9rem;
    margin-top: 6px;
    font-weight: 500;
}

.image-preview {
    max-width: 200px;
    max-height: 150px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    object-fit: cover;
}

.current-image {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
}

.image-label {
    font-size: 0.9rem;
    color: #64748b;
    font-weight: 500;
}

.character-count {
    text-align: right;
    margin-top: 6px;
    font-size: 0.9rem;
    color: #64748b;
}

#char-count {
    font-weight: 600;
}

.character-count.warning {
    color: #f59e0b;
}

.character-count.danger {
    color: #ef4444;
}

/* Placeholders para mejor contraste */
.form-control::placeholder {
    color: #9ca3af;
    opacity: 1;
}

.form-control::-webkit-input-placeholder {
    color: #9ca3af;
}

.form-control::-moz-placeholder {
    color: #9ca3af;
    opacity: 1;
}

.form-control:-ms-input-placeholder {
    color: #9ca3af;
}

/* Responsive */
@media (max-width: 768px) {
    .form-row {
        flex-direction: column;
        gap: 0;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Contador de caracteres para el contenido
    const contentTextarea = document.getElementById('content');
    const charCountSpan = document.getElementById('char-count');
    const characterCountDiv = document.querySelector('.character-count');
    
    function updateCharacterCount() {
        const currentLength = contentTextarea.value.length;
        charCountSpan.textContent = currentLength;
        
        // Cambiar color según el límite
        characterCountDiv.classList.remove('warning', 'danger');
        if (currentLength > 1800) {
            characterCountDiv.classList.add('danger');
        } else if (currentLength > 1500) {
            characterCountDiv.classList.add('warning');
        }
    }
    
    // Actualizar contador al cargar y al escribir
    updateCharacterCount();
    contentTextarea.addEventListener('input', updateCharacterCount);
    
    // Auto-resize del textarea
    contentTextarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.max(120, this.scrollHeight) + 'px';
    });
});

// Función para preview de imagen
function previewImage(input) {
    const container = document.getElementById('image-preview-container');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            // Limpiar preview anterior
            const existingPreview = container.querySelector('.new-image-preview');
            if (existingPreview) {
                existingPreview.remove();
            }
            
            // Crear nuevo preview
            const previewDiv = document.createElement('div');
            previewDiv.className = 'new-image-preview';
            previewDiv.style.marginTop = '12px';
            
            previewDiv.innerHTML = `
                <img src="${e.target.result}" alt="Vista previa" class="image-preview">
                <span class="image-label">Nueva imagen seleccionada</span>
            `;
            
            container.appendChild(previewDiv);
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>