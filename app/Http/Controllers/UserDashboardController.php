<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    /**
     * Dashboard para usuarios normales
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Verificar que sea usuario normal, si no redirigir a admin
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('user.dashboard', compact('user'));
    }
}
