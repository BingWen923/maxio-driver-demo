<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Phone;
use App\Models\Attendance;
use App\Models\Paper;
use App\Models\StudentIdCard;
use GuzzleHttp\BodySummarizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Helpers\HtmlGenerateHelper;

class StudentController extends Controller
{
    public function relationshipsmanytomany()
    {
        // pivot ordering
        $s1 = Student::find(1);
        $papers = $s1->papers()->get();
   //               ->orderByPivot('id', 'desc') 
   //               ->get();
        dd($papers); 

/*         // pivot filtering
        $s1 = Student::find(1);
        $papers = $s1->papers()
                  ->wherePivot('id', 1) // Filter on the pivot table column
                  ->get();
        dd($papers); */

/*         $student1 = Student::whereHas('papers', function($query) {
            $query->where('code', 'COMPX575');
        })->get();
        dump($student1);
        $student2 = Student::withCount('papers')->get();
        dd($student2); */
        
/*         $student1 = Student::first();
        $student2 = Student::skip(1)->first();

        $paper1 = Paper::create([
            'code' => 'COMPX575',
            'title' => 'Programming Tools and Techniques'
        ]);

        $paper2 = Paper::create([
            'code' => 'CSMAX596',
            'title' => 'Computer Science Internship'
        ]);

        $paper3 = Paper::create([
            'code' => 'COMPX546',
            'title' => 'Graph Theory'
        ]);

        $paper4 = Paper::create([
            'code' => 'COMPX532',
            'title' => 'Information Visualisation'
        ]);

        $student1->papers()->attach([$paper1->id, $paper2->id]);
        $student2->papers()->attach([$paper1->id, $paper3->id]);
        $paper4->students()->attach([$student1->id,$student2->id]); */

        return redirect()->route('students.relationships');        
    }

    public function relationships1tomany()
    {
        // Eager Loading
        $s1 = Student::with('attendance')->get();
        dd('Eager Loaded Students:', $s1);
        foreach ($s1 as $student) {
            // Check if attendance relationship is loaded
            dump("Is 'attendance' relationship loaded for Student {$student->id}:", $student->relationLoaded('attendance'));
        
            // Check the data inside the attendance relationship
            dump("Attendance Records for Student {$student->id} ({$student->name}):", $student->attendance);
        
            foreach ($student->attendance as $record) {
                dump('Attendance Record Details:', $record);
            }
        }
        dd('Eager Loading test is over');
        
        
        // Querying Relationship Existence
        $s1 = Student::has('attendence')->get();
        dump($s1);
        $s2 = Student::has('attendence','>=',3)->get();        
        dd($s2);

        // Querying Relations
        $s1 = Student::find(1);
        $a1 = $s1->attendence()->where('status','Present')->get();
        dd($a1);

        // Assuming we have a student record in the database
        //$student = Student::skip(1)->first();

/*         $atts = Student::withCount('attendence')->first();
        dd($atts); */

         // Create single attendance records using the relationship
        $student->attendance()->create([
            'time' => now(),
            'course' => 'Math 101',
            'status' => 'Present'
        ]);

        $student->attendance()->create([
            'time' => now()->subDay(),
            'course' => 'Science 202',
            'status' => 'Absent'
        ]);

        // Create multiple attendance records at once
        $student->attendance()->createMany([
            [
                'time' => now()->subDays(3),
                'course' => 'History 404',
                'status' => 'Present'
            ],
            [
                'time' => now()->subDays(4),
                'course' => 'Art 505',
                'status' => 'Absent'
            ]
        ]); 

        return redirect()->route('students.relationships');        
    }
    
    public function relationships1to1()
    {
/*         $card = StudentIdCard::create([
            'idnumber' => '1600001',
            'issuedate' => '2024-02-23',
            'expiredate' => '2025-03-31',
            'phone_id' => 1, 
        ]); */
        $r1 = Student::find(1);
        dump($r1);
        
        $r2 = $r1->phone()->first(); // Fetch related Phone
        dump($r2);
        
        $r3 = $r2->idcard; // Use property-like access to fetch the related StudentIdCard
        dump($r3);
        
        $h = Student::find(1)->phone_idcard()->toSql(); 
        
        dd($h); // Final output
        /*         // Create a student
        $student = Student::create([
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'college' => 'Science',
            'grades' => 85
        ]);

        // Create a phone record associated with the student
        $student->phone()->create([
            'cellphone' => '12-456-7890',
            'home' => '55-555-5555',
            'company' => '55-555-1234'
        ]); */
        // Get all students
        $allStudents = Student::with('phone')->get();
        //$allStudents = Student::all();

        // Filter out students that do not have a phone record
        // Assuming phone() returns a null or empty value when no phone record is present
        $studentsWithoutPhone = $allStudents->filter(function ($std) {
            return $std->phone === null;
        });

        foreach ($studentsWithoutPhone as $std) {
            // Generate random parts of the phone numbers
            $random1 = rand(1, 99) . '-' . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT) . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $random2 = rand(1, 99) . '-' . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT) . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $random3 = rand(1, 99) . '-' . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT) . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);


            // Create a phone record for the student
            $std->phone()->create([
                'cellphone' => $random1,
                'home' => $random2,
                'company' => $random3
            ]);
        }

        return redirect()->route('students.relationships');
    }

    public function relationships()
    {
        $OneToOne = <<<EOL
        // Create a student
        \$student = Student::create([
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'college' => 'Science',
            'grades' => 85
        ]);

        // Create a phone record associated with the student
        \$student->phone()->create([
            'cellphone' => '12-456-7890',
            'home' => '55-555-5555',
            'company' => '55-555-1234'
        ]);
        EOL;

        $OneToMany = <<<EOL
         // Create single attendance records using the relationship
        \$student->attendence()->create([
            'time' => now(),
            'course' => 'Math 101',
            'status' => 'Present'
        ]);

        \$student->attendence()->create([
            'time' => now()->subDay(),
            'course' => 'Science 202',
            'status' => 'Absent'
        ]);

        // Create multiple attendance records at once
        \$student->attendence()->createMany([
            [
                'time' => now()->subDays(3),
                'course' => 'History 404',
                'status' => 'Present'
            ],
            [
                'time' => now()->subDays(4),
                'course' => 'Art 505',
                'status' => 'Absent'
            ]
        ]); 
        EOL;

        $ManyToMany = <<<EOL
        \$student1 = Student::first();
        \$student2 = Student::skip(1)->first();

        \$paper1 = Paper::create([
            'code' => 'COMPX575',
            'title' => 'Programming Tools and Techniques'
        ]);

        \$paper2 = Paper::create([
            'code' => 'CSMAX596',
            'title' => 'Computer Science Internship'
        ]);

        \$paper3 = Paper::create([
            'code' => 'COMPX546',
            'title' => 'Graph Theory'
        ]);

        \$paper4 = Paper::create([
            'code' => 'COMPX532',
            'title' => 'Information Visualisation'
        ]);

        \$student1->papers()->attach([\$paper1->id, \$paper2->id]);
        \$student2->papers()->attach([\$paper1->id, \$paper3->id]);
        \$paper4->students()->attach([\$student1->id,\$student2->id]);

        EOL;

        // List all records
        $students = Student::all();
        $tableStudents = HtmlGenerateHelper::generateTable($students);
        $phones = Phone::all();
        $tablePhones = HtmlGenerateHelper::generateTable($phones);
        $atts = Attendance::all();
        $tableAttendance = HtmlGenerateHelper::generateTable($atts);
        $papers = Paper::all();
        $tablePapers = HtmlGenerateHelper::generateTable($papers);
        $paper_student = DB::table('table_paper_student')->get(); // load the intermediate table
        $tablePaperStudent = HtmlGenerateHelper::generateTable($paper_student);
        $sic = StudentIdCard::all();
        $tablesic = HtmlGenerateHelper::generateTable($sic);
    
        return view('students.relationships', compact('tableStudents', 'tablePhones', 'tableAttendance', 'tablePapers', 'tablePaperStudent','tablesic', 'OneToOne', 'OneToMany','ManyToMany'));
    }

    public function index()
    {
        // List all students
        $students = Student::all();

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
        $result = Student::max('grades');
        echo "<br>The maximum grade is: " . $result . "<br>";
 
        // Min: Get the minimum grade
        $result = Student::min('grades');
        echo "The minimum grade is: " . $result . "<br>";
        
        // Sum: Get the total of all grades
        $result = Student::sum('grades');
        echo "The total of all grades is: " . $result . "<br>";
        
        // Avg: Get the average grade
        $result = Student::avg('grades'); // Alternatively, you can use Student::average('Grades');
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
                'college' => $college,
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
        dd($validated);
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
