<form method="POST" action="{{ $action }}" class="m-3">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <div class="col-span-2">
            <label for="firstname" class="block text-gray-dark font-semibold mb-1">Voornaam</label>
            <input type="text" name="firstname" id="firstname" value="{{ old('firstname', $teacher->firstname ?? '') }}" placeholder="Voornaam" class="w-full p-1.5 pl-4 rounded-3xl border border-gray-300">
            @error('firstname')
                <div class="text-red text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-span-2">
            <label for="lastname" class="block text-gray-dark font-semibold mb-1">Achternaam</label>
            <input type="text" name="lastname" id="lastname" value="{{ old('lastname', $teacher->lastname ?? '') }}" placeholder="Achternaam" class="w-full p-1.5 pl-4 rounded-3xl border border-gray-300">
            @error('lastname')
                <div class="text-red text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-span-2">
            <label for="email" class="block text-gray-dark font-semibold mb-1">E-mail</label>
            <input type="email" name="email" id="email" value="{{ old('email', $teacher->email ?? '') }}" placeholder="E-mail" class="w-full p-1.5 pl-4 rounded-3xl border border-gray-300">
            @error('email')
                <div class="text-red text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-span-2">
            <label for="phone" class="block text-gray-dark font-semibold mb-1">Telefoon</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone', $teacher->phone ?? '') }}" placeholder="Telefoon" class="w-full p-1.5 pl-4 rounded-3xl border border-gray-300">
            @error('phone')
                <div class="text-red text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-span-2">
            <label for="companyname" class="block text-gray-dark font-semibold mb-1">Bedrijfsnaam</label>
            <input type="text" name="companyname" id="companyname" value="{{ old('companyname', $teacher->companyname ?? '') }}" placeholder="Bedrijfsnaam" class="w-full p-1.5 pl-4 rounded-3xl border border-gray-300">
            @error('companyname')
                <div class="text-red text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-span-2">
            <label for="companynumber" class="block text-gray-dark font-semibold mb-1">Bedrijfsnummer</label>
            <input type="text" name="companynumber" id="companynumber" value="{{ old('companynumber', $teacher->companynumber ?? '') }}" placeholder="Bedrijfsnummer" class="w-full p-1.5 pl-4 rounded-3xl border border-gray-300">
            @error('companynumber')
                <div class="text-red text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-span-3">
            <label for="street" class="block text-gray-dark font-semibold mb-1">Straat</label>
            <input type="text" name="street" id="street" value="{{ old('street', $teacher->street ?? '') }}" placeholder="Straat" class="w-full p-1.5 pl-4 rounded-3xl border border-gray-300">
            @error('street')
                <div class="text-red text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-span-1">
            <label for="streetnumber" class="block text-gray-dark font-semibold mb-1">Huisnummer</label>
            <input type="number" name="streetnumber" id="streetnumber" value="{{ old('streetnumber', $teacher->streetnumber ?? '') }}" placeholder="Huisnummer" class="w-full p-1.5 pl-4 rounded-3xl border border-gray-300">
            @error('streetnumber')
                <div class="text-red text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-span-4">
            <label for="city_name" class="block text-gray-dark font-semibold mb-1">Stad</label>
            <input type="text" name="city_name" id="city_name" value="{{ old('city_name', $teacher->city->name ?? '') }}" placeholder="Stad" class="w-full p-1.5 pl-4 rounded-3xl border border-gray-300">
            @error('city_name')
                <div class="text-red text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-span-4">
            <label class="block text-gray-dark font-semibold mb-1">Categorieën</label>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                @foreach ($categories as $category)
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" value="{{$category->id}}" name="categories[]" 
                            @if ($teacher && $teacher->category->contains($category->id)) checked 
                            @endif>
                        <span class="text-gray-dark text-sm">{{ $category->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>

    <div class="mt-6">
        <button type="submit" class="w-full bg-red text-white font-semibold py-2 rounded-3xl">
            Opslaan
        </button>
    </div>
</form>
