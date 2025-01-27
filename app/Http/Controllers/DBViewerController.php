<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Aws\S3\S3Client;
use App\Helpers\HtmlGenerateHelper;

class DBViewerController extends Controller
{
    public function ListTables() {
        $config = Config::get('database.connections.minio'); 
        
        if (!$config) {
            throw new \Exception('minio database configuration not found.');
        }

        // Step 2: Set up S3Client with credentials from Laravel configuration
        $s3Client = new S3Client([
            'credentials' => [
                'key' => $config['key'],
                'secret' => $config['secret'],
            ],
            'region' => $config['region'],
            'version' => 'latest',
            'endpoint' => $config['endpoint'],
            'use_path_style_endpoint' => $config['use_path_style_endpoint'],
            'http' => [
                'verify' => false, // Ignore SSL
            ],
        ]);

        $bucket = $config['database'];

        // Step 3: List top-level directories (prefixes) in the bucket
        $result = $s3Client->listObjectsV2([
            'Bucket' => $bucket,
            'Delimiter' => '/',  // Ensure only top-level directories are listed
        ]);
        
        $tables = [];
        if (isset($result['CommonPrefixes'])) {
            foreach ($result['CommonPrefixes'] as $prefix) {
                $tableName = rtrim($prefix['Prefix'], '/');  // Remove trailing slash
        
                // Skip entries that start with "__"
                if (strpos($tableName, '__') === 0) {
                    continue;  // Skip this directory (reserved for the driver)
                }
                
                $tables[] = $tableName;
            }
        } else {
            throw new \Exception('No tables (folders) found in the bucket.');
        }

        echo "<h2>Available Tables</h2>";
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li><a href='?table=" . urlencode($table) . "'>$table</a></li>";
        }
        echo "</ul>";

        // Check if a table is clicked and display its data
        if (isset($_GET['table'])) {
            $selectedTable = htmlspecialchars($_GET['table']);
            echo "<h3>Data for Table: $selectedTable</h3>";

            // Display "New Record" button
//            echo "<a href='?table=$selectedTable&action=new' style='margin-bottom:10px; display:inline-block;'>+ New Record</a><br><br>";

            // Fetch table records
            $records = DB::table($selectedTable)->get(); 

            if ($records->isEmpty()) {
                echo "<p>No records found.</p>";
            } else {
                echo "<table border='1' cellpadding='10' cellspacing='0'>";
                echo "<tr>";
                foreach ($records[0] as $key => $value) {
                    echo "<th>$key</th>";
                }
//                echo "<th>Actions</th>";
                echo "</tr>";

                foreach ($records as $record) {
                    echo "<tr>";
                    foreach ($record as $key => $value) {
                        echo "<td>$value</td>";
                    }
/*                     echo "<td>
                            <a href='?table=$selectedTable&action=edit&id={$record->id}'>Edit</a> | 
                            <a href='?table=$selectedTable&action=delete&id={$record->id}' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                          </td>"; */
                    echo "</tr>";
                }
                echo "</table>";
            }

            echo "<br><a href='?'>Back to table list</a>";
            return;

            // Handle actions
            if (isset($_GET['action'])) {
                $action = $_GET['action'];

                if ($action == 'new') {
                    echo "<h3>Add New Record</h3>";
                    echo "<form method='post' action='?table=$selectedTable&action=save'>";
                    echo csrf_field();
                    echo "<input type='text' name='column1' placeholder='Column 1' required>";
                    echo "<input type='text' name='column2' placeholder='Column 2' required>";
                    echo "<button type='submit'>Save</button>";
                    echo "</form>";
                }

                if ($action == 'edit' && isset($_GET['id'])) {
                    $record = DB::table($selectedTable)->find('id',$_GET['id']);
                    echo "<h3>Edit Record</h3>";
                    echo "<form method='post' action='?table=$selectedTable&action=update&id={$record->id}'>";
                    echo csrf_field();
                    echo "<input type='text' name='column1' value='{$record->column1}' required>";
                    echo "<input type='text' name='column2' value='{$record->column2}' required>";
                    echo "<button type='submit'>Update</button>";
                    echo "</form>";
                }

                if ($action == 'delete' && isset($_GET['id'])) {
                    DB::table($selectedTable)->where('id', $_GET['id'])->delete();
                    echo "<p>Record deleted successfully.</p>";
                    echo "<a href='?table=$selectedTable'>Go Back</a>";
                }

                if ($action == 'save') {
                    DB::table($selectedTable)->insert([
                        'column1' => $_POST['column1'],
                        'column2' => $_POST['column2']
                    ]);
                    echo "<p>New record added successfully.</p>";
                    echo "<a href='?table=$selectedTable'>Go Back</a>";
                }

                if ($action == 'update' && isset($_GET['id'])) {
                    DB::table($selectedTable)
                        ->where('id', $_GET['id'])
                        ->update([
                            'column1' => $_POST['column1'],
                            'column2' => $_POST['column2']
                        ]);
                    echo "<p>Record updated successfully.</p>";
                    echo "<a href='?table=$selectedTable'>Go Back</a>";
                }
            }
        }
    }
}
