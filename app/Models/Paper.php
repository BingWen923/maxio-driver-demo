<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Paper extends Model
{
    // attendence record for a student
    protected $connection = 'minio';
    protected $table = 'table_paper';
    protected $fillable = ['code', 'title'];
    protected $keyType = 'int';
    public $incrementing = true;

    // Define the inverse of the many-to-many relationship
    public function students()
    {
        return $this->belongsToMany(Student::class, 'table_paper_student', 'paper_id', 'student_id');
    }
}
