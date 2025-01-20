<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Attendance_through extends Model
{
    // this model is for hasManyThrough test
    protected $connection = 'minio';
    protected $table = 'table_attendance_through';
    protected $fillable = ['testfield', 'attendance_id'];
    protected $keyType = 'int';
    public $incrementing = true;

    // Define the inverse of the one-to-many relationship
    public function attendance()
    {
        return $this->belongsTo(Attendance::class, 'attendance_id', 'id'); // 'attendence_id' references the id in the attendance table
    }
}
