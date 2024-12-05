@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Student</h1>

    <!-- Display validation errors -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form for editing a student -->
    <form action="{{ route('students.update', $student->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $student->name) }}" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $student->email) }}" required>
        </div>

        <div class="form-group">
            <label for="college">College</label>
            <input type="text" name="college" id="college" class="form-control" value="{{ old('college', $student->college) }}" required>
        </div>

        <div class="form-group">
            <label for="grades">Grades</label>
            <input type="number" name="grades" id="grades" class="form-control" value="{{ old('grades', $student->grades) }}" required min="0" max="100">
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="{{ route('students.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
