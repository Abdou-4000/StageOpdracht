@extends('layout')

@section('content')
<div class="text-5xl text-gray-dark text-center font-semibold underline m-8">
    Leerkrachtenoverzicht
</div>
<div class="flex flex-row justify-evenly">
    <div class="flex flex-wrap">
        <form action="{{ route('teachers.import') }}" method="POST" enctype="multipart/form-data" class="mt-6 p-6 w-full max-w-lg mx-auto">
            @csrf
            <label for="file" class="block text-gray-dark font-semibold mb-2">Upload CSV File:</label>
            <input type="file" name="file" id="file" required class="block w-full text-gray-dark border border-gray-300 rounded-2xl p-2">
            <button type="submit" class="mt-4 w-full bg-red text-white font-semibold py-2 rounded-3xl">
                Leerkrachten toevoegen
            </button>
        </form>
    </div>
    <div class="flex flex-col self-center items-center">
        <div>
            <a class="text-red font-semibold" href="{{ url('/export-full-excel') }}" class="btn btn-primary text-black">Export Excel</a>
        </div>
        <div>
            <a class="text-red font-semibold" href="{{ url('/export-pdf') }}" class="btn btn-danger text-black">Export PDF</a>
        </div>
    </div>
</div>
<div class="flex flex-wrap justify-center">
    <input class="w-2/3 m-2 p-1.5 pl-4 text-gray-light border border-gray-200 rounded-3xl" id="searchbar" type="text" placeholder="Zoek">
</div>
<a href="{{ url('/export-full-excel') }}" class="btn btn-primary text-black">Export Full User Data</a>
<div class="w-screen p-6">
    <!-- Table container with horizontal and vertical scroll -->
    <div class="overflow-x-auto relative">
        <!-- Wrapper for the table to handle both horizontal and vertical scroll -->
        <div class="max-h-[500px] overflow-y-auto border border-gray-200 rounded-xl">
            <!-- Table with the header fixed and horizontal scrolling -->
            <table class="min-w-full bg-white shadow-md">
                <!-- Thead with fixed header for vertical scrolling -->
                <thead class="bg-gray-light text-white sticky top-0 z-10"> 
                    <tr>
                        <th class="py-3 px-6 text-left">Naam</th>
                        <th class="py-3 px-6 text-left"></th>
                        <th class="py-3 px-6 text-left">Email</th>
                        <th class="py-3 px-6 text-left">Telefoon</th>
                        <th class="py-3 px-6 text-left">Bedrijf</th>
                        <th class="py-3 px-6 text-left"></th>
                        <th class="py-3 px-6 text-left">Adres</th>
                        <th class="py-3 px-6 text-left">Coördinaten</th>
                        <th class="py-3 px-6 text-left">Categorieën</th>
                        <th class="py-3 px-6 text-left">Acties</th>
                    </tr>
                </thead>
                <!-- Table body with scroll -->
                <tbody class="divide-y divide-gray-200 text-gray-dark">
                    @foreach($teachers->sortByDesc('flagged') as $teacher)
                        <tr class="hover:bg-gray-100 {{ $teacher->flagged ? 'text-red' : '' }}">
                            <td class="py-4 px-6">{{ $teacher->firstname }}</td>
                            <td class="py-4 px-6">{{ $teacher->lastname }}</td>
                            <td class="py-4 px-6">{{ $teacher->email }}</td>
                            <td class="py-4 px-6">{{ $teacher->phone }}</td>
                            <td class="py-4 px-6">{{ $teacher->companyname }}</td>
                            <td class="py-4 px-6">{{ $teacher->companynumber }}</td>
                            <td class="py-4 px-6">{{ $teacher->street }} {{ $teacher->streetnumber }}, {{ $teacher->city->name }}</td>
                            <td class="flex  justify-center py-4 px-6">
                                @if (isset($teacher->lat) && isset($teacher->lng))
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16"> <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/> </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-exclamation-circle" viewBox="0 0 16 16"> <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/> <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/> </svg>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                @foreach ($teacher->category as $category)
                                    <span class="flex justify-center text-gray-light text-xs font-semibold px-2.5 py-0.5 rounded-lg block mt-1" style="background-color: {{ $category->color }};">
                                        {{$category->name}}
                                    </span>
                                @endforeach
                            </td>
                            <td class="py-4 px-6 flex space-x-2">
                                <a href="{{ route('teachers.edit', $teacher) }}" class="text-accentBlue hover:underline">Edit</a>
                                <form method="POST" action="{{ route('teachers.destroy', $teacher) }}" onsubmit="return confirm('Are you sure?')">
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
</div>
<script>
    // Searchbar
    function search() {
        const searchbar = document.getElementById('searchbar');
        const searchTerm = searchbar.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const rowText = row.textContent.toLowerCase();
            row.style.display = rowText.includes(searchTerm) ? '' : 'none';
        });
    }

    // Attach event listener
    document.getElementById('searchbar').addEventListener('input', search);
</script>
@endsection