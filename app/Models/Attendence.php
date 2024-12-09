<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Attendence extends Model
{
    // attendence record for a student
    protected $connection = 'minio';
    protected $table = 'table_attendence';
    protected $fillable = ['time', 'course', 'status', 'student_id'];
    protected $keyType = 'int';
    public $incrementing = true;

    // Define the inverse of the one-to-many relationship
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id'); // 'student_id' references the id in the students table
    }
}
