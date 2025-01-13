<?php

namespace tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Paper;
use Illuminate\Support\Facades\DB;

class RelationshipTest extends TestCase
{
    /*
      relationships in the demo
      one to one    -   student to phone
      one to many   -   student to attendance
      many to many  -   student to paper
      has one through   - student to phone to StudentIdCard
    */
    protected function setUp(): void
    {
        parent::setUp();

        // Bootstrapping Laravel application
        $this->app = require __DIR__ . '/../../bootstrap/app.php';

        // Ensure database connection for the test
        $this->app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    }

    public function testOneToOneHasOne(): void
    {
        echo "\n\n********************* Test One-to-One hasOne() Relationship *********************\n";
    
        try {
            // Step 1: Find a student
            $student = Student::find(1);
            $this->assertNotNull($student, "Student with ID 1 does not exist. Check the test data.");
            echo "\nLoaded Student(ID: {$student->id}).";
    
            // Step 2: Retrieve the associated phone record using hasOne()
            $phone = $student->phone()->first();
            $this->assertNotNull($phone, "Phone record for Student(ID: {$student->id}) does not exist.");
            echo "\nLoaded Phone(ID: {$phone->id}) for Student(ID: {$student->id}).";
    
            // Step 3: Verify the relationship integrity
            $this->assertEquals($student->id, $phone->student_id, "The 'student_id' in Phone does not match the Student ID.");
            echo "\nVerified that Phone(ID: {$phone->id}) belongs to Student(ID: {$student->id}).";
        } catch (\Exception $e) {
            // Catch exceptions and display the error message and stack trace
            echo "\nError: " . $e->getMessage();
            echo "\nTrace: " . $e->getTraceAsString();
        } finally {
            // Ensure cleanup is always executed
            restore_error_handler();
            restore_exception_handler();
        }
    }

    public function testHasManyAndBelongsTo(): void
    {
        echo "\n\n********************* Test hasMany() and belongsTo() Relationships *********************\n";

        try {
            // 1️⃣ test Student -> Attendance (hasMany)
            $student = Student::find(1);
            $this->assertNotNull($student, "Student with ID 1 does not exist. Check the test data.");
            echo "\nLoaded Student(ID: {$student->id})";

            // verify hasMany() if get the right Attendance record
            $attendances = $student->attendance;
            $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $attendances, "Student's attendance relationship did not return a Collection.");
            echo "\nVerified hasMany() relationship: Student -> Attendance.";

            // 2️⃣ test Attendance -> Student (belongsTo)
            $attendance = Attendance::where('student_id', $student->id)->first();
            $this->assertNotNull($attendance, "No attendance record found for Student(ID: {$student->id}).");
            echo "\nLoaded Attendance(ID: {$attendance->id}) related to Student(ID: {$student->id}).";

            // verify belongsTo() if get the right  Student record
            $relatedStudent = $attendance->student;
            $this->assertInstanceOf(Student::class, $relatedStudent, "Attendance's student relationship did not return a Student instance.");
            echo "\nVerified belongsTo() relationship: Attendance -> Student.";
        } catch (\Exception $e) {
            echo "\nError: " . $e->getMessage();
            echo "\nTrace: " . $e->getTraceAsString();
        } finally {
            restore_error_handler();
            restore_exception_handler();
        }
    }


    public function testHasOneOfMany(): void
    {
        echo "\n********************* test one to many relationship - has one of many *********************\n";
        echo "    hasone() latestOfMany() oldestOfMany() \n";
        try {
            $student = Student::find(1);
            $latestAtt = $student->hasOne(Attendance::class)->latestOfMany()->first();
            $oldestAtt = $student->hasOne(Attendance::class)->oldestOfMany()->first();
            $att = $student->hasOne(Attendance::class)->ofMany('id', 'max')->first();

            echo "\n*********************latest ID: " . ($latestAtt?->id ?? 'N/A');
            echo "\n*********************oldest ID: " . ($oldestAtt?->id ?? 'N/A');
            echo "\n*********************max ID: " . ($att?->id ?? 'N/A');

            $maxid = Attendance::where('student_id', '1')->max('id');
            $minid = Attendance::where('student_id', '1')->min('id');
            echo "\n*********Calculated max ID: $maxid and min ID: $minid\n";

            $this->assertNotNull($latestAtt, "Latest attendance should not be null.");
            $this->assertNotNull($oldestAtt, "Oldest attendance should not be null.");
            $this->assertNotNull($att, "Attendance with max ID should not be null.");

            $this->assertEquals($maxid, $att->id, "Max ID from raw query should match the attendance record with max ID.");
            $this->assertEquals($maxid, $latestAtt->id, "Latest attendance ID should match the max ID.");
            $this->assertEquals($minid, $oldestAtt->id, "Oldest attendance ID should match the min ID.");
            $this->assertGreaterThanOrEqual($oldestAtt->id, $latestAtt->id, "Latest attendance ID should be greater than or equal to oldest attendance ID.");
        } finally {
            // Ensure cleanup is always executed
            restore_error_handler();
            restore_exception_handler();
        }
    }

    public function testHasOneThrough(): void
    {
        echo "\n********************* test one to many relationship - HasOneThrough *********************\n";
        echo "   hasOneThrough() \n";
        try {
            $r1 = Student::find(1);
            $r2 = $r1->phone()->first(); // Fetch related Phone
            $r3 = $r2->idcard; // Use property-like access to fetch the related StudentIdCard
         
            $h = Student::find(1)->phone_idcard()->first();

            // Check if both $r3 and $h are not null
            echo "\n*********************the idcard ID: " . ($r3?->id ?? 'N/A');
            $this->assertNotNull($r3, "Student's phone's idcard should not be null, check the test data.");
            echo "\n*********************the phone_idcard() ID: " . ($h?->id ?? 'N/A');
            $this->assertNotNull($h, "Student's phone_idcard should not be null.");


            // Check whether $r3->id is equal to $h->id
            $this->assertEquals($r3->id, $h->id, "The ID of the idcard obtained via phone should match the one obtained via HasOneThrough relationship.");
        } finally {
            // Ensure cleanup is always executed
            restore_error_handler();
            restore_exception_handler();
        }
    }
    
    public function testManyToMany_retrieving(): void
    {
        /*
            Retrieving Intermediate Table Columns
            Filtering Queries via Intermediate Table Columns
            Ordering Queries via Intermediate Table Columns
            Defining Custom Intermediate Table Models
        */
        echo "\n\n********************* test many to many relationship - retrieving  *********************\n";
    
        try {
            $s1 = Student::find(1);
            $papers = $s1->papers;
            $this->assertFalse($papers->isEmpty(), "Student(id:1)'s papers should not be empty.");

            foreach ($papers as $paper) {
                echo "\n**pivot ID: " . ($paper->pivot->id ?? 'N/A').
                    "\n**student ID: " . ($paper->pivot->student_id ?? 'N/A').
                    "\n**paper ID: " . ($paper->pivot->paper_id ?? 'N/A').
                    "******\n";
            }

        } finally {
            // Ensure cleanup is always executed
            restore_error_handler();
            restore_exception_handler();
        }
    }

    public function testManyToMany_filtering(): void
    {
        /*
            Retrieving Intermediate Table Columns
            Filtering Queries via Intermediate Table Columns
            Ordering Queries via Intermediate Table Columns
            Defining Custom Intermediate Table Models
        */
        echo "\n\n********************* test many to many relationship - Filtering  *********************\n";
    
        try {
            $s1 = Student::find(1);
            $papers = $s1->papers()
                        ->wherePivot('paper_id', 1) // Filter on the pivot table column
                        ->get();
            $this->assertFalse($papers->isEmpty(), "Student(id:1)'s paper(id:1) should not be empty.");

        } finally {
            // Ensure cleanup is always executed
            restore_error_handler();
            restore_exception_handler();
        }
    }

    public function testManyToMany_ordering(): void
    {
        /*
            Retrieving Intermediate Table Columns
            Filtering Queries via Intermediate Table Columns
            Ordering Queries via Intermediate Table Columns
            Defining Custom Intermediate Table Models
        */
        echo "\n\n********************* test many to many relationship - Ordering  *********************\n";
    
        try {
            $s1 = Student::find(1);
            $papers = $s1->papers()
                        ->orderByPivot('id', 'desc')
                        ->get();
            $this->assertFalse($papers->isEmpty(), "Student(id:1)'s papers after ordering should not be empty.");

        } finally {
            // Ensure cleanup is always executed
            restore_error_handler();
            restore_exception_handler();
        }
    }

    public function testQueryingRelationshipExistence(): void
    {
        // Querying relationship and querying relationship existence
        echo "\n\n********************* test 1 to many Querying Relationship Existence  *********************\n";
    
        try {
            // Querying Relations
            $s1 = Student::find(1);
            $a1 = $s1->attendance()->where('status','Present')->get();
            echo "\n** student(1) attendance records with status=Present count: ".$a1->count();
            // dump($a1);
            $this->assertGreaterThanOrEqual(1, $a1->count(), "Student(id:1)'s attendance records with status 'Present' should not be empty.");

            $s2 = Student::has('attendance')->get();
            echo "\n** Students who have attendance records count: ".$s2->count();
            $this->assertGreaterThanOrEqual(1, $s2->count(), "No students have attendance records. Check the test data.");

            $s3 = Student::has('attendance','>=',3)->get();
            echo "\n** Students who have attendance records with condition count: ".$s3->count();
            $this->assertGreaterThanOrEqual(1, $s3->count(), "No students have attendance records with condition. Check the test data.");

        } catch (\Exception $e) {
            // Catch exceptions and display the error message and stack trace
            echo "\nError: " . $e->getMessage();
            echo "\nTrace: " . $e->getTraceAsString();
        } finally {
            // Ensure cleanup is always executed
            restore_error_handler();
            restore_exception_handler();
        }
    }

    public function testQueryingRelationshipAbsence(): void
    {
        echo "\n\n********************* test 1 to many Querying Relationship Absence *********************\n";
    
        try {
            $s2 = Student::doesntHave('attendance')->get();
            echo "\n** Students who do NOT have attendance records count: ".$s2->count();
            $this->assertGreaterThanOrEqual(1, $s2->count(), "Expected students without attendance records. Check the test data.");

        } catch (\Exception $e) {
            // Catch exceptions and display the error message and stack trace
            echo "\nError: " . $e->getMessage();
            echo "\nTrace: " . $e->getTraceAsString();
        } finally {
            // Ensure cleanup is always executed
            restore_error_handler();
            restore_exception_handler();
        }
    }

    public function testAggregatingRelatedModels_Count(): void
    {
        echo "\n\n********************* test 1 to many Aggregating Related Models - count *********************\n";
    
        try {
            echo "\n\n****** withCount() *******";
            $s1 = Student::withCount('attendance')->first();
            echo "\n** Student Record (JSON): " . $s1->toJson(JSON_PRETTY_PRINT);
            $this->assertGreaterThanOrEqual(1, $s1->attendance_count, "Expected the student's attendance_count to be greater than or equal to 1.");

            echo "\n\n****** loadCount() *******";
            $s1 = Student::find(1);
            $c1 = $s1->getAttribute('attendance_count') ?? 'N/A';
            echo "\n** Before loadCount - attendance_count: $c1";
            $s1->loadCount('attendance');
            $c2 = $s1->attendance_count;
            echo "\n** After loadCount - attendance_count: $c2";
            //echo "\n** Student Record (JSON): " . $s1->toJson(JSON_PRETTY_PRINT);
            $this->assertGreaterThanOrEqual(1, $c2, "Expected the student's attendance_count to be greater than or equal to 1.");
        } catch (\Exception $e) {
            // Catch exceptions and display the error message and stack trace
            echo "\nError: " . $e->getMessage();
            echo "\nTrace: " . $e->getTraceAsString();
        } finally {
            // Ensure cleanup is always executed
            restore_error_handler();
            restore_exception_handler();
        }
    }

    public function testAggregatingRelatedModels_others(): void
    {
        echo "\n\n********************* test 1 to many Aggregating Related Models *********************\n";
        echo "**** sum, min,max, avg ****\n";
    
        try {
            echo "\n\n****** withSum() *******";
            $s1 = Student::withSum('attendance','id')->first();
            echo "\n** Student Record (JSON): " . $s1->toJson(JSON_PRETTY_PRINT);
            $this->assertGreaterThanOrEqual(1, $s1->attendance_sum_id, "Expected withSum() result to be greater than or equal to 1.");

            echo "\n\n****** withMin() *******";
            $s1 = Student::withMin('attendance','id')->first();
            echo "\n** Student Record (JSON): " . $s1->toJson(JSON_PRETTY_PRINT);
            $this->assertGreaterThanOrEqual(1, $s1->attendance_min_id, "Expected withMin() result to be greater than or equal to 1.");

            echo "\n\n****** withMax() *******";
            $s1 = Student::withMax('attendance','id')->first();
            echo "\n** Student Record (JSON): " . $s1->toJson(JSON_PRETTY_PRINT);
            $this->assertGreaterThanOrEqual(1, $s1->attendance_max_id, "Expected withMax() result to be greater than or equal to 1.");

            echo "\n\n****** withAvg() *******";
            $s1 = Student::withAvg('attendance','id')->first();
            echo "\n** Student Record (JSON): " . $s1->toJson(JSON_PRETTY_PRINT);
            $this->assertGreaterThanOrEqual(1, $s1->attendance_avg_id, "Expected withAvg() result to be greater than or equal to 1.");

        } catch (\Exception $e) {
            // Catch exceptions and display the error message and stack trace
            echo "\nError: " . $e->getMessage();
            echo "\nTrace: " . $e->getTraceAsString();
        } finally {
            // Ensure cleanup is always executed
            restore_error_handler();
            restore_exception_handler();
        }
    }

    public function testEagerLoading(): void
    {
        echo "\n\n********************* test 1 to many Eager Loading *********************\n";
    
        try {
            // Eager Loading
            $s1 = Student::with('attendance')->get();
            dump($s1);
            echo "\n** Students eager loading count: ".$s1->count();
            $this->assertGreaterThanOrEqual(1, $s1->count(), "Expected Students eager loading counts>=1. Check the test data.");

        } catch (\Exception $e) {
            // Catch exceptions and display the error message and stack trace
            echo "\nError: " . $e->getMessage();
            echo "\nTrace: " . $e->getTraceAsString();
        } finally {
            // Ensure cleanup is always executed
            restore_error_handler();
            restore_exception_handler();
        }
    }

    public function testLazyEagerLoading(): void
    {
        echo "\n\n********************* Test 1-to-Many Lazy Eager Loading *********************\n";

        try {
            // Retrieve students without loading the attendance relationship
            $students = Student::all();
            echo "\n** Students count before lazy eager loading: " . $students->count();
            $this->assertGreaterThanOrEqual(1, $students->count(), "Expected Students count >= 1 before lazy eager loading. Check the test data.");

            // Lazy load the attendance relationship
            $students->load('attendance');
            dump($students);

            // Verify that the attendance relationship has been loaded
            foreach ($students as $student) {
                echo "\n** Student ID: {$student->id}, Attendance Count: " . $student->attendance->count();
            }
        } catch (\Exception $e) {
            // Catch exceptions and display the error message and stack trace
            echo "\nError: " . $e->getMessage();
            echo "\nTrace: " . $e->getTraceAsString();
        } finally {
            // Ensure cleanup is always executed
            restore_error_handler();
            restore_exception_handler();
        }
    }

    public function testRelationshipSave(): void
    {
        echo "\n\n********************* Test 1-to-Many Save() *********************\n";

        try {
            $a1 = new Attendance(['course' => 'Test course 555','status'=>'Present','time'=>'2025-1-1']);
            $s1 = Student::find(2);
            $this->assertNotNull($s1, "Student with ID 2 does not exist. Check the test data.");

            $a2 = $s1->Attendance()->save($a1);
            echo "inserted record with id: ".$a2->id;

            $a3 = Attendance::find($a2->id);
            $this->assertNotNull($a3, "Attendance record with ID {$a2->id} could not be found after insertion.");

            // Compare the data between $a2 and $a3
            $this->assertEquals($a2->course, $a3->course, "The 'course' values do not match.");
            $this->assertEquals($a2->status, $a3->status, "The 'status' values do not match.");
            $this->assertEquals($a2->time, $a3->time, "The 'time' values do not match.");
    
            echo "\nRecord verified successfully. ID: {$a2->id}, Course: {$a2->course}, Status: {$a2->status}, Time: {$a2->time}";

            // Delete the record to clean up the test
            $a3->delete();
            echo "\nRecord with ID {$a3->id} deleted successfully.";
        } catch (\Exception $e) {
            // Catch exceptions and display the error message and stack trace
            echo "\nError: " . $e->getMessage();
            echo "\nTrace: " . $e->getTraceAsString();
        } finally {
            // Ensure cleanup is always executed
            restore_error_handler();
            restore_exception_handler();
        }
    }

    public function testRelationshipCreate(): void
    {
        echo "\n\n********************* Test 1-to-Many Create() *********************\n";

        try {
            // Find the student to associate the attendance record
            $s1 = Student::find(2);
            $this->assertNotNull($s1, "Student with ID 2 does not exist. Check the test data.");

            // Use the create() method to insert a new attendance record
            $a1 = $s1->attendance()->create([
                'course' => 'Test course 666',
                'status' => 'Present',
                'time' => '2025-01-02'
            ]);
            echo "\nInserted record with ID: " . $a1->id;

            // Retrieve the record to verify consistency
            $a2 = Attendance::find($a1->id);
            $this->assertNotNull($a2, "Attendance record with ID {$a1->id} could not be found after insertion.");

            // Compare the data between $a1 and $a2
            $this->assertEquals($a1->course, $a2->course, "The 'course' values do not match.");
            $this->assertEquals($a1->status, $a2->status, "The 'status' values do not match.");
            $this->assertEquals($a1->time, $a2->time, "The 'time' values do not match.");

            echo "\nRecord verified successfully. ID: {$a1->id}, Course: {$a1->course}, Status: {$a1->status}, Time: {$a1->time}";

            // Delete the record to clean up the test
            $a2->delete();
            echo "\nRecord with ID {$a2->id} deleted successfully.";
        } catch (\Exception $e) {
            // Catch exceptions and display the error message and stack trace
            echo "\nError: " . $e->getMessage();
            echo "\nTrace: " . $e->getTraceAsString();
        } finally {
            // Ensure cleanup is always executed
            restore_error_handler();
            restore_exception_handler();
        }
    }

    public function testRelationshipAssociateDisassociate(): void
    {
        echo "\n\n********************* Test 1-to-Many Associate and Disassociate *********************\n";
    
        try {
            // Create a new attendance record without associating it to any student
            $a1 = Attendance::create([
                'course' => 'Test course 777',
                'status' => 'Absent',
                'time' => '2025-01-03',
            ]);
            echo "\nInserted attendance record with ID: " . $a1->id;
    
            // Verify that the attendance record is not associated with any student
            $this->assertNull($a1->student_id, "Attendance record should not be associated with any student initially.");
    
            // Find the student to associate with the attendance record
            $s1 = Student::find(2);
            $this->assertNotNull($s1, "Student with ID 2 does not exist. Check the test data.");
    
            // Associate the attendance record with the student
            $a1->student()->associate($s1);
            $a1->save();
            echo "\nAssociated attendance record with student ID: " . $s1->id;
    
            // Verify the association
            $a2 = Attendance::find($a1->id);
            $this->assertEquals($s1->id, $a2->student_id, "Attendance record was not correctly associated with the student.");
    
            // Disassociate the attendance record from the student
            $a2->student()->dissociate();
            $a2->save();
            echo "\nDisassociated attendance record from the student.";
    
            // Verify the disassociation
            $a3 = Attendance::find($a1->id);
            $this->assertNull($a3->student_id, "Attendance record should no longer be associated with any student.");
    
            echo "\nRecord successfully disassociated. ID: {$a1->id}, Course: {$a1->course}, Status: {$a1->status}, Time: {$a1->time}";
    
            // Clean up: Delete the attendance record
            $a3->delete();
            echo "\nRecord with ID {$a3->id} deleted successfully.";
        } catch (\Exception $e) {
            // Catch exceptions and display the error message and stack trace
            echo "\nError: " . $e->getMessage();
            echo "\nTrace: " . $e->getTraceAsString();
        } finally {
            // Ensure cleanup is always executed
            restore_error_handler();
            restore_exception_handler();
        }
    }

    public function testManyToManyAttachingDetaching(): void
    {
        echo "\n\n********************* Test Many-to-Many Attaching/Detaching *********************\n";

        try {
            // Find a student and a paper to work with
            $student = Student::find(2);
            $this->assertNotNull($student, "Student with ID 1 does not exist. Check the test data.");

            $paper = Paper::find(1);
            $this->assertNotNull($paper, "Paper with ID 1 does not exist. Check the test data.");

            // Step 1: Attach a paper to the student
            $student->papers()->attach($paper->id, ['memo' => 'test']);
            echo "\nAttached Paper(ID: {$paper->id}) to Student(ID: {$student->id}) with pivot data.";

            // Verify the attachment by directly querying the pivot table
            $attachedPivot = DB::table('table_paper_student')->get();
            $a1 = $attachedPivot->where('paper_id',$paper->id);
            $a2 = $a1->where('student_id',2)->first();
            $this->assertNotNull($a2, "Failed to attach the paper to the student.");
            // Check pivot data (e.g., memo)
            $this->assertEquals('test', $a2->memo, "Pivot memo value does not match.");
            echo "\nVerified attached paper with pivot data: memo = test";

            // Step 2: Detach the paper from the student
            $student->papers()->detach($paper->id);
            echo "\nDetached Paper(ID: {$paper->id}) from Student(ID: {$student->id}).";

            // Verify the detachment
            $detachedPaper = DB::table('table_paper_student')->get();
            $d1 = $detachedPaper->where('paper_id',$paper->id);
            $d2 = $d1->where('student_id',2);
            $this->assertTrue($d2->isEmpty(), "Failed to detach the paper from the student.");
            echo "\nVerified the paper has been successfully detached.";
        } catch (\Exception $e) {
            // Catch exceptions and display the error message and stack trace
            echo "\nError: " . $e->getMessage();
            echo "\nTrace: " . $e->getTraceAsString();
        } finally {
            // Ensure cleanup is always executed
            restore_error_handler();
            restore_exception_handler();
        }
    }

    public function testManyToManySync(): void
    {
        echo "\n\n********************* Test Many-to-Many Syncing Associations *********************\n";

        try {
            // Find a student and papers to work with
            $student = Student::find(3);
            $this->assertNotNull($student,"Student with ID 2 does not exist. Check the test data.");

            $paper1 = Paper::find(1);
            $paper2 = Paper::find(2);
            $this->assertNotNull($paper1, "Paper with ID 1 does not exist. Check the test data.");
            $this->assertNotNull($paper2, "Paper with ID 2 does not exist. Check the test data.");

            // Step 1: Sync papers with the student (attach paper1 and paper2)
            $student->papers()->sync([
                $paper1->id => ['memo' => 'synced paper 1'],
                $paper2->id => ['memo' => 'synced paper 2'],
            ]);
            echo "\nSynced papers (IDs: {$paper1->id}, {$paper2->id}) with Student(ID: {$student->id}) with pivot data.";

            // fetching the entire pivot table into a collection
            $pivotCollection = DB::table('table_paper_student')->get();

            // Verify paper1 in the collection
            $paper1Pivot = $pivotCollection->where('student_id', $student->id)->where('paper_id', $paper1->id)->first();
            $this->assertNotNull($paper1Pivot, "Failed to sync Paper(ID: {$paper1->id}) to Student(ID: {$student->id}).");
            $this->assertEquals('synced paper 1', $paper1Pivot->memo, "Pivot memo for Paper(ID: {$paper1->id}) does not match.");
            echo "\nVerified synced Paper(ID: {$paper1->id}) with memo: synced paper 1";

            // Verify paper2 in the collection
            $paper2Pivot = $pivotCollection->where('student_id', $student->id)->where('paper_id', $paper2->id)->first();
            $this->assertNotNull($paper2Pivot, "Failed to sync Paper(ID: {$paper2->id}) to Student(ID: {$student->id}).");
            $this->assertEquals('synced paper 2', $paper2Pivot->memo, "Pivot memo for Paper(ID: {$paper2->id}) does not match.");
            echo "\nVerified synced Paper(ID: {$paper2->id}) with memo: synced paper 2";

            // Step 2: Resync with only one paper to test removal
            $student->papers()->sync([
                $paper1->id => ['memo' => 'synced again'],
            ]);
            echo "\nSynced only Paper(ID: {$paper1->id}) with Student(ID: {$student->id}) to test removal.";

            // fetching the entire pivot table into a collection
            $pivotCollection = DB::table('table_paper_student')->get();

            // Verify paper1 remains in the collection
            $paper1Pivot = $pivotCollection->where('student_id', $student->id)->where('paper_id', $paper1->id)->first();
            $this->assertNotNull($paper1Pivot, "Paper(ID: {$paper1->id}) was incorrectly removed during sync.");
            $this->assertEquals('synced again', $paper1Pivot->memo, "Pivot memo for Paper(ID: {$paper1->id}) does not match after resync.");
            echo "\nVerified Paper(ID: {$paper1->id}) remains after resync with updated memo.";

            // Verify paper2 has been removed from the collection
            $paper2Pivot = $pivotCollection->where('student_id', $student->id)->where('paper_id', $paper2->id)->first();
            $this->assertNull($paper2Pivot, "Paper(ID: {$paper2->id}) was not removed during sync.");
            echo "\nVerified Paper(ID: {$paper2->id}) was successfully removed during resync.";

            // Cleanup: Detach all papers from the student
            $student->papers()->detach();
            echo "\nDetached all papers for Student(ID: {$student->id}) to clean up residual records.";

        } catch (\Exception $e) {
            // Catch exceptions and display the error message and stack trace
            echo "\nError: " . $e->getMessage();
            echo "\nTrace: " . $e->getTraceAsString();
        } finally {
            // Ensure cleanup is always executed
            restore_error_handler();
            restore_exception_handler();
        }
    }

    public function testManyToManyToggle(): void
    {
        echo "\n\n********************* Test Many-to-Many Toggle Associations *********************\n";
    
        try {
            // Find a student and papers to work with
            $student = Student::find(3);
            $this->assertNotNull($student, "Student with ID 3 does not exist. Check the test data.");
    
            $paper1 = Paper::find(1);
            $paper2 = Paper::find(2);
            $this->assertNotNull($paper1, "Paper with ID 1 does not exist. Check the test data.");
            $this->assertNotNull($paper2, "Paper with ID 2 does not exist. Check the test data.");
            echo "\nLoaded student 3, and paper 1 & 2";
    
            // Step 1: Toggle papers (attach paper1 and paper2 if not attached, or detach if already attached)
            $student->papers()->toggle([1,2]);
/*             $student->papers()->toggle([
                $paper1->id => ['memo' => 'toggled paper 1'],
                $paper2->id => ['memo' => 'toggled paper 2'],
            ]); */
            echo "\nToggled papers (IDs: {$paper1->id}, {$paper2->id}) for Student(ID: {$student->id}).";
    
            // Fetch the entire pivot table into a collection
            $pivotCollection = DB::table('table_paper_student')->get();
    
            // Verify paper1 in the collection
            $paper1Pivot = $pivotCollection->where('student_id', $student->id)
                ->where('paper_id', $paper1->id)
                ->first();
    
            if ($paper1Pivot) {
                echo "\nVerified Paper(ID: {$paper1->id}) is attached with memo: {$paper1Pivot->memo}";
                $this->assertEquals('toggled paper 1', $paper1Pivot->memo, "Pivot memo for Paper(ID: {$paper1->id}) does not match.");
            } else {
                echo "\nVerified Paper(ID: {$paper1->id}) is detached.";
            }
    
            // Verify paper2 in the collection
            $paper2Pivot = $pivotCollection->where('student_id', $student->id)
                ->where('paper_id', $paper2->id)
                ->first();
    
            if ($paper2Pivot) {
                echo "\nVerified Paper(ID: {$paper2->id}) is attached with memo: {$paper2Pivot->memo}";
                $this->assertEquals('toggled paper 2', $paper2Pivot->memo, "Pivot memo for Paper(ID: {$paper2->id}) does not match.");
            } else {
                echo "\nVerified Paper(ID: {$paper2->id}) is detached.";
            }
    
            // Step 2: Toggle the papers again to ensure proper toggling behavior
            $student->papers()->toggle([
                $paper1->id => ['memo' => 'toggled again paper 1'],
                $paper2->id => ['memo' => 'toggled again paper 2'],
            ]);
            echo "\nToggled papers (IDs: {$paper1->id}, {$paper2->id}) for Student(ID: {$student->id}) again.";
    
            // Fetch the entire pivot table into a collection
            $pivotCollection = DB::table('table_paper_student')->get();
    
            // Verify that paper1 and paper2 are toggled correctly
            $paper1Pivot = $pivotCollection->where('student_id', $student->id)
                ->where('paper_id', $paper1->id)
                ->first();
            $this->assertNull($paper1Pivot, "Paper(ID: {$paper1->id}) was not properly toggled off.");
            echo "\nVerified Paper(ID: {$paper1->id}) is toggled off.";
    
            $paper2Pivot = $pivotCollection->where('student_id', $student->id)
                ->where('paper_id', $paper2->id)
                ->first();
            $this->assertNull($paper2Pivot, "Paper(ID: {$paper2->id}) was not properly toggled off.");
            echo "\nVerified Paper(ID: {$paper2->id}) is toggled off.";
    
            // Cleanup: Detach all papers from the student
            $student->papers()->detach();
            echo "\nDetached all papers for Student(ID: {$student->id}) to clean up residual records.";
    
        } catch (\Exception $e) {
            // Catch exceptions and display the error message and stack trace
            echo "\nError: " . $e->getMessage();
            echo "\nTrace: " . $e->getTraceAsString();
        } finally {
            // Ensure cleanup is always executed
            restore_error_handler();
            restore_exception_handler();
        }
    }

    public function testManyToManyUpdatingPivot(): void
    {
        echo "\n\n********************* Test Many-to-Many Updating Pivot Table *********************\n";

        try {
            // Step 1: Find the student and paper
            $student = Student::find(3);
            $this->assertNotNull($student, "Student with ID 3 does not exist. Check the test data.");

            $paper = Paper::find(1);
            $this->assertNotNull($paper, "Paper with ID 1 does not exist. Check the test data.");
            echo "\nLoaded Student(ID: {$student->id}) and Paper(ID: {$paper->id}).";

            // Step 2: Attach the paper with initial pivot data
            $student->papers()->attach($paper->id, ['memo' => 'initial memo']);
            echo "\nAttached Paper(ID: {$paper->id}) to Student(ID: {$student->id}) with memo: 'initial memo'.";

            // Step 3: Update the pivot data
            $student->papers()->updateExistingPivot($paper->id, ['memo' => 'updated memo']);
            echo "\nUpdated pivot memo to 'updated memo'.";

            // Step 4: Fetch and verify the updated pivot data
            $pivotCollection = DB::table('table_paper_student')->get();
            $pivotRecord = $pivotCollection->where('student_id', $student->id)
                ->where('paper_id', $paper->id)
                ->first();

            $this->assertNotNull($pivotRecord, "Failed to find the pivot record after update.");
            $this->assertEquals('updated memo', $pivotRecord->memo, "Pivot memo was not updated correctly.");
            echo "\nVerified updated pivot memo: {$pivotRecord->memo}";

            // Cleanup: Detach the paper to clean up
            $student->papers()->detach($paper->id);
            echo "\nDetached Paper(ID: {$paper->id}) from Student(ID: {$student->id}) to clean up.";
        } catch (\Exception $e) {
            // Catch exceptions and display the error message and stack trace
            echo "\nError: " . $e->getMessage();
            echo "\nTrace: " . $e->getTraceAsString();
        } finally {
            // Ensure cleanup is always executed
            restore_error_handler();
            restore_exception_handler();
        }
    }

    public function testTouchingParentTimestamps(): void
    {
        echo "\n\n********************* Test Touching Parent Timestamps *********************\n";

        try {
            // Step 1: Find a student and their attendance record
            $student = Student::find(1);
            $this->assertNotNull($student, "Student with ID 1 does not exist. Check the test data.");

            $attendance = Attendance::where('student_id', $student->id)->first();
            $this->assertNotNull($attendance, "No attendance record found for Student(ID: {$student->id}).");

            echo "\nLoaded Student(ID: {$student->id}) and Attendance(ID: {$attendance->id}).";

            // Step 2: Record the original updated_at timestamp of the student
            $originalStudentUpdatedAt = $student->updated_at;
            echo "\nOriginal Student updated_at: {$originalStudentUpdatedAt}";

            // Pause for a second to ensure a noticeable timestamp difference
            sleep(1);

            // Step 3: Update the attendance record
            $attendance->update(['status' => 'Absent']);
            echo "\nUpdated Attendance(ID: {$attendance->id}) status to 'Absent'.";

            // Step 4: Refresh the student record to get the latest timestamp
            $student->refresh();
            $updatedStudentUpdatedAt = $student->updated_at;
            echo "\nUpdated Student updated_at: {$updatedStudentUpdatedAt}";

            // Step 5: Assert that the student's updated_at has changed
            $this->assertTrue($updatedStudentUpdatedAt > $originalStudentUpdatedAt, "Student's updated_at was not updated when attendance was modified.");
            echo "\nVerified that Student's updated_at timestamp was updated after modifying Attendance.";

            // Cleanup: Revert the attendance status to 'Present'
            $attendance->update(['status' => 'Present']);
            echo "\nReverted Attendance(ID: {$attendance->id}) status back to 'Present'.";
        } catch (\Exception $e) {
            // Catch exceptions and display the error message and stack trace
            echo "\nError: " . $e->getMessage();
            echo "\nTrace: " . $e->getTraceAsString();
        } finally {
            // Ensure cleanup is always executed
            restore_error_handler();
            restore_exception_handler();
        }
    }
}
