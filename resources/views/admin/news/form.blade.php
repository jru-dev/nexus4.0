<!-- resources/views/admin/news/form.blade.php -->
@csrf
<div class="mb-3">
    <label>Título</label>
    <input type="text" name="title" class="form-control" value="{{ old('title', $news->title ?? '') }}" required>
</div>
<div class="mb-3">
    <label>Contenido</label>
    <textarea name="content" class="form-control" required>{{ old('content', $news->content ?? '') }}</textarea>
</div>
<div class="mb-3">
    <label>Fecha</label>
    <input type="date" name="date" class="form-control" value="{{ old('date', $news->date ?? '') }}" required>
</div>
<div class="mb-3">
    <label>Imagen</label>
    <input type="file" name="image" class="form-control" accept="image/*">
    @if(!empty($news->image))
        <img src="{{ asset('storage/' . $news->image) }}" width="120" class="mt-2">
    @endif
</div>