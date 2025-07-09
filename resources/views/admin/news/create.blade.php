@extends('layouts.admin')

@section('content')
<div class="admin-form-wrapper" style="background: linear-gradient(135deg, #7c3aed 0%, #a78bfa 100%); min-height: 100vh; padding-top: 40px; padding-bottom: 40px;">
    <div class="admin-form-card">
        <h1 class="admin-form-title">Nueva noticia</h1>
        @if ($errors->any())
            <div class="alert alert-danger" style="margin-bottom:18px;">
                <ul style="margin:0; padding-left:18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('admin.news.store') }}" enctype="multipart/form-data" class="news-form">
            @include('admin.news.form')
            <button class="btn btn-success">Guardar</button>
        </form>
    </div>
</div>
@endsection