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
            'content' => 'required|string',
            'date' => 'required|date',
            'image' => 'nullable|image|max:2048', 
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/news'), $imageName);
            $data['image'] = 'images/news/' . $imageName;
        }

        News::create($data);

        return redirect()->route('admin.news.index')->with('success', 'Noticia creada correctamente.');
    }

    public function update(Request $request, News $news)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'date' => 'required|date',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Elimina la imagen anterior si existe
            if ($news->image && file_exists(public_path($news->image))) {
                unlink(public_path($news->image));
            }
            $image = $request->file('image');
            $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/news'), $imageName);
            $data['image'] = 'images/news/' . $imageName;
        }

        $news->update($data);

        return redirect()->route('admin.news.index')->with('success', 'Noticia actualizada correctamente.');
    }

    public function destroy(News $news)
    {
        if ($news->image && file_exists(public_path($news->image))) {
            unlink(public_path($news->image));
        }
        $news->delete();
        return back()->with('success', 'Noticia eliminada correctamente.');
    }
}