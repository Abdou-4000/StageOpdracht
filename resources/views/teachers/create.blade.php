@vite(['resources/js/blade/email.js'])
@extends('layout')

@section('content')
    <div class="text-gray-dark">
        <h1 class="text-3xl m-3">Leerkracht toevoegen</h1>
        @include('teachers._form', [
            'action' => route('teachers.store'),
            'isEdit' => false,
            'isCreate' => true,
            'teacher' => null,
            'categories' => $categories
        ])
    </div>

@endsection
