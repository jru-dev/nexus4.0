<?php
// app/Http/Controllers/AdminDashboardController.php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\GameCategory;
use App\Models\User;
use App\Models\Review;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminDashboardController extends Controller
{
    /**
     * Dashboard principal del admin
     */
    public function dashboard()
    {
        try {
            $stats = [
                'total_games' => Game::count(),
                'active_games' => Game::where('is_active', true)->count(),
                'hidden_games' => Game::where('is_active', false)->count(),
                'total_users' => User::where('role', 'user')->count(),
                'total_categories' => GameCategory::count(),
                'recent_games' => Game::with('category')->latest()->take(5)->get(),
                'total_reviews' => Review::count(),
                'total_orders' => Order::where('status', 'completed')->count(),
                'monthly_revenue' => Order::where('status', 'completed')
                    ->whereMonth('completed_at', now()->month)
                    ->sum('total_amount'),
                'recent_users' => User::where('role', 'user')->latest()->take(5)->get(),
            ];

            return view('admin.dashboard', compact('stats'));
        } catch (\Exception $e) {
            Log::error('Error en dashboard admin: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Error al cargar el dashboard');
        }
    }

    /**
     * GESTIÓN DE JUEGOS
     */
    public function games()
    {
        $games = Game::with('category')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        $categories = GameCategory::all();
        
        return view('admin.games', compact('games', 'categories'));
    }

    /**
     * Mostrar formulario para agregar juego
     */
    public function createGame()
    {
        $categories = GameCategory::all();
        return view('admin.games.create', compact('categories'));
    }

    /**
     * Guardar nuevo juego
     */
    public function storeGame(Request $request)
    {
        // Mostrar los datos recibidos para depuración
        // dd($request->all());
        $request->validate([
            'title' => 'required|string|max:255|unique:games,title',
            'description' => 'required|string|max:2000',
            'price' => 'required|numeric|min:0|max:999.99',
            'developer' => 'required|string|max:255',
            'publisher' => 'required|string|max:255',
            'release_date' => 'required|date',
            'category_id' => 'required|exists:game_categories,id',
            'age_rating' => 'required|in:E,E10+,T,M,AO',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'screenshots.*' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'system_requirements' => 'nullable|array',
            'system_requirements.minimum' => 'nullable|array',
            'system_requirements.recommended' => 'nullable|array',
            'system_requirements.minimum.os' => 'nullable|string|max:255',
            'system_requirements.minimum.processor' => 'nullable|string|max:255',
            'system_requirements.minimum.memory' => 'nullable|string|max:255',
            'system_requirements.minimum.graphics' => 'nullable|string|max:255',
            'system_requirements.minimum.storage' => 'nullable|string|max:255',
            'system_requirements.minimum.network' => 'nullable|string|max:255',
            'system_requirements.recommended.os' => 'nullable|string|max:255',
            'system_requirements.recommended.processor' => 'nullable|string|max:255',
            'system_requirements.recommended.memory' => 'nullable|string|max:255',
            'system_requirements.recommended.graphics' => 'nullable|string|max:255',
            'system_requirements.recommended.storage' => 'nullable|string|max:255',
            'system_requirements.recommended.network' => 'nullable|string|max:255',
        ], [
            'title.required' => 'El título es obligatorio',
            'title.unique' => 'Ya existe un juego con ese título',
            'price.required' => 'El precio es obligatorio',
            'price.numeric' => 'El precio debe ser un número válido',
            'category_id.required' => 'Debes seleccionar una categoría',
            'age_rating.required' => 'La clasificación de edad es obligatoria',
        ]);

        try {
            // Crear slug único
            $slug = Str::slug($request->title);
            $originalSlug = $slug;
            $counter = 1;
            
            while (Game::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            // Procesar imagen principal
            $imageUrl = null;
            if ($request->hasFile('image')) {
                $imageUrl = $this->uploadGameImage($request->file('image'), $slug);
            }

            // Procesar screenshots
            $screenshots = [];
            if ($request->hasFile('screenshots')) {
                foreach ($request->file('screenshots') as $index => $screenshot) {
                    $screenshotUrl = $this->uploadGameScreenshot($screenshot, $slug, $index);
                    if ($screenshotUrl) {
                        $screenshots[] = $screenshotUrl;
                    }
                }
            }

            $systemRequirements = $this->processSystemRequirements($request->system_requirements);

            // Crear el juego
            $game = Game::create([
                'title' => $request->title,
                'slug' => $slug,
                'description' => $request->description,
                'price' => $request->price,
                'developer' => $request->developer,
                'publisher' => $request->publisher,
                'release_date' => $request->release_date,
                'image_url' => $imageUrl,
                'screenshots' => $screenshots,
                'system_requirements' => $systemRequirements,
                'age_rating' => $request->age_rating,
                'category_id' => $request->category_id,
                'is_active' => true
            ]);

            Log::info("Juego creado por admin: {$game->title} (ID: {$game->id})");

            return redirect()->route('admin.games')
                ->with('success', "Juego '{$game->title}' creado exitosamente");

        } catch (\Exception $e) {
            Log::error('Error al crear juego: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Error al crear el juego: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de edición de juego
     */
    public function editGame($id)
    {
        $game = Game::with('category')->findOrFail($id);
        $categories = GameCategory::all();
        
        return view('admin.games.edit', compact('game', 'categories'));
    }

    private function processSystemRequirements($requirements)
    {
        if (!$requirements) {
            return null;
        }

        // Limpiar y estructurar los requerimientos
        $processed = [];
        
        if (isset($requirements['minimum'])) {
            $processed['minimum'] = array_filter($requirements['minimum'], function($value) {
                return !empty(trim($value));
            });
        }
        
        if (isset($requirements['recommended'])) {
            $processed['recommended'] = array_filter($requirements['recommended'], function($value) {
                return !empty(trim($value));
            });
        }
        
        // Si no hay requerimientos, retornar null
        return empty($processed) ? null : $processed;
    }

    /**
     * Actualizar juego
     */
    public function updateGame(Request $request, $id)
    {
        $game = Game::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255|unique:games,title,' . $id,
            'description' => 'required|string|max:2000',
            'price' => 'required|numeric|min:0|max:999.99',
            'developer' => 'required|string|max:255',
            'publisher' => 'required|string|max:255',
            'release_date' => 'required|date',
            'category_id' => 'required|exists:game_categories,id',
            'age_rating' => 'required|in:E,E10+,T,M,AO',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'screenshots.*' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'system_requirements' => 'nullable|array',
            'system_requirements.minimum' => 'nullable|array',
            'system_requirements.recommended' => 'nullable|array',
            'system_requirements.minimum.os' => 'nullable|string|max:255',
            'system_requirements.minimum.processor' => 'nullable|string|max:255',
            'system_requirements.minimum.memory' => 'nullable|string|max:255',
            'system_requirements.minimum.graphics' => 'nullable|string|max:255',
            'system_requirements.minimum.storage' => 'nullable|string|max:255',
            'system_requirements.minimum.network' => 'nullable|string|max:255',
            'system_requirements.recommended.os' => 'nullable|string|max:255',
            'system_requirements.recommended.processor' => 'nullable|string|max:255',
            'system_requirements.recommended.memory' => 'nullable|string|max:255',
            'system_requirements.recommended.graphics' => 'nullable|string|max:255',
            'system_requirements.recommended.storage' => 'nullable|string|max:255',
            'system_requirements.recommended.network' => 'nullable|string|max:255',
        ]);

        try {
            // Actualizar slug si cambió el título
            if ($game->title !== $request->title) {
                $slug = Str::slug($request->title);
                $originalSlug = $slug;
                $counter = 1;
                
                while (Game::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
                $game->slug = $slug;
            }

            // Actualizar imagen si se subió una nueva
            if ($request->hasFile('image')) {
                // Eliminar imagen anterior
                if ($game->image_url && file_exists(public_path($game->image_url))) {
                    unlink(public_path($game->image_url));
                }
                $game->image_url = $this->uploadGameImage($request->file('image'), $game->slug);
            }

            // Actualizar screenshots si se subieron nuevos
            if ($request->hasFile('screenshots')) {
                // Eliminar screenshots anteriores
                if ($game->screenshots) {
                    foreach ($game->screenshots as $screenshot) {
                        if (file_exists(public_path($screenshot))) {
                            unlink(public_path($screenshot));
                        }
                    }
                }
                
                $screenshots = [];
                foreach ($request->file('screenshots') as $index => $screenshot) {
                    $screenshotUrl = $this->uploadGameScreenshot($screenshot, $game->slug, $index);
                    if ($screenshotUrl) {
                        $screenshots[] = $screenshotUrl;
                    }
                }
                $game->screenshots = $screenshots;
            }

            $systemRequirements = $this->processSystemRequirements($request->system_requirements);

            // Actualizar otros campos
            $game->update([
                'title' => $request->title,
                'description' => $request->description,
                'price' => $request->price,
                'developer' => $request->developer,
                'publisher' => $request->publisher,
                'release_date' => $request->release_date,
                'category_id' => $request->category_id,
                'age_rating' => $request->age_rating,
                'system_requirements' =>$systemRequirements 
            ]);

            Log::info("Juego actualizado por admin: {$game->title} (ID: {$game->id})");

            return redirect()->route('admin.games')
                ->with('success', "Juego '{$game->title}' actualizado exitosamente");

        } catch (\Exception $e) {
            Log::error('Error al actualizar juego: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Error al actualizar el juego: ' . $e->getMessage());
        }
    }

    /**
     * Alternar visibilidad del juego (activar/ocultar)
     */
    public function toggleGame($id)
    {
        try {
            $game = Game::findOrFail($id);
            $game->is_active = !$game->is_active;
            $game->save();

            $status = $game->is_active ? 'activado' : 'ocultado';
            
            Log::info("Juego {$status} por admin: {$game->title} (ID: {$game->id})");

            return response()->json([
                'success' => true,
                'message' => "Juego '{$game->title}' {$status} exitosamente",
                'is_active' => $game->is_active
            ]);

        } catch (\Exception $e) {
            Log::error('Error al cambiar estado del juego: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado del juego'
            ], 500);
        }
    }

    /**
     * Eliminar juego
     */
    public function destroyGame($id)
    {
        try {
            $game = Game::findOrFail($id);
            $gameTitle = $game->title;

            // Verificar si tiene órdenes asociadas
            if ($game->orderItems()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el juego porque tiene órdenes asociadas'
                ], 400);
            }

            // Eliminar archivos de imagen
            if ($game->image_url && file_exists(public_path($game->image_url))) {
                unlink(public_path($game->image_url));
            }

            if ($game->screenshots) {
                foreach ($game->screenshots as $screenshot) {
                    if (file_exists(public_path($screenshot))) {
                        unlink(public_path($screenshot));
                    }
                }
            }

            $game->delete();

            Log::info("Juego eliminado por admin: {$gameTitle} (ID: {$id})");

            return response()->json([
                'success' => true,
                'message' => "Juego '{$gameTitle}' eliminado exitosamente"
            ]);

        } catch (\Exception $e) {
            Log::error('Error al eliminar juego: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el juego'
            ], 500);
        }
    }

    /**
     * GESTIÓN DE USUARIOS
     */
    public function users()
    {
        $users = User::where('role', 'user')
            ->withCount(['library', 'reviews', 'orders'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.users', compact('users'));
    }

    /**
     * Ver detalles de un usuario
     */
    public function showUser($id)
    {
        $user = User::with(['library.game', 'reviews.game', 'orders.items'])
            ->findOrFail($id);

        $stats = [
            'total_games' => $user->library->count(),
            'total_reviews' => $user->reviews->count(),
            'total_orders' => $user->orders->count(),
            'total_spent' => $user->orders->where('status', 'completed')->sum('total_amount'),
            'hours_played' => $user->library->sum('hours_played')
        ];

        return view('admin.users.show', compact('user', 'stats'));
    }

    /**
     * GESTIÓN DE RESEÑAS
     */
    public function reviews()
    {
        $reviews = Review::with(['user', 'game'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.reviews', compact('reviews'));
    }

    /**
     * Aprobar/desaprobar reseña
     */
    public function toggleReview($id)
    {
        try {
            $review = Review::findOrFail($id);
            $review->is_approved = !$review->is_approved;
            $review->save();

            $status = $review->is_approved ? 'aprobada' : 'desaprobada';

            return response()->json([
                'success' => true,
                'message' => "Reseña {$status} exitosamente",
                'is_approved' => $review->is_approved
            ]);

        } catch (\Exception $e) {
            Log::error('Error al cambiar estado de reseña: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado de la reseña'
            ], 500);
        }
    }

    /**
     * Eliminar reseña
     */
    public function destroyReview($id)
    {
        try {
            $review = Review::findOrFail($id);
            $gameTitle = $review->game->title;
            $userName = $review->user->name;
            
            $review->delete();

            Log::info("Reseña eliminada por admin: {$userName} - {$gameTitle}");

            return response()->json([
                'success' => true,
                'message' => 'Reseña eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al eliminar reseña: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la reseña'
            ], 500);
        }
    }

    /**
     * MÉTODOS AUXILIARES
     */
    private function uploadGameImage($file, $slug)
    {
        $imageName = $slug . '_cover.' . $file->getClientOriginalExtension();
        $imagePath = public_path('images/games/covers');
        
        if (!file_exists($imagePath)) {
            mkdir($imagePath, 0755, true);
        }
        
        $file->move($imagePath, $imageName);
        return 'images/games/covers/' . $imageName;
    }

    private function uploadGameScreenshot($file, $slug, $index)
    {
        $imageName = $slug . '_screenshot_' . ($index + 1) . '.' . $file->getClientOriginalExtension();
        $imagePath = public_path('images/games/screenshots/' . $slug);
        
        if (!file_exists($imagePath)) {
            mkdir($imagePath, 0755, true);
        }
        
        $file->move($imagePath, $imageName);
        return 'images/games/screenshots/' . $slug . '/' . $imageName;
    }

    /**
     * GESTIÓN DE CATEGORÍAS
     */
    public function categories()
    {
        $categories = GameCategory::withCount('games')
            ->orderBy('name')
            ->get();

        return view('admin.categories', compact('categories'));
    }

    /**
     * Crear nueva categoría
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:game_categories,name',
            'description' => 'nullable|string|max:500'
        ]);

        try {
            $category = GameCategory::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description
            ]);

            return response()->json([
                'success' => true,
                'message' => "Categoría '{$category->name}' creada exitosamente"
            ]);

        } catch (\Exception $e) {
            Log::error('Error al crear categoría: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la categoría'
            ], 500);
        }
    }

    /**
     * Eliminar categoría
     */
    public function destroyCategory($id)
    {
        try {
            $category = GameCategory::findOrFail($id);
            
            if ($category->games()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar la categoría porque tiene juegos asociados'
                ], 400);
            }

            $categoryName = $category->name;
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => "Categoría '{$categoryName}' eliminada exitosamente"
            ]);

        } catch (\Exception $e) {
            Log::error('Error al eliminar categoría: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la categoría'
            ], 500);
        }
    }
}