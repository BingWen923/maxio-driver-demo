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

    th,
    td {
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
        max-height: 12em;
        /* about 10 lines high */
        overflow-y: auto;
        /* scroll if too many lines */
        white-space: pre;
        /* keep the original format */
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
    <a href="{{ route('students.relationships1to1') }}" class="btn btn-primary mb-3" onmouseover="show_demo_code({{ json_encode($OneToOne) }})">one to one create</a>
    <a href="{{ route('students.relationships1tomany') }}" class="btn btn-primary mb-3" onmouseover="show_demo_code({{ json_encode($OneToMany) }})">One to many</a>
    <a href="{{ route('students.relationshipsmanytomany') }}" class="btn btn-primary mb-3" onmouseover="show_demo_code({{ json_encode($ManyToMany) }})">Many to many</a>
</div>



<h1>Students</h1>
{!! $tableStudents !!}


<h1>Phones</h1>
{!! $tablePhones !!}

<h1>Attendence</h1>
{!! $tableAttendence !!}

<h1>Papers</h1>
{!! $tablePapers !!}

<h1>intermediate table paper-student</h1>
{!! $tablePaperStudent !!}

@endsection