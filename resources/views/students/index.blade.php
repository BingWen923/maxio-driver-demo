@extends('layouts.app')

@section('title', 'All Students')

@section('content')
    <h1 class="text-center mb-4">All Students</h1>
    <a href="{{ route('students.create') }}" class="btn btn-primary mb-3">Add student</a>
    <form action="{{ route('students.insert') }}" method="POST">
        @csrf
            <textarea name="InsertStudents" id="InsertStudents" class="form-control" rows="10" required>
[
    {"name": "Alice", "email": "alice@example.com", "grades": 25},
    {"name": "Bob", "email": "bob@example.com", "grades": 30},
    {"name": "Charlie", "email": "charlie@example.com", "grades": 35}
]
            </textarea>
        <button type="submit" class="btn btn-primary">Insert Students</button>
    </form>
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Grades</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
                <tr>
                    <td>{{ $student->id }}</td>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->email }}</td>
                    <td>{{ $student->grades }}</td>
                    <td>
                        <a href="{{ route('students.edit', $student) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('students.destroy', $student) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
