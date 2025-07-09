<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;           
use App\Models\GameCategory;  
use App\Models\News; 
use App\Models\Review; // Agregar esta importación

class HomeController extends Controller
{
    /**
     * Mostrar la página principal de la tienda
     */
    public function index()
    {
        // Traer juegos destacados para el carrusel hero
        $carouselGames = Game::with('category')->take(6)->get();
        
        // Traer solo algunas ofertas especiales
        $specialOffers = Game::with('category')->inRandomOrder()->take(3)->get();
        
        // Traer categorías
        $categories = GameCategory::all();

        // Traer noticias más recientes
        $news = News::orderBy('date', 'desc')->take(3)->get();
        
        // NUEVA LÍNEA: Traer 3 reseñas aleatorias con usuario y juego
        $quickReviews = Review::with(['user', 'game'])
            ->where('is_approved', true)
            ->inRandomOrder()
            ->take(3)
            ->get();
        
        return view('home.index', compact(
            'carouselGames', 
            'specialOffers',
            'categories',
            'news',
            'quickReviews' // Agregar esta variable
        ));
    }

    /**
     * Buscar juegos
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $categoryFilter = $request->get('category', 'all');
        $sortBy = $request->get('sort', 'relevance');
        
        // Si no hay búsqueda, redirigir al home
        if (empty(trim($query))) {
            return redirect()->route('home')->with('error', 'Por favor ingresa un término de búsqueda');
        }
        
        // Query base
        $gamesQuery = Game::with('category')->where('is_active', true);
        
        // Búsqueda por título, descripción o desarrollador
        $gamesQuery->where(function($q) use ($query) {
            $q->where('title', 'LIKE', "%{$query}%")
              ->orWhere('description', 'LIKE', "%{$query}%")
              ->orWhere('developer', 'LIKE', "%{$query}%")
              ->orWhere('publisher', 'LIKE', "%{$query}%");
        });
        
        // Filtro por categoría
        if ($categoryFilter !== 'all') {
            $gamesQuery->where('category_id', $categoryFilter);
        }
        
        // Ordenamiento
        switch ($sortBy) {
            case 'price_asc':
                $gamesQuery->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $gamesQuery->orderBy('price', 'desc');
                break;
            case 'newest':
                $gamesQuery->orderBy('created_at', 'desc');
                break;
            case 'name_asc':
                $gamesQuery->orderBy('title', 'asc');
                break;
            case 'name_desc':
                $gamesQuery->orderBy('title', 'desc');
                break;
            case 'relevance':
            default:
                // Ordenar por relevancia (juegos que empiecen con la búsqueda primero)
                $gamesQuery->orderByRaw("CASE 
                    WHEN title LIKE '{$query}%' THEN 1 
                    WHEN title LIKE '%{$query}%' THEN 2 
                    ELSE 3 
                END")
                ->orderBy('title', 'asc');
        }
        
        $games = $gamesQuery->get();
        $categories = GameCategory::all();
        
        // Sugerencias de búsqueda si no hay resultados
        $suggestions = [];
        if ($games->isEmpty()) {
            $suggestions = Game::where('is_active', true)
                ->where(function($q) use ($query) {
                    $searchWords = explode(' ', $query);
                    foreach ($searchWords as $word) {
                        if (strlen($word) > 2) {
                            $q->orWhere('title', 'LIKE', "%{$word}%")
                              ->orWhere('developer', 'LIKE', "%{$word}%");
                        }
                    }
                })
                ->take(5)
                ->get();
        }
        
        return view('search.results', compact(
            'games', 
            'categories', 
            'query', 
            'categoryFilter', 
            'sortBy',
            'suggestions'
        ));
    }

    /**
     * Mostrar juegos por categoría
     */
    public function category($slug, Request $request)
    {
        // Buscar la categoría por slug
        $category = GameCategory::where('slug', $slug)->first();
        
        if (!$category) {
            abort(404, 'Categoría no encontrada');
        }
        
        // Query base para juegos de esa categoría
        $query = Game::with('category')->where('category_id', $category->id)->where('is_active', true);
        
        // Aplicar ordenamiento según el parámetro
        $sort = $request->get('sort', 'popular');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'popular':
            default:
                $query->orderBy('title', 'asc'); // Por defecto alfabético
        }
        
        $games = $query->get();
        
        // Traer todas las categorías para el menú
        $categories = GameCategory::all();
        
        return view('games.by-category', compact('category', 'games', 'categories', 'sort'));
    }

    /**
     * Mostrar página individual del juego
     */
    public function show($slug)
    {
        // Buscar el juego por slug
        $game = Game::with('category')->where('slug', $slug)->first();
        
        if (!$game) {
            abort(404, 'Juego no encontrado');
        }
        
        // Juegos relacionados (misma categoría)
        $relatedGames = Game::with('category')
            ->where('category_id', $game->category_id)
            ->where('id', '!=', $game->id)
            ->take(4)
            ->get();
        
        return view('games.show', compact('game', 'relatedGames'));
    }

    /**
     * API para autocompletado de búsqueda
     */
    public function autocomplete(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        $suggestions = Game::where('is_active', true)
            ->where('title', 'LIKE', "%{$query}%")
            ->orderByRaw("CASE WHEN title LIKE '{$query}%' THEN 1 ELSE 2 END")
            ->take(8)
            ->get(['id', 'title', 'slug', 'image_url', 'price'])
            ->map(function($game) {
                return [
                    'id' => $game->id,
                    'title' => $game->title,
                    'slug' => $game->slug,
                    'image' => asset($game->image_url),
                    'price' => 'S/ ' . number_format($game->price, 2),
                    'url' => route('game.show', $game->slug)
                ];
            });
        
        return response()->json($suggestions);
    }
}