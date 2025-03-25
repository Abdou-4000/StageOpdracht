<form method="POST" action="{{ $action }}">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div>
        <div>
            <input type="text" name="firstname" id="firstname" value="{{ old('firstname', isset($teacher) ? $teacher->firstname : '') }}" placeholder="First Name">
            @error('firstname')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <input type="text" name="lastname" id="lastname" value="{{ old('lastname', isset($teacher) ? $teacher->lastname : '') }}" placeholder="Last Name">
            @error('lastname')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <input type="email" name="email" id="email" value="{{ old('email', isset($teacher) ? $teacher->email : '') }}" placeholder="Email">
            @error('email')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <input type="number" name="phone" id="phone" value="{{ old('phone', isset($teacher) ? $teacher->phone : '') }}" placeholder="Phone">
            @error('phone')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <input type="number" name="companynumber" id="companynumber" value="{{ old('companynumber', isset($teacher) ? $teacher->companynumber : '') }}" placeholder="Company Number">
            @error('companynumber')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <input type="text" name="companyname" id="companyname" value="{{ old('companyname', isset($teacher) ? $teacher->companyname : '') }}" placeholder="Company Name">
            @error('companyname')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <input type="text" name="street" id="street" value="{{ old('street', isset($teacher) ? $teacher->street : '') }}" placeholder="Street">
            @error('street')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <input type="number" name="streetnumber" id="streetnumber" value="{{ old('streetnumber', isset($teacher) ? $teacher->streetnumber : '') }}" placeholder="Street Number">
            @error('streetnumber')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <input type="text" name="city_name" id="city_name" value="{{ old('city_name', isset($teacher) ? $teacher->city->name : '') }}" placeholder="City">
            @error('city_name')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div>
            @foreach ($categories as $category)
                <label>
                    <input type="checkbox" value="{{$category->id}}" name="categories[]"
                    @if ($teacher)
                        @if ($teacher->category->contains($category->id)) 
                            checked 
                        @endif
                    @endif>
                    {{$category->name}}
                </label>
            @endforeach
        </div>
    </div>

    <div>
        <button type="submit">Opslaan</button>
    </div>
</form>
