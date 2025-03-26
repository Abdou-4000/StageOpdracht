@include('header')


<div class="flex flex-wrap flex-row bg-white">
    <div class="flex flex-col w-1/5 h-96 m-8 ml-16 border border-gray-300 shadow-lg rounded-3xl">
        <div class="text-black text-2xl m-4 mt-8 mb-6">
            <p>Voornaam Achternaam</p>
        </div>
        <div class="border border-black"></div>
        <div class="text-black text-xl m-4 mt-6 mb-6">
            <a href="{{ route('teachers.index') }}">Leerkrachten Overzicht</a>
        </div>
        <div class="border border-black"></div>
        <div class="text-black text-xl m-4 mt-6 mb-6">
            <a href="{{ route('teachers.create') }}">Leerkracht Toevoegen</a>
        </div>
        <div class="border border-black"></div>
        <div class="text-black text-xl m-4 mt-6 mb-6">
            <a href="{{ route('categories.index') }}">Categorieënbeheer</a>
        </div>
        <div class="border border-black"></div>
    </div>
    <div class="flex flex-wrap flex-col">
        @yield('content')
    </div>
</div>
