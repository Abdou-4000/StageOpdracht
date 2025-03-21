<div class="p-6 text-gray-900">
    <form method="POST" action="{{ route('teachers.update', $teacher) }}"> 
        @csrf
        @method('PUT')

        <div> 
            <div>
                <label for="firstname">Firstname:</label>
            </div>
            <input type="text" name="firstname" id="firstname" value="{{ $teacher->firstname }}">
        </div>
        <div>
            <button type="submit">
                Save
            </button>
        </div> 
    </form>
</div>
