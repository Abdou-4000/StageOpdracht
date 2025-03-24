<div class="p-6 text-gray-900">
    <form method="POST" action="{{ route('teachers.store') }}"> 
        @csrf

        <div> 
            <input type="text" name="firstname" id="firstname" placeholder="first name">
            <input type="text" name="lastname" id="lastname" placeholder="last name">
            <input type="email" name="email" id="email" placeholder="email">
            <input type="number" name="phone" id="phone" placeholder="phone">
            <input type="number" name="companynumber" id="companynumber" placeholder="company number">
            <input type="text" name="companyname" id="companyname" placeholder="company name">
            <input type="text" name="street" id="street" placeholder="street">
            <input type="number" name="streetnumber" id="streetnumber" placeholder="street number">
            <input type="text" name="city_name" id="city_name" placeholder="city">
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