<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Student extends Model
{
    // protected $connection = 'sqlite'; // Specify the database connection
    protected $connection = 'maxio'; 

    protected $table = 'table_student';

    protected $fillable = ['name', 'email', 'grades'];

    protected $keyType = 'int';
    public $incrementing = true;

    public static function boot()
    {
        parent::boot();

/*         logger('***********Student model booting', [
            'connection' => (new static)->getConnectionName(),
        ]); */

        static::ensureTableExists();
    }

    /**
     * Ensure the table exists in the database; create if it doesn't exist.
     */
    public static function ensureTableExists()
    {


        $tableName = (new static)->getTable();
        $connection = (new static)->getConnectionName();
        // If the connection is 'maxio', return without creating the table
        if ($connection == 'maxio') {
            return;
        }


        // Raw SQL to check and create the table if it doesn't exist
        $createTableSQL = "
            CREATE TABLE IF NOT EXISTS {$tableName} (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT,
                grades INTEGER NOT NULL default 0,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                updated_at TEXT DEFAULT CURRENT_TIMESTAMP
            );
        ";

        // Execute the SQL on the specified connection
        DB::connection($connection)->unprepared($createTableSQL);
    }
}
