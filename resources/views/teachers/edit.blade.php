@extends('layout')

@section('content')
    <div class="p-6 text-gray-900">
        <h1>Edit Teacher</h1>
        @include('teachers._form', [
            'action' => route('teachers.update', $teacher->id),
            'isEdit' => true,
            'teacher' => $teacher,
            'categories' => $categories
        ])
    </div>
@endsection
