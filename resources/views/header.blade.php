<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<div class="flex flex-wrap flex-row justify-between">
    <img class="flex w-64 pt-8 pl-8 bg-white" src="{{ asset('assets/Logo.png') }}" alt="Logo">

    <div class="flex m-8 mt-16">
        <a class="flex text-white bg-red-600 w-96 h-10 p-2 justify-center rounded-full" href="{{ route('teachers.index') }}">
            Kaart
        </a> 
    </div>
</div>