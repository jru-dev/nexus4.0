<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Models\UserLibrary;
use App\Models\Review;
use App\Models\Order;

class UserProfileController extends Controller
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
        
        // Obtener reseñas del usuario
        $userReviews = Review::with('game')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Obtener órdenes recientes
        $recentOrders = Order::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
        
        // Estadísticas del perfil
        $stats = [
            'total_games' => $libraryGames->count(),
            'hours_played' => $libraryGames->sum('hours_played'),
            'favorites' => $libraryGames->where('is_favorite', true)->count(),
            'completed' => $libraryGames->where('status', 'completed')->count(),
            'total_reviews' => $userReviews->count(),
            'total_orders' => $recentOrders->count(),
        ];
        
        return view('profile.index', compact('user', 'libraryGames', 'stats', 'userReviews', 'recentOrders'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit-custom', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        // Validación
        $request->validate([
            'name' => 'required|string|max:255|unique:users,name,' . $user->id,
            'bio' => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048', // 2MB máximo
        ], [
            'name.required' => 'El nombre es obligatorio',
            'name.unique' => 'Este nombre ya está en uso',
            'name.max' => 'El nombre no puede tener más de 255 caracteres',
            'bio.max' => 'La biografía no puede tener más de 500 caracteres',
            'profile_image.image' => 'El archivo debe ser una imagen',
            'profile_image.mimes' => 'La imagen debe ser JPG, PNG o WEBP',
            'profile_image.max' => 'La imagen no puede ser mayor a 2MB',
        ]);

        // Datos a actualizar
        $userData = [
            'name' => $request->name,
            'bio' => $request->bio,
        ];

        // Manejar la subida de imagen - NUEVO MÉTODO
        if ($request->hasFile('profile_image')) {
            // Eliminar imagen anterior si existe
            if ($user->profile_image && File::exists(public_path($user->profile_image))) {
                File::delete(public_path($user->profile_image));
            }

            // Subir nueva imagen a public/images/profiles/
            $file = $request->file('profile_image');
            
            // Generar nombre único para el archivo
            $fileName = 'profile_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Crear la carpeta si no existe
            $destinationPath = public_path('images/profiles');
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }
            
            // Mover archivo a public/images/profiles/
            $file->move($destinationPath, $fileName);
            
            // Guardar la ruta relativa en la base de datos
            $userData['profile_image'] = 'images/profiles/' . $fileName;
        }

        // Actualizar usuario
        $user->update($userData);

        return redirect()->route('profile.index')->with('success', '¡Perfil actualizado correctamente!');
    }

    /**
     * Método helper para obtener la URL de la foto de perfil
     */
    public function getProfileImageUrl()
    {
        $user = Auth::user();
        
        if ($user->profile_image && File::exists(public_path($user->profile_image))) {
            return asset($user->profile_image);
        }
        
        return null; // Retorna null si no hay imagen
    }

    /**
     * Método para eliminar foto de perfil
     */
    public function removeProfileImage()
    {
        $user = Auth::user();
        
        if ($user->profile_image && File::exists(public_path($user->profile_image))) {
            // Eliminar archivo
            File::delete(public_path($user->profile_image));
            
            // Limpiar campo en base de datos
            $user->update(['profile_image' => null]);
            
            return redirect()->route('profile.edit.custom')->with('success', 'Foto de perfil eliminada correctamente');
        }
        
        return redirect()->route('profile.edit.custom')->with('error', 'No hay foto de perfil para eliminar');
    }
}