<!DOCTYPE html>
<html>
<head>
    <title>User List</title>
    <style>
        
    </style>
</head>
<body>
    <h1>Users</h1>

    @if(isset($error))
        <p style="color:red">{{ $error }}</p>
    @endif

    <form method="GET" action="{{ route('users.index') }}">
        <input type="text" name="search" placeholder="Search by name" value="{{ $search ?? '' }}">
        <button type="submit">Search</button>
    </form>

    @if(!empty($users))
        <table border=3>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Address</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $row)
                <tr>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['email'] }}</td>
                    <td>{{ $row['address']['street'] }}, {{ $row['address']['city'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</body>
</html>