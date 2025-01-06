<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentIdCard extends Model
{
    protected $connection = 'minio';
    protected $table = 'table_idcard';
    protected $fillable = ['idnumber', 'issuedate', 'expiredate', 'phone_id'];
    protected $keyType = 'int';
    public $incrementing = true;

    // Define the inverse of the one-to-one relationship
    public function phone()
    {
        return $this->belongsTo(Phone::class, 'phone_id', 'id'); 
    }
}
