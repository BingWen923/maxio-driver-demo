<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Student;
use App\Models\Attendence;

class RelationshipTest extends TestCase
{
    /*
      relationships in the demo
      one to one    -   student to phone
      one to many   -   student to attendence
      many to many  -   student to paper
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
        try {
            $student = Student::find(1);
            $latestAtt = $student->hasOne(Attendence::class)->latestOfMany()->first();
            $oldestAtt = $student->hasOne(Attendence::class)->oldestOfMany()->first();
            $att = $student->hasOne(Attendence::class)->ofMany('id', 'max')->first();

            echo "\n*********************latest ID: " . ($latestAtt?->id ?? 'N/A');
            echo "\n*********************oldest ID: " . ($oldestAtt?->id ?? 'N/A');
            echo "\n*********************max ID: " . ($att?->id ?? 'N/A');

            $maxid = Attendence::where('student_id', '1')->max('id');
            $minid = Attendence::where('student_id', '1')->min('id');
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
    
}
