<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Student extends Model
{
    // protected $connection = 'sqlite'; // Specify the database connection
    protected $connection = 'minio'; 

    protected $table = 'table_student';

    protected $fillable = ['name', 'email', 'college','grades'];

    protected $keyType = 'int';
    public $incrementing = true;
}
