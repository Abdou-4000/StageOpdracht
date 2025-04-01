@extends('layout')

@section('content')
<div class="text-gray-dark">
<a href="{{ route('categories.create') }}">Add new category</a> 
<table class="text-gray-dark"> 
        <thead>
            <tr>
                <th>Name</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
                <tr>
                    <td>{{ $category->name }}</td>
                    <td>
                        <a href="{{ route('categories.edit', $category) }}">Edit</a>
                        <form method="POST" action="{{ route('categories.destroy', $category) }}"> 
                            @csrf
                            @method('DELETE')

                            <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection