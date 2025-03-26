 <form method="POST" action="{{ route('categories.store') }}"> 
        @csrf

        <div> 
            <input type="text" name="name" id="name" placeholder="name">
        </div>
        <div>
            <button type="submit">
                Save
            </button>
        </div> 
    </form>