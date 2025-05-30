<head>
    @vite(['resources/css/app.css', 'resources/js/blade.ts'])
    <style>
        a.active {
            text-decoration: underline;
        }

        a {
            text-decoration: none; /* This removes the underline from all links */
        }
    </style>
</head>

<div class="flex flex-wrap flex-row justify-between bg-white sticky top-0">
    <img class="flex w-64 pt-8 pl-8 bg-white" src="{{ asset('assets/Logo.png') }}" alt="Logo">
    <div class="mt-[80px]">
        <a class="text-black {{ request()->routeIs('teachers.index') ? 'active' : '' }}" href="{{ route('teachers.index')}}">Leerkrachtenoverzicht</a>
    </div>
    <div class="mt-[80px]">
        <a class="text-black {{ request()->routeIs('teachers.create') ? 'active' : '' }}" href="{{ route('teachers.create')}}">Leerkracht toevoegen</a>
    </div>
    <div class="mt-[80px]">
        <a class="text-black {{ request()->routeIs('categories.index') ? 'active' : '' }}" href="{{ route('categories.index')}}">Categorieënbeheer</a>
    </div>
    <div class="flex m-8 mt-[72px]">
        <a class="flex text-white bg-red w-96 h-10 p-2 justify-center rounded-full" href="{{ route('map') }}">
            Kaart
        </a> 
    </div>
</div>