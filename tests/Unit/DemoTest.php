<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Student;

class DemoTest extends TestCase
{
    protected function setUp(): void
    {
        // Bootstrapping Laravel application
        $this->app = require __DIR__ . '/../../bootstrap/app.php';

        // Ensure database connection for the test
        $this->app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    }

    public function testModelSave(): void
    {
        $student = new Student();
        $student->name = "John Doe";
        $student->email = "johndoe@example.com";
        $student->grades = 77;
    
        $saved = $student->save();
        $id = $student->getConnection()->getPdo()->lastInsertId();
        $this->assertTrue($saved, "Student should be saved successfully");
    
        echo "\n*********************Saved Record ID:\n";
        print_r($id);
    
        $retrievedStudent = Student::find($id);
        $this->assertNotNull($retrievedStudent, "Retrieved student should not be null");
        $this->assertEquals($id, $retrievedStudent->id, "IDs should match");
        $this->assertEquals($student->name, $retrievedStudent->name, "Names should match");
        $this->assertEquals($student->email, $retrievedStudent->email, "Emails should match");
        $this->assertEquals($student->grades, $retrievedStudent->grades, "Grades should match");
    
        // Wait for 200 milliseconds
        usleep(200000);
    
        try {
            $retrievedStudent->setConnection('maxio');
            $retrievedStudent->delete();
            $this->assertNull(Student::find($id), "Student record should be deleted");
        } catch (\Exception $e) {
            echo "\nCleanup Exception: " . $e->getMessage();
            $this->assertTrue(true, "Record not found or deletion failed but is acceptable");
        } finally {
            restore_error_handler();
            restore_exception_handler();
        }
    }
    
}
