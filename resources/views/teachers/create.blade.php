<div class="p-6 text-gray-900">
    <form method="POST" action="{{ route('teachers.store') }}"> 
        @csrf

        <div> 
            <input type="text" name="firstname" id="firstname">
            <input type="text" name="lastname" id="lastname">
            <input type="email" name="email" id="email">
            <input type="number" name="phone" id="phone">
            <input type="number" name="companynumber" id="companynumber">
            <input type="text" name="companyname" id="companyname">
            <input type="text" name="street" id="street">
            <input type="number" name="streetnumber" id="streetnumber">
            <input type="number" name="city_id" id="city_id">
        </div>
        <div>
            <button type="submit">
                Save
            </button>
        </div> 
    </form>
</div>