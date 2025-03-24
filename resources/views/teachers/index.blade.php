<h2 class="font-semibold text-xl text-gray-800 leading-tight">
    {{ __('Teachers') }} 
</h2>

<div class="p-6 text-gray-900">
    <a href="{{ route('teachers.create') }}">Add new teacher</a> 
    <table> 
        <thead>
            <tr>
                <th>Name</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($teachers as $teacher)
                <tr>
                    <td>{{ $teacher->firstname }}</td>
                    <td>{{ $teacher->lastname }}</td>
                    <td>{{ $teacher->companyname }}</td>
                    <td>{{ $teacher->city->name}}</td>
                    @foreach ($teacher->category as $category)
                        <td>{{$category->name}}</td>
                    @endforeach
                    <td>
                        <a href="{{ route('teachers.edit', $teacher) }}">Edit</a>
                        <form method="POST" action="{{ route('teachers.destroy', $teacher) }}"> 
                            @csrf
                            @method('DELETE')

                            <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table> 
</div>