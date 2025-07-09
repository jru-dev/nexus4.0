<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\GameCategory;
use App\Models\User;

class AdminController extends Controller
{
    /**
     * Dashboard principal del admin
     */
    public function dashboard()
    {
        $stats = [
            'total_games' => Game::count(),
            'active_games' => Game::where('is_active', true)->count(),
            'hidden_games' => Game::where('is_active', false)->count(),
            'total_users' => User::where('role', 'user')->count(),
            'total_categories' => GameCategory::count(),
            'recent_games' => Game::latest()->take(5)->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Mostrar lista de juegos para gestionar
     */
    public function games()
    {
        $games = Game::with('category')->orderBy('created_at', 'desc')->get();
        $categories = GameCategory::all();
        
        return view('admin.games', compact('games', 'categories'));
    }
}