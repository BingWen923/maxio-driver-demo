<?php

namespace tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\TableNoAutoInc;


class ModelTest extends TestCase
{
    protected function setUp(): void
    {
        // Bootstrapping Laravel application
        $this->app = require __DIR__ . '/../../bootstrap/app.php';

        // Ensure database connection for the test
        $this->app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    }

    public function testTableNoAutoIncReadWrite(): void
    {
        echo "\n\n********************* Test TableNoAutoInc Read/Write *********************\n";

        try {
            // Step 1: Create a unique key for the test
            $uniqueKey = 'custom_key_' . uniqid();
            echo "\nGenerated unique key: {$uniqueKey}";

            // Step 2: Insert a new record with the custom key
            $record = TableNoAutoInc::create([
                'key'    => $uniqueKey,
                'field1' => 'Test Data 1',
                'field2' => 'Test Data 2',
                'field3' => 'Test Data 3',
            ]);
            echo "\nInserted record with key: {$record->key}";

            // Step 3: Retrieve the record by the custom key
            $retrievedRecord = TableNoAutoInc::find($uniqueKey);
            $this->assertNotNull($retrievedRecord, "Failed to retrieve the record with key: {$uniqueKey}");
            echo "\nRetrieved record with key: {$retrievedRecord->key}";

            // Step 4: Verify the data matches
            $this->assertEquals('Test Data 1', $retrievedRecord->field1, "Field1 does not match.");
            $this->assertEquals('Test Data 2', $retrievedRecord->field2, "Field2 does not match.");
            $this->assertEquals('Test Data 3', $retrievedRecord->field3, "Field3 does not match.");
            echo "\nVerified that all fields match the inserted data.";

            // Step 5: Update the record
            $retrievedRecord->update([
                'field1' => 'Updated Data 1',
                'field2' => 'Updated Data 2',
            ]);
            echo "\nUpdated record with new values for field1 and field2.";

            // Step 6: Retrieve and verify the updated data
            $updatedRecord = TableNoAutoInc::find($uniqueKey);
            $this->assertEquals('Updated Data 1', $updatedRecord->field1, "Updated Field1 does not match.");
            $this->assertEquals('Updated Data 2', $updatedRecord->field2, "Updated Field2 does not match.");
            $this->assertEquals('Test Data 3', $updatedRecord->field3, "Field3 should remain unchanged.");
            echo "\nVerified that updates were applied correctly.";

            // Note: Data is intentionally not deleted for persistence
            echo "\nTest completed. Data retained in the database.";
        } catch (\Exception $e) {
            // Catch and display exceptions
            echo "\nError: " . $e->getMessage();
            echo "\nTrace: " . $e->getTraceAsString();
        } finally {
            // Ensure cleanup of error handlers
            restore_error_handler();
            restore_exception_handler();
        }
    }
}