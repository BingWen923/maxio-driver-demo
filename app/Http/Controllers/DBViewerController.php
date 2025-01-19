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
        //dd($tables);
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

            $records = DB::table($selectedTable)->get(); 
            $htmlTable = HtmlGenerateHelper::generateTable($records);
            echo $htmlTable;
            echo "<br><a href='?'>Back to table list</a>";
        }
    }
}
