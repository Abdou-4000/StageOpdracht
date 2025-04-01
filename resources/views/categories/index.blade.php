@extends('layout')

@section('content')
<div class="flex flex-col text-gray-dark m-8">
    <div class="text-5xl text-gray-dark text-center font-semibold underline m-8">
        Categorieën Overzicht
    </div>

    <!-- Table container with horizontal and vertical scroll -->
    <div class="overflow-x-auto relative mt-6">
        <div class="max-h-[500px] overflow-y-auto border border-gray-200 rounded-xl">
            <table class="min-w-full bg-white shadow-md">
                <!-- Thead with fixed header for vertical scrolling -->
                <thead class="bg-gray-light text-white sticky top-0 z-10">
                    <tr>
                        <th class="py-3 px-6 text-left">Naam</th>
                        <th class="py-3 px-6 text-left">Kleur</th>
                        <th class="py-3 px-6 text-left">Acties</th>
                    </tr>
                </thead>
                <!-- Table body with scroll -->
                <tbody class="divide-y divide-gray-200 text-gray-dark">
                    @foreach($categories as $category)
                        <tr class="hover:bg-gray-100">
                            <td class="py-4 px-6">{{ $category->name }}</td>
                            <td class="py-4 px-6">
                                <span class="w-10 h-10 inline-block rounded-3xl" style="background-color: {{ $category->color }};"></span>
                            </td>
                            <td class="py-4 px-6 flex space-x-2">
                                <a href="{{ route('categories.edit', $category) }}" class="text-accentBlue hover:underline">Edit</a>
                                <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex flex-row justify-evenly">
        <div class="flex flex-wrap">
            <a href="{{ route('categories.create') }}" class="mt-6 p-6 w-full max-w-lg mx-auto bg-red text-white font-semibold py-2 rounded-3xl text-center">
                Voeg een nieuwe categorie toe
            </a>
        </div>
    </div>
</div>
@endsection