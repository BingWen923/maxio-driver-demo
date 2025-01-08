<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Student;
use App\Models\Attendance;

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
        // Bootstrapping Laravel application
        $this->app = require __DIR__ . '/../../bootstrap/app.php';

        // Ensure database connection for the test
        $this->app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
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
    
}
