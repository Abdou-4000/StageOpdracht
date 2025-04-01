<form method="POST" action="{{ $action }}" class="m-3">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div> 
        <input type="text" name="name" id="name" value="{{ old('name', $category->name ?? '') }}" placeholder="naam">
        <label for="color">Choose a Color:</label>
        <input type="color" id="color" name="color" value="{{ old('color', $category->color ?? '#000000') }}">
    </div>
     <div>
        <button type="submit">
            Save
        </button>
    </div> 
</form>