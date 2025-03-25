<div class="p-6 text-gray-900">
    <form method="POST" action="{{ route('teachers.store') }}"> 
        @csrf

        <div>
            <div>
                <input type="text" name="firstname" id="firstname" placeholder="first name">
                @error('firstname')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div> 
            <div>
                <input type="text" name="lastname" id="lastname" placeholder="last name">
                @error('lastname')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div> 
            <div>
                <input type="email" name="email" id="email" placeholder="email">
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div> 
            <div>
                <input type="number" name="phone" id="phone" placeholder="phone">
                @error('phone')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div> 
            <div>
                <input type="number" name="companynumber" id="companynumber" placeholder="company number">
                @error('companynumber')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div> 
            <div>
                <input type="text" name="companyname" id="companyname" placeholder="company name">
                @error('companyname')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div> 
            <div>
                <input type="text" name="street" id="street" placeholder="street">
                @error('street')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div> 
            <div>
                <input type="number" name="streetnumber" id="streetnumber" placeholder="street number">
                @error('streetnumber')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <input type="text" name="city_name" id="city_name" placeholder="city">
                @error('city_name')
                    <p>{{$message}}</p>
                @enderror
            </div>
            <div>
                @foreach ($categories as $category)
                    <label>
                        <input type="checkbox" value="{{$category->id}}" name="categories[]">
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