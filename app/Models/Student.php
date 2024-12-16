<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $connection = 'minio';
    protected $table = 'table_student';
    protected $fillable = ['name', 'email', 'college', 'grades'];
    protected $keyType = 'int';
    public $incrementing = true;

    // Define the one-to-one relationship
    public function phone()
    {
        return $this->hasOne(Phone::class, 'student_id', 'id'); // 'student_id' is the foreign key in the phones table
    }

    public function attendence()
    {
        return $this->hasMany(Attendence::class, 'student_id', 'id'); // 'student_id' is the foreign key in the Attendence table
    }

    // Define the many-to-many relationship for papers
    public function papers()
    {
        // assuming there's a 'table_paper_student' pivot table with 'student_id' and 'paper_id' fields
        return $this->belongsToMany(Paper::class, 'table_paper_student', 'student_id', 'paper_id');
    }
}
