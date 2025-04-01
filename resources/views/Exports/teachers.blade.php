<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Export</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        .header { font-size: 16px; font-weight: bold; text-align: center; padding: 10px; }
    </style>
</head>
<body>

    <h2 class="header">User Data Export</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th colspan="2">Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th colspan="2">Company</th>
                <th colspan=3>Address</th>
                <th>Categories</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($teachers as $teacher)
                <tr>
                    <td>{{ $teacher->id }}</td>
                    <td>{{ $teacher->firstname }}</td>
                    <td>{{ $teacher->lastname }}</td>
                    <td>{{ $teacher->email }}</td>
                    <td>{{ $teacher->phone }}</td>
                    <td>{{ $teacher->companyname }}</td>
                    <td>{{ $teacher->companynumber }}</td>
                    <td>{{ $teacher->street }}</td>
                    <td>{{ $teacher->streetnumber }}</td>
                    <td>{{ $teacher->city ? $teacher->city->name : 'N/A' }}</td>
                    <td>
                        @if ($teacher->category && $teacher->category->isNotEmpty())
                            {{ $teacher->category->pluck('name')->implode(', ') }}
                        @else
                            
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
