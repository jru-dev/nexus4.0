<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Game;
use App\Models\UserLibrary;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    /**
     * Mostrar reseñas de un juego específico - PÚBLICO (sin autenticación)
     */
    public function index(Game $game, Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);

            // Obtener reseñas paginadas
            $reviews = $game->reviews()
                           ->with('user')
                           ->approved()
                           ->orderBy('created_at', 'desc')
                           ->paginate($perPage);

            $user = Auth::user();
            $canReview = false;
            $userReview = null;

            // Solo verificar si hay usuario logueado
            if ($user) {
                $canReview = Review::canUserReview($user->id, $game->id);
                $userReview = Review::where('user_id', $user->id)
                                   ->where('game_id', $game->id)
                                   ->first();
            }

            return response()->json([
                'success' => true,
                'reviews' => $reviews,
                'can_review' => $canReview,
                'user_review' => $userReview,
                'stats' => [
                    'total' => $game->reviews()->approved()->count(),
                    'recommended' => $game->reviews()->approved()->where('recommendation', 'recommended')->count(),
                    'not_recommended' => $game->reviews()->approved()->where('recommendation', 'not_recommended')->count(),
                    'average_rating' => round($game->reviews()->approved()->avg('rating') ?? 0, 1),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al cargar reseñas: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar las reseñas',
                'reviews' => ['data' => []],
                'can_review' => false,
                'user_review' => null,
                'stats' => [
                    'total' => 0,
                    'recommended' => 0,
                    'not_recommended' => 0,
                    'average_rating' => 0,
                ]
            ]);
        }
    }

    /**
     * Crear una nueva reseña - REQUIERE AUTENTICACIÓN
     */
    public function store(Request $request, Game $game)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión para escribir una reseña'
            ], 401);
        }

        $user = Auth::user();

        // Verificar que el usuario puede escribir una reseña
        if (!Review::canUserReview($user->id, $game->id)) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes escribir una reseña para este juego. Debes tenerlo en tu biblioteca y no haber escrito una reseña anteriormente.'
            ], 403);
        }

        // Validar datos
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:2000',
            'recommendation' => 'required|in:recommended,not_recommended'
        ], [
            'rating.required' => 'La calificación es obligatoria',
            'rating.min' => 'La calificación mínima es 1 estrella',
            'rating.max' => 'La calificación máxima es 5 estrellas',
            'title.required' => 'El título es obligatorio',
            'title.max' => 'El título no puede tener más de 255 caracteres',
            'content.required' => 'El contenido es obligatorio',
            'content.max' => 'El contenido no puede tener más de 2000 caracteres',
            'recommendation.required' => 'Debes indicar si recomiendas el juego o no',
            'recommendation.in' => 'La recomendación debe ser "recommended" o "not_recommended"'
        ]);

        try {
            // Crear la reseña
            $review = Review::create([
                'user_id' => $user->id,
                'game_id' => $game->id,
                'rating' => $request->rating,
                'title' => $request->title,
                'content' => $request->content,
                'recommendation' => $request->recommendation,
                'is_approved' => true, // Auto-aprobar por ahora
                'helpful_votes' => 0
            ]);

            Log::info("Nueva reseña creada: Usuario {$user->id} reseñó {$game->title}");

            return response()->json([
                'success' => true,
                'message' => '¡Reseña publicada exitosamente!',
                'review' => $review->load('user')
            ]);

        } catch (\Exception $e) {
            Log::error('Error al crear reseña: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al publicar la reseña. Inténtalo de nuevo.'
            ], 500);
        }
    }

    /**
     * Actualizar una reseña existente
     */
    public function update(Request $request, Review $review)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión'
            ], 401);
        }

        $user = Auth::user();

        // Verificar que la reseña pertenece al usuario
        if ($review->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para editar esta reseña'
            ], 403);
        }

        // Validar datos
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:2000',
            'recommendation' => 'required|in:recommended,not_recommended'
        ]);

        try {
            $review->update([
                'rating' => $request->rating,
                'title' => $request->title,
                'content' => $request->content,
                'recommendation' => $request->recommendation,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reseña actualizada exitosamente',
                'review' => $review->fresh()->load('user')
            ]);

        } catch (\Exception $e) {
            Log::error('Error al actualizar reseña: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la reseña'
            ], 500);
        }
    }

    /**
     * Eliminar una reseña
     */
    public function destroy(Review $review)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión'
            ], 401);
        }

        $user = Auth::user();

        // Verificar permisos (el autor o un admin)
        if ($review->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para eliminar esta reseña'
            ], 403);
        }

        try {
            $review->delete();

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
     * Marcar una reseña como útil
     */
    public function markHelpful(Review $review)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión para marcar como útil'
            ], 401);
        }

        $user = Auth::user();

        if ($review->user_id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes marcar tu propia reseña como útil'
            ], 400);
        }

        try {
            $review->increment('helpful_votes');

            return response()->json([
                'success' => true,
                'message' => 'Marcado como útil',
                'helpful_votes' => $review->fresh()->helpful_votes
            ]);

        } catch (\Exception $e) {
            Log::error('Error al marcar reseña como útil: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la acción'
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de reseñas para un juego - PÚBLICO
     */
    public function getStats(Game $game)
    {
        try {
            $total = $game->reviews()->approved()->count();
            $recommended = $game->reviews()->approved()->where('recommendation', 'recommended')->count();
            $notRecommended = $game->reviews()->approved()->where('recommendation', 'not_recommended')->count();
            $averageRating = round($game->reviews()->approved()->avg('rating') ?? 0, 1);

            $ratingDistribution = [];
            for ($i = 1; $i <= 5; $i++) {
                $count = $game->reviews()->approved()->where('rating', $i)->count();
                $ratingDistribution[$i] = [
                    'count' => $count,
                    'percentage' => $total > 0 ? round(($count / $total) * 100, 1) : 0
                ];
            }

            return response()->json([
                'success' => true,
                'total_reviews' => $total,
                'recommended' => $recommended,
                'not_recommended' => $notRecommended,
                'recommendation_percentage' => $total > 0 ? round(($recommended / $total) * 100, 1) : 0,
                'average_rating' => $averageRating,
                'rating_distribution' => $ratingDistribution
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener estadísticas: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'total_reviews' => 0,
                'recommended' => 0,
                'not_recommended' => 0,
                'recommendation_percentage' => 0,
                'average_rating' => 0,
                'rating_distribution' => []
            ]);
        }
    }
}