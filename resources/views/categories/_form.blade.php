<form method="POST" action="{{ $action }}" class="m-3 p-6 w-full max-w-lg mx-auto">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="space-y-6">
        <div>
            <label for="name" class="block text-gray-dark font-semibold mb-1">Naam</label>
            <input type="text" name="name" id="name" value="{{ old('name', $category->name ?? '') }}" placeholder="Naam" class="w-full p-1.5 pl-4 rounded-3xl border border-gray-300">
            @error('name')
                <div class="text-red text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="color" class="block text-gray-dark font-semibold mb-1">Kies een kleur</label>
            <input type="color" id="color" name="color" value="{{ old('color', $category->color ?? '#000000') }}" class="w-full bg-white rounded-3xl">
            @error('color')
                <div class="text-red text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="mt-6">
        <button type="submit" class="w-full bg-red text-white font-semibold py-2 rounded-3xl">
            Opslaan
        </button>
    </div>
</form>