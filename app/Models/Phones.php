<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Phones extends Model
{
    protected $connection = 'minio';
    protected $table = 'table_phones';
    protected $fillable = ['cellphone', 'home', 'company', 'student_id'];
    protected $keyType = 'int';
    public $incrementing = true;

    // Define the inverse of the one-to-one relationship
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id'); // 'student_id' references the id in the students table
    }
}
