<?php

namespace App\Http\Controllers;

use App\Models\student;
use GuzzleHttp\BodySummarizer;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        // List all students
        $students = Student::all();

        // Convert id field to integer for each student
        $students = $students->map(function ($student) {
            $student->id = (int) $student->id;
            return $student;
        });

        // Sort by id in ascending order
        $students = $students->sortBy('id');
        //dd($students);
        return view('students.index', compact('students'));
    }

    public function selectwhere()
    {
        // conditional select
        // $students = Student::where('grades',11)->get();
        // $students = Student::where('grades', 11)->where('name', 'ironman')->get();
        // $students = Student::where('grades', 11)->orwhere('name', 'ironman')->get();
         $students = Student::where('grades', 11)->orwhere('grades', 12)->orwhere('name', 'ironman')->get();  
        // $students = Student::where('grades', 11)->orwhere('grades', 12)->where('name', 'ironman')->get();       
/*          $students = Student::where(function ($query) {
            $query->where('grades', 11)
                  ->orWhere('grades', 12);
        })->where('name', 'ironman')->get();  */

        // Convert id field to integer for each student
        $students = $students->map(function ($student) {
            $student->id = (int) $student->id;
            return $student;
        });
        // Sort by id in ascending order
        $students = $students->sortBy('id');
        //dd($students);

        return view('students.index', compact('students'));
    }

    public function create()
    {
        // Show create student form
        return view('students.create');
    }

    public function store(Request $request)
    {
        // Validate and store new student
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'grades' => 'required|integer|between:0,100',
        ]);

        logger("\n\n\n\n");
        $student = Student::create($validated);
        // dd($student);
        //$student = new student($validated);
        //$student->save();

        // Check if the instance was successfully created and log it
        if ($student) {
            // dd(['student created', $student]);
            return redirect()->route('students.index')->with('success', 'Student created successfully.');
        } else {
            dd(['student create failed', $student]);
            return redirect()->back()->withErrors(['error' => 'Failed to create student.']);
        }
    }

    public function edit(Student $student)
    {
        // Show edit form
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        // Validate and update student
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'grades' => 'required|integer|between:0,100',
        ]);
        // dd($validated);
        $student->update($validated);

        return redirect()->route('students.index')->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        // Delete student
        // dd(['attributes'=>$student->getAttributes(),'id'=>$student->id]);
        // $student->delete();
        $id = $student->id;
        $student->delete();

        return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
    }

    public function insert(Request $request)
    {
        logger("\n\n\n\n*****insert");
        $data = json_decode($request->input('InsertStudents'), true);
    
        if (json_last_error() !== JSON_ERROR_NONE) {
            return redirect()->back()->withErrors(['error' => 'controller.insert: Invalid JSON format: ' . json_last_error_msg()]);
        }
    
        if (!is_array($data)) {
            return redirect()->back()->withErrors(['error' => 'controller.insert: Invalid data format.']);
        }
        logger($data);
    
        $student = Student::insert($data);
    
        if ($student) {
            return redirect()->route('students.index')->with('success', 'controller.insert: Students inserted successfully.');
        } else {
            return redirect()->back()->withErrors(['error' => 'controller.insert: Failed to insert students.']);
        }
    }
    
}
