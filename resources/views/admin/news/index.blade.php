<!-- resources/views/admin/news/index.blade.php -->
@extends('layouts.admin')

@section('content')
<h1>Noticias</h1>
<a href="{{ route('admin.news.create') }}" class="btn btn-primary mb-3">Nueva noticia</a>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table">
    <thead>
        <tr>
            <th>Título</th>
            <th>Fecha</th>
            <th>Imagen</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($news as $item)
        <tr>
            <td>{{ $item->title }}</td>
            <td>{{ $item->date }}</td>
            <td>
                @if($item->image)
                    <img src="{{ asset('storage/' . $item->image) }}" width="80">
                @endif
            </td>
            <td>
                <a href="{{ route('admin.news.edit', $item) }}" class="btn btn-sm btn-warning">Editar</a>
                <form action="{{ route('admin.news.destroy', $item) }}" method="POST" style="display:inline;">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar noticia?')">Eliminar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $news->links() }}
@endsection