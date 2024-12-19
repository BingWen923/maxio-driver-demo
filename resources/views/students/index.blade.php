@extends('layouts.app')

@section('title', 'All Students')

@section('content')
    <h1 class="text-center mb-4">All Students</h1>
    <a href="{{ route('students.create') }}" class="btn btn-primary mb-3">Add student</a>
    <a href="{{ route('students.selectwhere') }}" class="btn btn-primary mb-3">Conditional Select</a>
    <a href="{{ route('students.showAggregates') }}" class="btn btn-primary mb-3">Show Aggregates</a>
    <a href="{{ route('students.showAggregates2') }}" class="btn btn-primary mb-3">Aggregates with Where</a>
    <a href="{{ route('students.groupby') }}" class="btn btn-primary mb-3">groupby Aggregates</a>
    <form action="{{ route('students.insert') }}" method="POST">
        @csrf
        <textarea name="InsertStudents" id="InsertStudents" class="form-control" rows="5" required>
[
    {"name": "Alice Smith", "email": "alice@example.com", "grades": 25, "college": "Science"},
    {"name": "Bob Smith", "email": "bob@example.com", "grades": 30, "college": "Arts"},
    {"name": "Charlie Smith", "email": "charlie@example.com", "grades": 35, "college": "Engineering"}
]
        </textarea>
        <button type="submit" class="btn btn-primary">Insert Students</button>
    </form>
    <a href="{{ route('students.relationships') }}" class="btn btn-primary mb-3">relationships demo</a>
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Grades</th>
                <th>College</th>
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
                    <td>{{ $student->college }}</td>
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
