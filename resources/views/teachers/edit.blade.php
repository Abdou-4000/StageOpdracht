<div class="p-6 text-gray-900">
    <form method="POST" action="{{ route('teachers.update', $teacher) }}"> 
        @csrf
        @method('PUT')

        <div> 
            <div>
                <input type="text" name="firstname" id="firstname" value="{{ $teacher->firstname }}">
                @error('firstname')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <input type="text" name="lastname" id="lastname" value="{{ $teacher->lastname }}">
                @error('lastname')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <input type="email" name="email" id="email" value="{{ $teacher->email }}">
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <input type="number" name="phone" id="phone" value="{{ $teacher->phone }}">
                @error('phone')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <input type="number" name="companynumber" id="companynumber" value="{{ $teacher->companynumber }}">
                @error('companynumber')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <input type="text" name="companyname" id="companyname" value="{{ $teacher->companyname }}">
                @error('companyname')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <input type="text" name="street" id="street" value="{{ $teacher->street }}">
                @error('street')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <input type="number" name="streetnumber" id="streetnumber" value="{{ $teacher->streetnumber }}">
                @error('streetnumber')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <input type="text" name="city_name" id="city_name" value="{{ $teacher->city->name }}">
                @error('city_name')
                    <p>{{$message}}</p>
                @enderror
            </div>
            <div>
                @foreach ($categories as $category)
                    <label>
                        <input type="checkbox" value="{{$category->id}}" name="categories[]"
                        @if ($teacher->category->contains($category->id)) 
                            checked 
                        @endif>
                        {{$category->name}}
                    </label>
                @endforeach
            </div>
        </div>
        <div>
            <button type="submit">
                Opslaan
            </button>
        </div> 
    </form>
</div>
