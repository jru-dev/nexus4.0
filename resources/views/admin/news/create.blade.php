@extends('layouts.admin')

@section('content')
<div class="admin-form-wrapper" style="background: linear-gradient(135deg, #7c3aed 0%, #a78bfa 100%); min-height: 100vh; padding-top: 40px; padding-bottom: 40px;">
    <div class="admin-form-card">
        <div class="form-header">
            <h1 class="admin-form-title">Nueva Noticia</h1>
            <p class="form-subtitle">Crea una nueva noticia para mantener informados a los usuarios</p>
        </div>

        {{-- Mostrar errores generales --}}
        @if ($errors->any())
            <div class="alert alert-danger" style="margin-bottom: 24px;">
                <div class="alert-icon">⚠️</div>
                <div class="alert-content">
                    <strong>Por favor corrige los siguientes errores:</strong>
                    <ul style="margin: 8px 0 0 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data" class="news-form">
            @include('admin.news.form')
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    Guardar Noticia
                </button>
                <a href="{{ route('admin.news.index') }}" class="btn btn-secondary">
                     Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<style>
.admin-form-wrapper {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 100vh;
    padding: 40px 20px;
}

.admin-form-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.12);
    padding: 40px;
    width: 100%;
    max-width: 800px;
}

.form-header {
    text-align: center;
    margin-bottom: 32px;
}

.admin-form-title {
    font-size: 2.2rem;
    font-weight: 700;
    margin-bottom: 8px;
    color: #1a202c;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
}

.form-subtitle {
    font-size: 1.1rem;
    color: #64748b;
    margin: 0;
    font-weight: 400;
}

.alert {
    padding: 20px;
    border-radius: 12px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    font-size: 0.95rem;
}

.alert-danger {
    background: #fef2f2;
    border: 2px solid #fecaca;
    color: #991b1b;
}

.alert-icon {
    font-size: 1.2rem;
    flex-shrink: 0;
    margin-top: 2px;
}

.alert-content {
    flex: 1;
}

.alert-content strong {
    display: block;
    margin-bottom: 4px;
}

.alert-content ul {
    list-style-type: disc;
}

.alert-content li {
    margin-bottom: 4px;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 16px;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 2px solid #f3f4f6;
}

.btn {
    padding: 14px 28px;
    border-radius: 10px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 1rem;
}

.btn-primary {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.btn-primary:hover {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(99, 102, 241, 0.4);
}

.btn-secondary {
    background: #f8fafc;
    color: #64748b;
    border: 2px solid #e2e8f0;
}

.btn-secondary:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
    transform: translateY(-1px);
}

/* Responsive */
@media (max-width: 768px) {
    .admin-form-wrapper {
        padding: 20px 16px;
    }
    
    .admin-form-card {
        padding: 24px;
    }
    
    .admin-form-title {
        font-size: 1.8rem;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        justify-content: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Efecto de fade-in para la forma
    const formCard = document.querySelector('.admin-form-card');
    formCard.style.opacity = '0';
    formCard.style.transform = 'translateY(20px)';
    
    setTimeout(() => {
        formCard.style.transition = 'all 0.6s ease-out';
        formCard.style.opacity = '1';
        formCard.style.transform = 'translateY(0)';
    }, 100);
    
    // Validación antes de enviar
    const form = document.querySelector('.news-form');
    form.addEventListener('submit', function(e) {
        const title = document.getElementById('title').value.trim();
        const content = document.getElementById('content').value.trim();
        const date = document.getElementById('date').value;
        
        if (!title || !content || !date) {
            e.preventDefault();
            alert('Por favor completa todos los campos obligatorios.');
            return false;
        }
        
        if (content.length < 20) {
            e.preventDefault();
            alert('El contenido debe tener al menos 20 caracteres.');
            return false;
        }
        
        // Mostrar loading en el botón
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '⏳ Guardando...';
        submitBtn.disabled = true;
    });
});
</script>
@endsection