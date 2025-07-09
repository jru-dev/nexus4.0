@extends('layouts.admin')

@section('content')
<div class="admin-form-wrapper" style="background: linear-gradient(135deg, #7c3aed 0%, #a78bfa 100%); min-height: 100vh; padding-top: 40px; padding-bottom: 40px;">
    <div class="admin-form-card">
        <h1 class="admin-form-title">Agregar Nuevo Juego</h1>
        {{-- Mostrar errores generales --}}
        @if ($errors->any())
            <div class="alert alert-danger" style="margin-bottom:18px;">
                <ul style="margin:0; padding-left:18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('admin.games.store') }}" method="POST" enctype="multipart/form-data" class="game-form">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label for="title">Nombre del Juego</label>
                    <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required maxlength="255" placeholder="Ej: The Witcher 3">
                    @error('title')<span class="error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="price"> Precio (S/)</label>
                    <input type="number" name="price" id="price" class="form-control" value="{{ old('price') }}" step="0.01" min="0" max="999.99" placeholder="Ej: 59.99">
                    @error('price')<span class="error">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="category_id"> Categoría</label>
                    <select name="category_id" id="category_id" class="form-control" required>
                        <option value="">Selecciona una categoría</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<span class="error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="image"> Imagen principal</label>
                    <input type="file" name="image" id="image" class="form-control image-input" accept="image/*">
                    @error('image')<span class="error">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="developer"> Desarrollador</label>
                    <input type="text" name="developer" id="developer" class="form-control" value="{{ old('developer') }}" required maxlength="255" placeholder="Ej: CD Projekt Red">
                    @error('developer')<span class="error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="publisher"> Publicador</label>
                    <input type="text" name="publisher" id="publisher" class="form-control" value="{{ old('publisher') }}" required maxlength="255" placeholder="Ej: CD Projekt">
                    @error('publisher')<span class="error">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="release_date"> Fecha de Lanzamiento</label>
                    <input type="date" name="release_date" id="release_date" class="form-control" value="{{ old('release_date') }}" required>
                    @error('release_date')<span class="error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label for="age_rating"> Clasificación de Edad</label>
                    <select name="age_rating" id="age_rating" class="form-control" required>
                        <option value="">Selecciona</option>
                        <option value="E" {{ old('age_rating') == 'E' ? 'selected' : '' }}>E (Todos)</option>
                        <option value="E10+" {{ old('age_rating') == 'E10+' ? 'selected' : '' }}>E10+ (10+ años)</option>
                        <option value="T" {{ old('age_rating') == 'T' ? 'selected' : '' }}>T (Adolescentes)</option>
                        <option value="M" {{ old('age_rating') == 'M' ? 'selected' : '' }}>M (Maduros 17+)</option>
                        <option value="AO" {{ old('age_rating') == 'AO' ? 'selected' : '' }}>AO (Solo adultos)</option>
                    </select>
                    @error('age_rating')<span class="error">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-group">
                <label for="description"> Descripción</label>
                <textarea name="description" id="description" class="form-control" rows="4" required maxlength="2000" placeholder="Describe el juego...">{{ old('description') }}</textarea>
                @error('description')<span class="error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label for="screenshots"> Capturas de pantalla (máx. 5 imágenes)</label>
                <input type="file" name="screenshots[]" id="screenshots" class="form-control" accept="image/*" multiple onchange="updateScreenshotCount()" max="5">
                <div id="screenshot-count" style="margin-top: 6px; color: #6366f1; font-weight: 500;"></div>
                @error('screenshots')<span class="error">{{ $message }}</span>@enderror
            </div>

            <!-- NUEVA SECCIÓN: REQUERIMIENTOS DEL SISTEMA -->
            <div class="requirements-section">
                <h3 class="requirements-title"> Requerimientos del Sistema</h3>
                
                <!-- Requerimientos Mínimos -->
                <div class="requirements-group">
                    <h4 class="requirements-subtitle">Requerimientos Mínimos</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="min_os">Sistema Operativo</label>
                            <input type="text" name="system_requirements[minimum][os]" id="min_os" class="form-control" 
                                   value="{{ old('system_requirements.minimum.os') }}" 
                                   placeholder="Ej: Windows 10 64-bit">
                        </div>
                        <div class="form-group">
                            <label for="min_processor">Procesador</label>
                            <input type="text" name="system_requirements[minimum][processor]" id="min_processor" class="form-control" 
                                   value="{{ old('system_requirements.minimum.processor') }}" 
                                   placeholder="Ej: Intel Core i5-6600K">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="min_memory">Memoria RAM</label>
                            <input type="text" name="system_requirements[minimum][memory]" id="min_memory" class="form-control" 
                                   value="{{ old('system_requirements.minimum.memory') }}" 
                                   placeholder="Ej: 8 GB RAM">
                        </div>
                        <div class="form-group">
                            <label for="min_graphics">Tarjeta Gráfica</label>
                            <input type="text" name="system_requirements[minimum][graphics]" id="min_graphics" class="form-control" 
                                   value="{{ old('system_requirements.minimum.graphics') }}" 
                                   placeholder="Ej: NVIDIA GeForce GTX 960">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="min_storage">Almacenamiento</label>
                            <input type="text" name="system_requirements[minimum][storage]" id="min_storage" class="form-control" 
                                   value="{{ old('system_requirements.minimum.storage') }}" 
                                   placeholder="Ej: 50 GB">
                        </div>
                        <div class="form-group">
                            <label for="min_network">Red (Opcional)</label>
                            <input type="text" name="system_requirements[minimum][network]" id="min_network" class="form-control" 
                                   value="{{ old('system_requirements.minimum.network') }}" 
                                   placeholder="Ej: Conexión de banda ancha">
                        </div>
                    </div>
                </div>

                <!-- Requerimientos Recomendados -->
                <div class="requirements-group">
                    <h4 class="requirements-subtitle">Requerimientos Recomendados</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="rec_os">Sistema Operativo</label>
                            <input type="text" name="system_requirements[recommended][os]" id="rec_os" class="form-control" 
                                   value="{{ old('system_requirements.recommended.os') }}" 
                                   placeholder="Ej: Windows 11 64-bit">
                        </div>
                        <div class="form-group">
                            <label for="rec_processor">Procesador</label>
                            <input type="text" name="system_requirements[recommended][processor]" id="rec_processor" class="form-control" 
                                   value="{{ old('system_requirements.recommended.processor') }}" 
                                   placeholder="Ej: Intel Core i7-8700K">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="rec_memory">Memoria RAM</label>
                            <input type="text" name="system_requirements[recommended][memory]" id="rec_memory" class="form-control" 
                                   value="{{ old('system_requirements.recommended.memory') }}" 
                                   placeholder="Ej: 16 GB RAM">
                        </div>
                        <div class="form-group">
                            <label for="rec_graphics">Tarjeta Gráfica</label>
                            <input type="text" name="system_requirements[recommended][graphics]" id="rec_graphics" class="form-control" 
                                   value="{{ old('system_requirements.recommended.graphics') }}" 
                                   placeholder="Ej: NVIDIA GeForce RTX 3060">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="rec_storage">Almacenamiento</label>
                            <input type="text" name="system_requirements[recommended][storage]" id="rec_storage" class="form-control" 
                                   value="{{ old('system_requirements.recommended.storage') }}" 
                                   placeholder="Ej: 50 GB SSD">
                        </div>
                        <div class="form-group">
                            <label for="rec_network">Red (Opcional)</label>
                            <input type="text" name="system_requirements[recommended][network]" id="rec_network" class="form-control" 
                                   value="{{ old('system_requirements.recommended.network') }}" 
                                   placeholder="Ej: Conexión de banda ancha">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Guardar Juego</button>
                <a href="{{ route('admin.games') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
function updateScreenshotCount() {
    const input = document.getElementById('screenshots');
    const countDiv = document.getElementById('screenshot-count');
    if (input.files.length > 5) {
        countDiv.innerHTML = `Solo puedes seleccionar hasta 5 imágenes. Has seleccionado ${input.files.length}.`;
        countDiv.style.color = '#e53e3e';
        input.value = '';
    } else if (input.files.length > 0) {
        countDiv.innerHTML = `Imágenes seleccionadas: ${input.files.length}`;
        countDiv.style.color = '#6366f1';
    } else {
        countDiv.innerHTML = '';
    }
}

// Auto-llenar requerimientos recomendados basados en los mínimos
document.addEventListener('DOMContentLoaded', function() {
    const minInputs = document.querySelectorAll('[name^="system_requirements[minimum]"]');
    
    minInputs.forEach(input => {
        input.addEventListener('blur', function() {
            const fieldName = this.name.match(/\[([^\]]+)\]$/)[1];
            const recInput = document.querySelector(`[name="system_requirements[recommended][${fieldName}]"]`);
            
            if (recInput && !recInput.value && this.value) {
                // Auto-sugerir mejores especificaciones
                let suggestion = this.value;
                
                if (fieldName === 'memory') {
                    suggestion = suggestion.replace(/\d+/, (match) => parseInt(match) * 2);
                } else if (fieldName === 'os') {
                    suggestion = suggestion.replace('Windows 10', 'Windows 11');
                } else if (fieldName === 'storage') {
                    if (!suggestion.toLowerCase().includes('ssd')) {
                        suggestion += ' SSD';
                    }
                }
                
                recInput.value = suggestion;
            }
        });
    });
});
</script>

<style>
.admin-form-wrapper {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 80vh;
    padding: 40px 0;
}
.admin-form-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.08);
    padding: 32px 36px 24px 36px;
    width: 100%;
    max-width: 800px; /* Aumentado para acomodar requerimientos */
}
.admin-form-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 24px;
    color: #2d3748;
    text-align: center;
}
.form-row {
    display: flex;
    gap: 24px;
}
.form-group {
    flex: 1;
    display: flex;
    flex-direction: column;
    margin-bottom: 18px;
}
.form-group label {
    font-weight: 600;
    margin-bottom: 6px;
    color: #374151;
    display: flex;
    align-items: center;
    gap: 6px;
}
.form-control {
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 10px 12px;
    font-size: 1rem;
    background: #f9fafb;
    transition: border 0.2s;
}
.form-control:focus {
    border-color: #6366f1;
    outline: none;
    background: #fff;
}
.error {
    color: #e53e3e;
    font-size: 0.95em;
    margin-top: 2px;
}

/* Estilos para sección de requerimientos */
.requirements-section {
    margin: 24px 0;
    padding: 20px;
    background: #f8fafc;
    border-radius: 12px;
    border: 2px solid #e2e8f0;
}

.requirements-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 20px;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.requirements-group {
    margin-bottom: 24px;
    padding: 16px;
    background: white;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.requirements-subtitle {
    font-size: 1.1rem;
    font-weight: 600;
    color: #4a5568;
    margin-bottom: 16px;
    padding-bottom: 8px;
    border-bottom: 2px solid #e2e8f0;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 16px;
    margin-top: 24px;
}
.btn.btn-primary {
    background: #6366f1;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 12px 24px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}
.btn.btn-primary:hover {
    background: #4f46e5;
}
.btn.btn-secondary {
    background: #e5e7eb;
    color: #374151;
    border: none;
    border-radius: 6px;
    padding: 12px 24px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
    text-decoration: none;
}
.btn.btn-secondary:hover {
    background: #d1d5db;
}
.image-input {
    max-width: 260px;
    min-width: 0;
    width: 100%;
    box-sizing: border-box;
}

</style>
@endsection