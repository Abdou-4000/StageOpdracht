@extends('layout')

@section('content')
    <div class="text-gray-dark">
        <h1 class="text-3xl m-3">Leerkracht bewerken</h1>
        @include('teachers._form', [
            'action' => route('teachers.update', $teacher->id),
            'isEdit' => true,
            'isCreate' => false,
            'teacher' => $teacher,
            'categories' => $categories
        ])
    </div>
@endsection
