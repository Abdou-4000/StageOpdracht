@extends('layout')

@section('content')
    <div class="text-gray-dark">
        <h1 class="text-3xl m-3">Categorie bewerken</h1>
        @include('categories._form', [
            'action' => route('categories.update', $category->id),
            'isEdit' => true,
            'categories' => $category
        ])
    </div>
@endsection