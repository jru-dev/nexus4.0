<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ForumPost;
use App\Models\ForumReply;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ForumController extends Controller
{
    /**
     * Mostrar la página principal del foro
     */
    public function index()
    {
        try {
            // Obtener posts del foro ordenados por actividad reciente
            $posts = ForumPost::with(['user', 'replies.user'])
                ->where('game_id', null) // Solo posts generales, no específicos de juegos
                ->orderBy('last_activity', 'desc')
                ->paginate(10);

            return view('forum.index', compact('posts'));

        } catch (\Exception $e) {
            Log::error('Error al cargar foro: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Error al cargar el foro');
        }
    }

    /**
     * Mostrar formulario para crear nuevo post
     */
    public function create()
    {
        return view('forum.create');
    }

    /**
     * Guardar nuevo post del foro
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:5000',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048'
        ], [
            'title.required' => 'El título es obligatorio',
            'title.max' => 'El título no puede tener más de 255 caracteres',
            'content.required' => 'El contenido es obligatorio',
            'content.max' => 'El contenido no puede tener más de 5000 caracteres',
            'image.image' => 'El archivo debe ser una imagen',
            'image.mimes' => 'La imagen debe ser JPG, PNG o WEBP',
            'image.max' => 'La imagen no puede ser mayor a 2MB'
        ]);

        try {
            $user = Auth::user();
            $imageUrl = null;

            // Procesar imagen si se subió una
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = 'forum_' . $user->id . '_' . time() . '.' . $image->getClientOriginalExtension();
                
                // Crear directorio si no existe
                $imagePath = public_path('images/forum');
                if (!file_exists($imagePath)) {
                    mkdir($imagePath, 0755, true);
                }
                
                // Mover imagen
                $image->move($imagePath, $imageName);
                $imageUrl = 'images/forum/' . $imageName;
            }

            // Crear el post
            ForumPost::create([
                'user_id' => $user->id,
                'game_id' => null, // Post general del foro
                'title' => $request->title,
                'content' => $request->content,
                'images' => $imageUrl ? [$imageUrl] : null,
                'type' => 'discussion',
                'is_pinned' => false,
                'is_locked' => false,
                'views' => 0,
                'replies_count' => 0,
                'last_activity' => now()
            ]);

            return redirect()->route('forum.index')->with('success', '¡Post creado exitosamente!');

        } catch (\Exception $e) {
            Log::error('Error al crear post: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error al crear el post. Inténtalo de nuevo.');
        }
    }

    /**
     * Mostrar un post específico con sus respuestas
     */
    public function show($id)
    {
        try {
            $post = ForumPost::with(['user', 'replies.user'])
                ->findOrFail($id);

            // Incrementar vistas
            $post->incrementViews();

            // Obtener respuestas ordenadas por fecha
            $replies = $post->replies()
                ->with('user')
                ->where('is_approved', true)
                ->orderBy('created_at', 'asc')
                ->get();

            return view('forum.show', compact('post', 'replies'));

        } catch (\Exception $e) {
            Log::error('Error al mostrar post: ' . $e->getMessage());
            return redirect()->route('forum.index')->with('error', 'Post no encontrado');
        }
    }

    /**
     * Crear respuesta a un post
     */
    public function reply(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string|max:2000'
        ], [
            'content.required' => 'El contenido de la respuesta es obligatorio',
            'content.max' => 'La respuesta no puede tener más de 2000 caracteres'
        ]);

        try {
            $post = ForumPost::findOrFail($postId);
            
            // Verificar que el post no esté bloqueado
            if ($post->is_locked) {
                return back()->with('error', 'Este post está cerrado para nuevas respuestas');
            }

            $user = Auth::user();

            // Crear la respuesta
            ForumReply::create([
                'forum_post_id' => $post->id,
                'user_id' => $user->id,
                'parent_reply_id' => null, // Por ahora sin respuestas anidadas
                'content' => $request->content,
                'images' => null, // Por ahora sin imágenes en respuestas
                'is_approved' => true
            ]);

            return redirect()->route('forum.show', $post->id)->with('success', '¡Respuesta agregada exitosamente!');

        } catch (\Exception $e) {
            Log::error('Error al crear respuesta: ' . $e->getMessage());
            return back()->with('error', 'Error al agregar la respuesta. Inténtalo de nuevo.');
        }
    }

    /**
     * Eliminar un post (solo el autor o admin)
     */
    public function destroy($id)
    {
        try {
            $post = ForumPost::findOrFail($id);
            $user = Auth::user();

            // Verificar permisos
            if ($post->user_id !== $user->id && !$user->isAdmin()) {
                return redirect()->route('forum.index')->with('error', 'No tienes permiso para eliminar este post');
            }

            // Eliminar imagen si existe
            if ($post->images && count($post->images) > 0) {
                foreach ($post->images as $image) {
                    $imagePath = public_path($image);
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
            }

            $post->delete();

            return redirect()->route('forum.index')->with('success', 'Post eliminado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al eliminar post: ' . $e->getMessage());
            return back()->with('error', 'Error al eliminar el post');
        }
    }
}