<!-- resources/views/admin/news/edit.blade.php -->
@extends('layouts.admin')

@section('content')
<h1>Editar noticia</h1>
<form method="POST" action="{{ route('admin.news.update', $news) }}" enctype="multipart/form-data">
    @method('PUT')
    @include('admin.news.form')
    <button class="btn btn-primary">Actualizar</button>
</form>
@endsection