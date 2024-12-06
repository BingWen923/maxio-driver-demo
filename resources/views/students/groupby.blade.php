@extends('layouts.app')

@section('title', 'Grouped Students')

@section('content')
    <h1 class="text-center mb-4">Grouped Students</h1>

    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>College</th>
                <th>Max Grade</th>
                <th>Min Grade</th>
                <th>Average Grade</th>
                <th>Total Students</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $result)
                <tr>
                    <td>{{ $result['status'] }}</td>
                    <td>{{ $result['max_grade'] }}</td>
                    <td>{{ $result['min_grade'] }}</td>
                    <td>{{ number_format($result['average_grade'], 2) }}</td>
                    <td>{{ $result['total_students'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
