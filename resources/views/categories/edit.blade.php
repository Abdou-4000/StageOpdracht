<div>  
    <form method="POST" action="{{ route('categories.update', $category) }}"> 
        @csrf
        @method('PUT')

        <div> 
            <input type="text" name="name" id="name" value="{{ $category->name }}">
        </div>
        <div>
            <button type="submit">
                Save
            </button>
        </div> 
    </form>
</div>