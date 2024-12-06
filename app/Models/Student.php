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
        return $this->hasOne(Phones::class, 'student_id', 'id'); // 'student_id' is the foreign key in the phones table
    }
}
