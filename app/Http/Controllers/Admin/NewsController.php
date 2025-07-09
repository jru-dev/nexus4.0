<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;

class NewsController extends Controller
{
    public function create()
    {
        return view('admin.news.create');
    }

    public function index()
    {
        $news = News::orderBy('date', 'desc')->paginate(10);
        return view('admin.news.index', compact('news'));
    }

    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:20|max:2000',
            'date' => 'required|date',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048', 
        ], [
            'title.required' => 'El título es obligatorio',
            'title.max' => 'El título no puede tener más de 255 caracteres',
            'content.required' => 'El contenido es obligatorio',
            'content.min' => 'El contenido debe tener al menos 20 caracteres',
            'content.max' => 'El contenido no puede tener más de 2000 caracteres',
            'date.required' => 'La fecha es obligatoria',
            'date.date' => 'La fecha debe ser válida',
            'image.image' => 'El archivo debe ser una imagen',
            'image.mimes' => 'La imagen debe ser JPG, PNG o WEBP',
            'image.max' => 'La imagen no puede ser mayor a 2MB'
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'news_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            
            // Crear directorio si no existe
            $imagePath = public_path('images/news');
            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0755, true);
            }
            
            // Mover imagen
            $image->move($imagePath, $imageName);
            $data['image'] = 'images/news/' . $imageName;
        }

        News::create($data);

        return redirect()->route('admin.news.index')->with('success', '¡Noticia creada correctamente! 📰');
    }

    public function update(Request $request, News $news)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:20|max:2000',
            'date' => 'required|date',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ], [
            'title.required' => 'El título es obligatorio',
            'title.max' => 'El título no puede tener más de 255 caracteres',
            'content.required' => 'El contenido es obligatorio',
            'content.min' => 'El contenido debe tener al menos 20 caracteres',
            'content.max' => 'El contenido no puede tener más de 2000 caracteres',
            'date.required' => 'La fecha es obligatoria',
            'date.date' => 'La fecha debe ser válida',
            'image.image' => 'El archivo debe ser una imagen',
            'image.mimes' => 'La imagen debe ser JPG, PNG o WEBP',
            'image.max' => 'La imagen no puede ser mayor a 2MB'
        ]);

        if ($request->hasFile('image')) {
            // Elimina la imagen anterior si existe
            if ($news->image && file_exists(public_path($news->image))) {
                unlink(public_path($news->image));
            }
            
            $image = $request->file('image');
            $imageName = 'news_' . time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            
            // Crear directorio si no existe
            $imagePath = public_path('images/news');
            if (!file_exists($imagePath)) {
                mkdir($imagePath, 0755, true);
            }
            
            // Mover imagen
            $image->move($imagePath, $imageName);
            $data['image'] = 'images/news/' . $imageName;
        }

        $news->update($data);

        return redirect()->route('admin.news.index')->with('success', '¡Noticia actualizada correctamente! ✅');
    }

    public function destroy(News $news)
    {
        // Eliminar imagen si existe
        if ($news->image && file_exists(public_path($news->image))) {
            unlink(public_path($news->image));
        }
        
        $newsTitle = $news->title;
        $news->delete();
        
        return redirect()->route('admin.news.index')->with('success', "Noticia '{$newsTitle}' eliminada correctamente 🗑️");
    }
}