@vite(['resources/js/blade/email.js'])
@extends('layout')

@section('content')
    <div class="text-gray-dark">
        <h1 class="text-3xl m-3">Leerkracht toevoegen</h1>
        @include('teachers._form', [
            'action' => route('teachers.store'),
            'isEdit' => false,
            'teacher' => null,
            'categories' => $categories
        ])
    </div>
    <div class="flex flex-col p-4">
        <div class="pb-2 font-semibold">
            <p>Het account zal gecreëerd worden op het volgende email adres</p>
        </div>
        <div>
            <input id="emailPrefixPreview" type="text" class="w-1/2 p-1.5 pl-4 rounded-3xl border border-gray-300">
            <span>@docent.syntrapxl.be</span>
            <input type="hidden" name="email" id="emailFull">
        </div>
    </div>

@endsection
