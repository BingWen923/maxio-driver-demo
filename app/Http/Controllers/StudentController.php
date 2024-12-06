<?php

namespace App\Http\Controllers;

use App\Models\student;
use GuzzleHttp\BodySummarizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function showAggregates()
    {
        // Max: Get the maximum grade
        $result = Student::max('Grades');
        echo "<br>The maximum grade is: " . $result . "<br>";
        
        // Min: Get the minimum grade
        $result = Student::min('Grades');
        echo "The minimum grade is: " . $result . "<br>";
        
        // Sum: Get the total of all grades
        $result = Student::sum('Grades');
        echo "The total of all grades is: " . $result . "<br>";
        
        // Avg: Get the average grade
        $result = Student::avg('Grades'); // Alternatively, you can use Student::average('Grades');
        echo "The average grade is: " . $result . "<br>";
        
        // Count: Get the total number of records
        $result = Student::count();
        echo "The total number of students is: " . $result . "<br>";
        
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

    public function showAggregates2()
    {
        // Max: Get the maximum grade for students with name like '%Smith%'
        $result = Student::where('name', 'like', '%Smith%')->max('Grades');
        echo "<br>The maximum grade for students with name like '%Smith%' is: " . $result . "<br>";
        
        // Min: Get the minimum grade for students with name like '%Smith%'
        $result = Student::where('name', 'like', '%Smith%')->min('Grades');
        echo "The minimum grade for students with name like '%Smith%' is: " . $result . "<br>";
        
        // Sum: Get the total of all grades for students with name like '%Smith%'
        $result = Student::where('name', 'like', '%Smith%')->sum('Grades');
        echo "The total of all grades for students with name like '%Smith%' is: " . $result . "<br>";
        
        // Avg: Get the average grade for students with name like '%Smith%'
        $result = Student::where('name', 'like', '%Smith%')->avg('Grades');
        echo "The average grade for students with name like '%Smith%' is: " . $result . "<br>";
        
        // Count: Get the total number of records for students with name like '%Smith%'
        $result = Student::where('name', 'like', '%Smith%')->count();
        echo "The total number of students with name like '%Smith%' is: " . $result . "<br>";
        
        $students = Student::all();
        
        // Convert id field to integer for each student
        $students = $students->map(function ($student) {
            $student->id = (int) $student->id;
            return $student;
        });
    
        // Sort by id in ascending order
        $students = $students->sortBy('id');
    
        return view('students.index', compact('students')); 
    }

    public function groupby()
    {
       
        $students = Student::all();

        $groupedStudents = $students -> groupBy('college');
        
        $results = $groupedStudents->map(function ($group, $college) {
            return [
                'status' => $college,
                'max_grade' => $group->max('grades'),
                'min_grade' => $group->min('grades'),
                'average_grade' => $group->avg('grades'),
                'total_students' => $group->count(),
            ];
        });
    
        return view('students.groupby', compact('results')); 
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
            'college'=> 'required|string|max:255',
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
            'college'=> 'required|string|max:255',
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
