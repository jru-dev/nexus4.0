<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserLibrary;

class LibraryController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Si no está autenticado, redirigir al login
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Obtener juegos de la biblioteca del usuario
        $libraryGames = UserLibrary::with('game.category')
            ->where('user_id', $user->id)
            ->orderBy('purchased_at', 'desc')
            ->get();
        
        // Estadísticas
        $stats = [
            'total_games' => $libraryGames->count(),
            'hours_played' => $libraryGames->sum('hours_played'),
            'favorites' => $libraryGames->where('is_favorite', true)->count(),
            'completed' => $libraryGames->where('status', 'completed')->count(),
            'playing' => $libraryGames->where('status', 'playing')->count(),
        ];
        
        return view('library.index', compact('user', 'libraryGames', 'stats'));
    }
}