<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TableNoAutoInc extends Model
{
    protected $connection = 'minio';
    protected $table = 'table_noautoinc';
    protected $fillable = ['key', 'field1', 'field2', 'field3'];
    protected $keyType = 'string';
    protected $primarykey = "key";
    public $incrementing = false;
}
