@extends('layouts.app')

@section('title', 'Demo Relationships')

@section('content')
    <style>
        table {
            border-collapse: collapse;
            width: 90%;
            margin-bottom: 20px;
            margin-right: 20px;
            margin-left: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background: #f7f7f7;
        }
        h1 {
            clear: both;
        }
        .demo-code {
            border: 1px solid #ccc;
            width: 90%;
            margin: 20px;
            padding: 10px;
            background: #f0f0f0;
            max-height: 12em; /* about 10 lines high */
            overflow-y: auto; /* scroll if too many lines */
            white-space: pre; /* keep the original format */
        }

        .demo-box .btn {
            padding: 5px 10px;
            background: #3490dc;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
        }
        .demo-box .btn:hover {
            background: #2779bd;
        }
    </style>

    <script>
        function show_demo_code(code) {
            document.getElementById('demo-code').textContent = code;  
        }
    </script>

    <div class="demo-box">
        <h2>demo code</h2>
        <div class="demo-code" id="demo-code">
            demo codes
            <!-- Code snippet will appear here on hover -->
        </div>
        <a href="{{ route('students.relationships1to1') }}" class="btn btn-primary mb-3" onmouseover="show_demo_code({{ json_encode($OneToOneCreate) }})">one to one create</a>
    </div>
 
    <h1>Students</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>College</th>
                <th>Grades</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
            <tr>
                <td>{{ $student->id }}</td>
                <td>{{ $student->name }}</td>
                <td>{{ $student->email }}</td>
                <td>{{ $student->college }}</td>
                <td>{{ $student->grades }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h1>Phones</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Cellphone</th>
                <th>Home</th>
                <th>Company</th>
                <th>Student ID</th>
            </tr>
        </thead>
        <tbody>
            @foreach($phones as $phone)
            <tr>
                <td>{{ $phone->id }}</td>
                <td>{{ $phone->cellphone }}</td>
                <td>{{ $phone->home }}</td>
                <td>{{ $phone->company }}</td>
                <td>{{ $phone->student_id }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection
