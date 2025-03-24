<div class="p-6 text-gray-900">
    <form method="POST" action="{{ route('teachers.update', $teacher) }}"> 
        @csrf
        @method('PUT')

        <div> 
            <div>
                <label for="firstname">Firstname:</label>
            </div>
            <input type="text" name="firstname" id="firstname" value="{{ $teacher->firstname }}">
            <input type="text" name="lastname" id="lastname" value="{{ $teacher->lastname }}">
            <input type="email" name="email" id="email" value="{{ $teacher->email }}">
            <input type="number" name="phone" id="phone" value="{{ $teacher->phone }}">
            <input type="number" name="companynumber" id="companynumber" value="{{ $teacher->companynumber }}">
            <input type="text" name="companyname" id="companyname" value="{{ $teacher->companyname }}">
            <input type="text" name="street" id="street" value="{{ $teacher->street }}">
            <input type="number" name="streetnumber" id="streetnumber" value="{{ $teacher->streetnumber }}">
            <input type="text" name="city_name" id="city_name" value="{{ $teacher->city->name }}">
             @foreach ($categories as $category)
                <label>
                    <input type="checkbox" value="{{$category->id}}" name="categories[]"
                    @if ($teacher->category->contains($category->id)) 
                        checked 
                    @endif>
                    {{$category->name}}
                </label>
            @endforeach
            @error('city_name')
                <p>{{$message}}</p>
            @enderror
        </div>
        <div>
            <button type="submit">
                Save
            </button>
        </div> 
    </form>
</div>
