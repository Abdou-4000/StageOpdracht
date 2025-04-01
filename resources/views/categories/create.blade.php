@extends('layout')

@section('content')
    <div class="text-gray-dark">
        <h1 class="text-3xl m-3">Categorie toevoegen</h1>
        @include('categories._form', [
            'action' => route('categories.store'),
            'isEdit' => false,
            'categories' => null
        ])
    </div>
@endsection
