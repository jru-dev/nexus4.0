<?php
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\HomeController; 
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CartController; 
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ForumController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\NewsController;

// Ruta principal - Index de la tienda
Route::get('/', [HomeController::class, 'index'])->name('home');

// Ruta para categorías
Route::get('/categoria/{slug}', [HomeController::class, 'category'])->name('category.show');

// Ruta para vista individual de juegos
Route::get('/juego/{slug}', [HomeController::class, 'show'])->name('game.show');

// Incluir rutas de autenticación de Breeze
require __DIR__.'/auth.php';

// Rutas de administración - Solo para admins
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard principal
    Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->name('dashboard');
    
    // Gestión de juegos
    Route::get('/games', [AdminDashboardController::class, 'games'])->name('games');
    Route::get('/games/create', [AdminDashboardController::class, 'createGame'])->name('games.create');
    Route::post('/games', [AdminDashboardController::class, 'storeGame'])->name('games.store');
    Route::get('/games/{id}/edit', [AdminDashboardController::class, 'editGame'])->name('games.edit');
    Route::put('/games/{id}', [AdminDashboardController::class, 'updateGame'])->name('games.update');
    Route::post('/games/{id}/toggle', [AdminDashboardController::class, 'toggleGame'])->name('games.toggle');
    Route::delete('/games/{id}', [AdminDashboardController::class, 'destroyGame'])->name('games.destroy');
    
    // Gestión de usuarios
    Route::get('/users', [AdminDashboardController::class, 'users'])->name('users');
    Route::get('/users/{id}', [AdminDashboardController::class, 'showUser'])->name('users.show');
    
    // Gestión de reseñas
    Route::get('/reviews', [AdminDashboardController::class, 'reviews'])->name('reviews');
    Route::post('/reviews/{id}/toggle', [AdminDashboardController::class, 'toggleReview'])->name('reviews.toggle');
    Route::delete('/reviews/{id}', [AdminDashboardController::class, 'destroyReview'])->name('reviews.destroy');
    
    // Gestión de categorías
    Route::get('/categories', [AdminDashboardController::class, 'categories'])->name('categories');
    Route::post('/categories', [AdminDashboardController::class, 'storeCategory'])->name('categories.store');
    Route::delete('/categories/{id}', [AdminDashboardController::class, 'destroyCategory'])->name('categories.destroy');

    // Gestión de noticias
    Route::resource('news', NewsController::class);
});

// Dashboard principal - redirige según el rol
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('home');
    })->name('dashboard');
});

// Rutas de perfil Breeze (mantener)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// RUTAS DE PERFIL PERSONALIZADO
Route::middleware('auth')->group(function () {
    Route::get('/mi-perfil', [UserProfileController::class, 'index'])->name('profile.index');
    Route::get('/mi-perfil/editar', [UserProfileController::class, 'edit'])->name('profile.edit.custom');
    Route::patch('/mi-perfil', [UserProfileController::class, 'update'])->name('profile.update.custom');
    Route::delete('/mi-perfil/foto', [UserProfileController::class, 'removeProfileImage'])->name('profile.remove-image');
});

// Biblioteca del usuario
Route::middleware('auth')->group(function () {
    Route::get('/biblioteca', [LibraryController::class, 'index'])->name('library.index');
});

// RUTAS DEL CARRITO - CORREGIDAS
Route::middleware('auth')->prefix('carrito')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/agregar/{game}', [CartController::class, 'add'])->name('add');
    Route::delete('/eliminar/{item}', [CartController::class, 'remove'])->name('remove');
    Route::post('/limpiar', [CartController::class, 'clear'])->name('clear');
    Route::post('/checkout', [CartController::class, 'checkout'])->name('checkout');
    
    // APIs para AJAX
    Route::get('/contador', [CartController::class, 'getCount'])->name('count');
    Route::get('/resumen', [CartController::class, 'getSummary'])->name('summary');
    Route::patch('/actualizar/{item}', [CartController::class, 'updateQuantity'])->name('update');
    Route::get('/verificar/{game}', [CartController::class, 'isInCart'])->name('check');
    Route::get('/disponibilidad/{game}', [CartController::class, 'checkAvailability'])->name('availability');
});

// RUTAS DE PAGO ACTUALIZADAS CON YAPE
Route::middleware('auth')->prefix('pagar')->name('payment.')->group(function () {
    // Pago individual de un juego
    Route::get('/{game}', [PaymentController::class, 'showPaymentForm'])->name('form');
    Route::post('/procesar', [PaymentController::class, 'processPayment'])->name('process');
    
    // RUTAS DEL CARRITO
    Route::get('/carrito/formulario', [PaymentController::class, 'showCartPaymentForm'])->name('cart.form');
    Route::post('/carrito/procesar', [PaymentController::class, 'processCartPayment'])->name('cart.process');
    
    // *** NUEVAS RUTAS YAPE ***
    Route::post('/yape/iniciar', [PaymentController::class, 'initiateYapePayment'])->name('yape.initiate');
    Route::get('/yape/{orderId}', [PaymentController::class, 'showYapePayment'])->name('yape.show');
    Route::post('/yape/{orderId}/confirmar', [PaymentController::class, 'confirmYapePayment'])->name('yape.confirm');
    Route::get('/yape/{orderId}/estado', [PaymentController::class, 'checkYapeStatus'])->name('yape.status');
    Route::post('/yape/{orderId}/cancelar', [PaymentController::class, 'cancelYapeOrder'])->name('yape.cancel');
    
    // Páginas de resultado
    Route::get('/exitoso/{order}', [PaymentController::class, 'paymentSuccess'])->name('success');
    Route::get('/fallido', function() {
        return view('payment.failed');
    })->name('failed');

    // APIs para el frontend de pago
    Route::get('/api/tarjetas/{bank}', [PaymentController::class, 'getCardsByBank'])->name('api.cards.by-bank');
    Route::post('/api/verificar-fondos', [PaymentController::class, 'checkCardFunds'])->name('api.check-funds');
});

// RUTAS DE RESEÑAS QUE REQUIEREN AUTENTICACIÓN
Route::middleware('auth')->prefix('api/reviews')->name('reviews.')->group(function () {
    // Crear una nueva reseña
    Route::post('/game/{game}', [App\Http\Controllers\ReviewController::class, 'store'])->name('store');
    
    // Actualizar una reseña existente
    Route::put('/{review}', [App\Http\Controllers\ReviewController::class, 'update'])->name('update');
    
    // Eliminar una reseña
    Route::delete('/{review}', [App\Http\Controllers\ReviewController::class, 'destroy'])->name('destroy');
    
    // Marcar reseña como útil
    Route::post('/{review}/helpful', [App\Http\Controllers\ReviewController::class, 'markHelpful'])->name('helpful');
});

// RUTAS PÚBLICAS DE RESEÑAS (sin autenticación requerida)
Route::prefix('api/reviews')->name('reviews.')->group(function () {
    // Ver reseñas de un juego (público)
    Route::get('/game/{game}', [App\Http\Controllers\ReviewController::class, 'index'])->name('game.index');
    
    // Obtener estadísticas de reseñas de un juego (público)
    Route::get('/game/{game}/stats', [App\Http\Controllers\ReviewController::class, 'getStats'])->name('game.stats');
});

// Rutas del Foro - Solo para usuarios autenticados
Route::middleware('auth')->prefix('comunidad')->name('forum.')->group(function () {
    Route::get('/', [ForumController::class, 'index'])->name('index');
    Route::get('/crear', [ForumController::class, 'create'])->name('create');
    Route::post('/crear', [ForumController::class, 'store'])->name('store');
    Route::get('/post/{id}', [ForumController::class, 'show'])->name('show');
    Route::post('/post/{id}/responder', [ForumController::class, 'reply'])->name('reply');
    Route::delete('/post/{id}', [ForumController::class, 'destroy'])->name('destroy');
});

// Rutas de búsqueda
Route::get('/buscar', [HomeController::class, 'search'])->name('search');
Route::get('/api/autocomplete', [HomeController::class, 'autocomplete'])->name('autocomplete');

// Helper para detectar ruta activa
function isActiveRoute($routeName) {
    return request()->routeIs($routeName) ? 'active' : '';
}

// Rutas API adicionales para mejorar la experiencia
Route::middleware('auth')->prefix('api')->name('api.')->group(function () {
    // Verificar estado de juegos
    Route::get('/juego/{game}/estado', function($game) {
        $user = auth()->user();
        return response()->json([
            'owned' => $user->ownsGame($game),
            'in_cart' => $user->getActiveCart()->items()->where('game_id', $game)->exists()
        ]);
    })->name('game.status');
    
    // Estadísticas de usuario
    Route::get('/usuario/estadisticas', function() {
        $user = auth()->user();
        $cart = $user->getActiveCart();
        $library = $user->library;
        
        return response()->json([
            'cart_count' => $cart->getTotalItems(),
            'library_count' => $library->count(),
            'total_spent' => $library->sum('purchase_price')
        ]);
    })->name('user.stats');
});