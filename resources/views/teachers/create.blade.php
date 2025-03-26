@extends('layout')

@section('content')
    <div class="p-6 text-gray-900">
        <h1>Create Teacher</h1>
        @include('teachers._form', [
            'action' => route('teachers.store'),
            'isEdit' => false,
            'teacher' => null,
            'categories' => $categories
        ])
    </div>
@endsection
