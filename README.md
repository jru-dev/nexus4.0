# 🎮 NEXUS
Plataforma completa de videojuegos desarrollada en Laravel

## 📖 Descripción
NEXUS es una plataforma web que revoluciona la experiencia gaming. Los usuarios pueden comprar videojuegos, escribir reseñas, participar en foros comunitarios y gestionar su biblioteca personal. Los administradores tienen control total sobre juegos, usuarios y contenido, mientras que el sistema incluye múltiples métodos de pago y categorización avanzada.

## 🚀 Tecnologías
- **Backend**: Laravel 11
- **Frontend**: Blade Templates + CSS personalizado + JavaScript
- **Base de Datos**: MySQL/SQLite
- **Autenticación**: Laravel Breeze
- **Pagos**: Sistema de tarjetas + Yape (QR)

## 📋 Prerrequisitos
Antes de instalar, asegúrate de tener:
- PHP >= 8.1
- Composer
- Node.js >= 16
- MySQL/SQLite
- Git

## 🛠️ Instalación

### 1. Clonar el repositorio
```bash
git clone https://github.com/tu-usuario/nexus.git
cd nexus
```

### 2. Instalar dependencias de PHP
```bash
composer install
```

### 3. Instalar dependencias de Node.js
```bash
npm install
npm run build
```

### 4. Configurar el archivo de entorno
```bash
# Copiar el archivo de ejemplo
cp .env.example .env

# Generar la clave de aplicación
php artisan key:generate
```

### 5. Configurar la base de datos
Edita el archivo `.env` con los datos de tu base de datos:


```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nexus
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

### 6. Crear la base de datos

```sql
-- En tu cliente MySQL/phpMyAdmin
CREATE DATABASE nexus;
```

### 7. Ejecutar migraciones y seeders
```bash
# Ejecutar las migraciones (IMPORTANTE: en orden correcto)
php artisan migrate

# Poblar la base de datos con datos iniciales
php artisan db:seed
```

### 8. Configurar archivos públicos
```bash
# Crear enlace simbólico para archivos de storage
php artisan storage:link
```

### 9. Iniciar el servidor de desarrollo
```bash
php artisan serve
```

La aplicación estará disponible en: `http://localhost:8000`

## 👥 Usuarios de Prueba

### Administrador
- **Email**: admin@nexus.com
- **Contraseña**: admin123

### Usuarios Normales
- **Email**: juan@gmail.com
- **Contraseña**: password123
- **Email**: maria@gmail.com
- **Contraseña**: password123
- **Email**: carlos@gmail.com
- **Contraseña**: password123

## 📁 Estructura del Proyecto

```
nexus/
├── app/
│   ├── Http/
│   │   ├── Controllers/Auth/    # Controladores de autenticación
│   │   ├── Middleware/          # Middlewares personalizados
│   │   ├── AdminDashboardController.php
│   │   └── UserDashboardController.php
│   └── Models/                  # Modelos Eloquent (User, Game, Cart, etc.)
├── database/
│   ├── migrations/              # Migraciones de base de datos
│   └── seeders/                 # Datos de prueba
├── public/
│   ├── css/                     # Estilos personalizados
│   ├── js/                      # JavaScript personalizado
│   └── storage/                 # Archivos públicos (imágenes)
├── resources/views/
│   ├── auth/                    # Vistas de autenticación
│   ├── admin/                   # Vistas de administrador
│   ├── user/                    # Vistas de usuario
│   └── welcome.blade.php        # Página principal
└── routes/
    ├── web.php                  # Rutas principales
    └── auth.php                 # Rutas de autenticación
```

## 🚀 Características Implementadas

### ✅ Sistema de Autenticación
- Sistema completo de login/registro con Laravel Breeze
- Redirección automática según roles (Admin/Usuario)
- Middleware de protección de rutas
- Gestión de sesiones segura

### ✅ Gestión de Usuarios
- Roles diferenciados: Administrador y Usuario
- Campos personalizados: biografía, imagen de perfil
- Dashboards específicos por rol
- Perfiles de usuario editables

### ✅ Base de Datos Completa
- 12 migraciones estructuradas
- Modelos con relaciones establecidas
- Seeders con datos realistas de juegos
- Índices optimizados para rendimiento

### 🔄 En Desarrollo
- Tienda con carrito de compras
- Sistema de foro comunitario
- Gestión completa de reseñas
- Panel de administración avanzado
- Integración de pagos (Yape/Tarjeta)

## 🎮 Datos de Prueba Incluidos

El sistema incluye seeders que generan:
- **5 categorías** de juegos: Acción, Aventura, Supervivencia, Terror, Estrategia
- **10 juegos populares** con información completa (precios, descripciones, screenshots)
- **4 usuarios** listos para usar (1 administrador + 3 usuarios normales)
- **Datos estructurados** para testing inmediato

## 🔧 Comandos Útiles

```bash
# Limpiar todas las cachés
php artisan optimize:clear

# Refrescar base de datos completamente
php artisan migrate:fresh --seed

# Ver todas las rutas disponibles
php artisan route:list

# Regenerar clave de aplicación
php artisan key:generate

# Compilar assets para desarrollo
npm run dev

# Compilar assets para producción
npm run build
```

## 🎨 Personalización

### Cambiar Imágenes de Fondo
1. Sube tu imagen a `public/storage/`
2. Actualiza las rutas en los archivos CSS:
   - `public/css/login.css`
   - `public/css/register.css`
   - `public/css/welcome.css`

```css
/* Ejemplo en login.css */
background: url('/storage/tu-nueva-imagen.jpg');
```

### Modificar Colores del Tema
Los archivos principales para personalizar:
- **Login**: `public/css/login.css`
- **Registro**: `public/css/register.css`
- **Página principal**: `public/css/welcome.css`

## 🐛 Solución de Problemas

### Error: "Route [login] not defined"
```bash
php artisan route:clear
php artisan config:clear
php artisan serve
```

### Problemas de Permisos
```bash
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### Base de Datos no se Conecta
1. Verifica la configuración en `.env`
2. Para SQLite: `touch database/database.sqlite`
3. Ejecuta: `php artisan migrate:fresh --seed`

### Assets no se Cargan
```bash
npm run build
php artisan storage:link
```

### Error de Composer
```bash
composer clear-cache
composer install --no-cache
```

## 🤝 Contribuir al Proyecto

1. **Fork** el repositorio
2. **Crea** una rama: `git checkout -b feature/nueva-funcionalidad`
3. **Commit** tus cambios: `git commit -am 'Agregar nueva funcionalidad'`
4. **Push** a la rama: `git push origin feature/nueva-funcionalidad`
5. **Abre** un Pull Request

## 📞 Soporte Técnico

Si encuentras problemas:

1. **Revisa los logs**: `storage/logs/laravel.log`
2. **Verifica configuración**: Archivo `.env`
3. **Limpia caché**: `php artisan optimize:clear`
4. **Recrea base de datos**: `php artisan migrate:fresh --seed`

## 🏗️ Roadmap de Desarrollo

### Próximas Funcionalidades
- [ ] **Tienda completa** con carrito y checkout
- [ ] **Sistema de pagos** integrado (Yape + Tarjetas)
- [ ] **Foro interactivo** con hilos por juego
- [ ] **Sistema de reseñas** con puntuaciones
- [ ] **Panel de admin** con estadísticas
- [ ] **API REST** para integración móvil
- [ ] **Notificaciones** en tiempo real
- [ ] **Sistema de wishlist**

---

**🎮 Desarrollado con Laravel 11 | Happy Gaming!**