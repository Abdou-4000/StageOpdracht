@extends('layout')

@section('content')
<div class="container mx-auto p-6">
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="py-3 px-6 text-left">Naam</th>
                    <th class="py-3 px-6 text-left"></th>
                    <th class="py-3 px-6 text-left">Email</th>
                    <th class="py-3 px-6 text-left">Telefoon</th>
                    <th class="py-3 px-6 text-left">Bedrijf</th>
                    <th class="py-3 px-6 text-left"></th>
                    <th class="py-3 px-6 text-left">Adres</th>
                    <th class="py-3 px-6 text-left">Categorieën</th>
                    <th class="py-3 px-6 text-left">Acties</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-black">
                @foreach($teachers as $teacher)
                    <tr class="hover:bg-gray-100">
                        <td class="py-4 px-6">{{ $teacher->firstname }}</td>
                        <td class="py-4 px-6">{{ $teacher->lastname }}</td>
                        <td class="py-4 px-6">{{ $teacher->email }}</td>
                        <td class="py-4 px-6">{{ $teacher->phone }}</td>
                        <td class="py-4 px-6">{{ $teacher->companyname }}</td>
                        <td class="py-4 px-6">{{ $teacher->companynumber }}</td>
                        <td class="py-4 px-6">{{ $teacher->street }} {{ $teacher->streetnumber }}, {{ $teacher->city->name }}</td>
                        <td class="py-4 px-6">
                            @foreach ($teacher->category as $category)
                                <span class="bg-blue-200 text-blue-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">
                                    {{$category->name}}
                                </span>
                            @endforeach
                        </td>
                        <td class="py-4 px-6 flex space-x-2">
                            <a href="{{ route('teachers.edit', $teacher) }}" class="text-blue-600 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('teachers.destroy', $teacher) }}" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <form action="{{ route('teachers.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" required>
        <button type="submit">Import Teachers</button>
    </form>
</div>

@endsection