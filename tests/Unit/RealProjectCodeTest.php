<?php

namespace tests\Unit;

use PHPUnit\Framework\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Phone;

class CampaignManagerVinModelVariant extends Model
{
    protected $table = 'R_campaign_manager_vin_model_variants';
    protected $fillable = ['vin', 'name'];

    public function campaignRows(): HasMany
    {
        return $this->hasMany(CampaignRow::class, 'vin_model_variant_id');
    }
}

class CampaignRow extends Model
{
    protected $table = 'R_campaign_rows';
    protected $fillable = ['vin_model_variant_id', 'row_data'];
    
    public function campaignManagerVinModelVariant()
    {
        return $this->belongsTo(CampaignManagerVinModelVariant::class, 'vin_model_variant_id');
    }
}

class SaleType extends Model
{
    protected $table = 'R_sale_types'; // Table name
    protected $fillable = ['code', 'bulk_vins']; // Fillable fields

    // Ensure auto-incrementing `id` is enabled
    public $incrementing = true; // Enable auto-increment
    protected $keyType = 'int'; // Primary key type is integer
}

class TrimLevel extends Model
{
    protected $table = 'R_trim_levels';
    protected $fillable = ['id', 'name'];
}

class VehicleProfile extends Model
{
    protected $table = 'R_vehicle_profiles';
    protected $fillable = ['vehicle_id', 'draft', 'created_at'];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }
}

class Vehicle extends Model
{
    protected $table = 'R_vehicles';
    protected $fillable = ['id', 'vin'];
}

class Event extends Model
{
    protected $table = 'R_events';
    protected $fillable = ['id'];
}

class SugarCrmLead extends Model
{
    protected $table = 'R_sugar_crm_leads';
    protected $fillable = ['user_uuid', 'sugar_uuid'];
}

class VehicleStatus extends Model
{
    protected $table = 'R_vehicle_statuses';
    protected $fillable = ['id', 'slug'];
}

class CareersNZJobsPathway extends Model
{
    protected $table = 'R_careers_nz_jobs_pathways';
    protected $fillable = ['jobID', 'vocationalPathway'];
}

class AsStudentAssessment extends Model
{
    protected $table = 'R_as_student_assessments';
    protected $fillable = ['teacher_systemUserCode', 'schoolCode'];
}

class Message extends Model
{
    protected $table = 'R_messages';
    protected $fillable = ['systemUserCode', 'm_OtherSystemUserCode', 'm_ID'];
}

class KamarStaff extends Model
{
    protected $table = 'R_kamar_staff';
    protected $fillable = ['last_updated'];
}

class Teacher extends Model
{
    protected $table = 'R_teachers';
    protected $fillable = ['ui_email'];
}

/* class Student extends Model
{
    protected $table = 'R_students';
    protected $fillable = ['sch_email', 'pi_email'];
} */

class RealProjectCodeTest extends TestCase
{
    protected static $isDataInitialized = false;

    protected function setUp(): void
    {
        parent::setUp();

        // Bootstrapping Laravel application
        $this->app = require __DIR__ . '/../../bootstrap/app.php';

        // Ensure database connection for the test
        $this->app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        if (!self::$isDataInitialized) {
            self::$isDataInitialized = true;

            echo "\n******** Initializing test data: CampaignManagerVinModelVariant\n";
            CampaignManagerVinModelVariant::truncate();
            CampaignManagerVinModelVariant::insert([
                ['vin' => 'VIN001', 'name' => 'ModelVariantA'],
                ['vin' => 'VIN002', 'name' => 'ModelVariantB'],
                ['vin' => 'VIN003', 'name' => 'ModelVariantC'],
            ]);

            echo "\n******** Initializing test data: CampaignRow\n";
            CampaignRow::truncate();

            echo "\n******** Initializing test data: SaleType\n";
            SaleType::truncate();
            SaleType::insert([
                ['code' => 'DFLEET', 'bulk_vins' => false],
                ['code' => 'INTFLEET', 'bulk_vins' => false],
                ['code' => 'GFLEET', 'bulk_vins' => false],
                ['code' => 'RFLEET', 'bulk_vins' => false],
                ['code' => 'OTHER', 'bulk_vins' => false],
            ]);

            echo "\n******** Initializing test data: TrimLevel\n";
            TrimLevel::truncate();
            TrimLevel::insert([
                ['id' => 1, 'name' => 'Base'],
                ['id' => 2, 'name' => 'Sport'],
                ['id' => 3, 'name' => 'Luxury'],
            ]);

            echo "\n******** Initializing test data: Vehicle and VehicleProfile\n";
            Vehicle::truncate();
            Vehicle::insert([
                ['id' => 1, 'vin' => 'VIN001'],
                ['id' => 2, 'vin' => 'VIN002'],
            ]);
            
            VehicleProfile::truncate();
            VehicleProfile::insert([
                ['vehicle_id' => 1, 'draft' => false, 'created_at' => now()->subDays(3)],
                ['vehicle_id' => 1, 'draft' => true, 'created_at' => now()->subDays(2)], // Draft record
                ['vehicle_id' => 2, 'draft' => false, 'created_at' => now()->subDays(1)],
            ]);

        }
    }

    public function testExistingVins(): void
    {
        $vins = ['VIN001', 'VIN003', 'VIN999']; // Simulated test data, some existing in the database, some not
        $existingVins = CampaignManagerVinModelVariant::query()->whereIn('vin', $vins)->get()->keyBy('vin');
    
        // Validate the query results
        $this->assertArrayHasKey('VIN001', $existingVins->toArray(), 'VIN001 should exist in the database.');
        $this->assertArrayHasKey('VIN003', $existingVins->toArray(), 'VIN003 should exist in the database.');
        $this->assertArrayNotHasKey('VIN999', $existingVins->toArray(), 'VIN999 should not exist in the database.');
    
        echo "\n******* Existing VINs test passed successfully.\n";
    }

    public function testCreateVinModelVariant(): void
    {
        echo "\n******* Testing creation of a CampaignManagerVinModelVariant with associated CampaignRows *******\n";
    
        try {
            // Simulated data for the CampaignManagerVinModelVariant and associated CampaignRows
            $data = (object) [
                'vin' => 'VIN004',
                'modelVariant' => 'ModelVariantD',
                'campaignRows' => [
                    ['row_data' => 'Row Data 1'],
                    ['row_data' => 'Row Data 2'],
                ],
            ];
    
            // Create a new CampaignManagerVinModelVariant along with associated CampaignRows
            $vinModelVariant = CampaignManagerVinModelVariant::query()
                ->with('campaignRows')
                ->create([
                    'vin' => $data->vin,
                    'name' => $data->modelVariant,
                ]);
    
            // Associate campaign rows with the created VIN model variant
            foreach ($data->campaignRows as $rowData) {
                $vinModelVariant->campaignRows()->create($rowData);
            }
    
            // Verify the creation
            $this->assertNotNull($vinModelVariant, 'CampaignManagerVinModelVariant should be created.');
            $this->assertEquals($data->vin, $vinModelVariant->vin, 'VIN does not match the expected value.');
            $this->assertEquals($data->modelVariant, $vinModelVariant->name, 'Model variant name does not match the expected value.');
    
            // Verify associated CampaignRows
            $campaignRows = $vinModelVariant->campaignRows;
            echo "\n*** campaignRows:$campaignRows";
            $this->assertNotEmpty($campaignRows, 'CampaignRows should not be empty.');
            $this->assertCount(count($data->campaignRows), $campaignRows, 'The number of associated CampaignRows does not match.');
    
            echo "\n******* CampaignManagerVinModelVariant and associated CampaignRows created successfully *******\n";
    
        } catch (\Exception $e) {
            // Handle exceptions
            echo "\nError: " . $e->getMessage();
            echo "\nTrace: " . $e->getTraceAsString();
            $this->fail('Test failed due to an exception.');
        } finally {
            // Ensure cleanup
            restore_error_handler();
            restore_exception_handler();
        }
    }

    public function testBulkUpdateSaleTypes(): void
    {
        echo "\n******* Testing bulk update of SaleType records *******\n";

        try {
            // Define the codes to update
            $codes = ['DFLEET', 'INTFLEET', 'GFLEET', 'RFLEET'];

            // Perform the bulk update
            SaleType::query()->whereIn('code', $codes)->get()->each(function (SaleType $saleType) {
                $saleType->bulk_vins = true;
                $saleType->save();
            });

            // Verify that the specified records were updated
            $updatedRecords = SaleType::query()->whereIn('code', $codes)->get();
            foreach ($updatedRecords as $record) {
                $this->assertTrue($record->bulk_vins, "SaleType with code {$record->code} should have bulk_vins set to true.");
            }

            // Verify that records not in the update list remain unchanged
            $nonUpdatedRecords = SaleType::query()->whereNotIn('code', $codes)->get();
            foreach ($nonUpdatedRecords as $record) {
                $this->assertFalse($record->bulk_vins, "SaleType with code {$record->code} should have bulk_vins set to false.");
            }

            echo "\n******* SaleType bulk update test passed successfully *******\n";
        } catch (\Exception $e) {
            // Handle exceptions
            echo "\nError: " . $e->getMessage();
            echo "\nTrace: " . $e->getTraceAsString();
            $this->fail('Test failed due to an exception.');
        } finally {
            // Ensure cleanup
            restore_error_handler();
            restore_exception_handler();
        }
    }

    public function testFindTrimLevelById(): void
    {
        echo "\n******* Testing retrieval of a TrimLevel by ID *******\n";
    
        try {
            // Case 1: Test existing ID
            $id = 2;
    
            // Retrieve the TrimLevel record
            $trimLevel = TrimLevel::query()->where('id', $id)->firstOrFail();
    
            // Verify the retrieval
            $this->assertNotNull($trimLevel, "TrimLevel with ID {$id} should exist.");
            $this->assertEquals('Sport', $trimLevel->name, "The name of the TrimLevel with ID {$id} should be 'Sport'.");
    
            echo "\n******* TrimLevel record retrieved successfully: ID = {$trimLevel->id}, Name = {$trimLevel->name} *******\n";
    
            // Case 2: Test non-existent ID
            $nonExistentId = 999;
    
            try {
                TrimLevel::query()->where('id', $nonExistentId)->firstOrFail();
                $this->fail("Expected an exception when retrieving a TrimLevel with a non-existent ID: {$nonExistentId}");
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                echo "\n******* Correctly caught ModelNotFoundException for non-existent ID: {$nonExistentId} *******\n";
                $this->assertTrue(true, "ModelNotFoundException correctly thrown for non-existent ID.");
            }
    
        } catch (\Exception $e) {
            // Handle unexpected exceptions
            echo "\nError: " . $e->getMessage();
            echo "\nTrace: " . $e->getTraceAsString();
            $this->fail('Test failed due to an exception.');
        } finally {
            // Ensure cleanup
            restore_error_handler();
            restore_exception_handler();
        }
    }

    public function testVehicleProfilesWithVehicle(): void
    {
        echo "\n******* Testing retrieval of VehicleProfiles with associated Vehicle *******";
        echo "\n*** Note: The current driver does not support nested queries.\n";
    
        try {
            // Simulated vehicle for testing
            $vehicle = (object) ['id' => 1];
    
            // Query VehicleProfiles where the associated Vehicle matches the given vehicle ID
            $profiles = VehicleProfile::query()->whereHas('vehicle', function ($query) use ($vehicle) {
                $query->where('vehicles.id', $vehicle->id);
            })->get();
    
            // Verify the query results
            $this->assertNotEmpty($profiles, 'No VehicleProfiles found for the given vehicle ID.');
            $this->assertCount(1, $profiles, 'The number of VehicleProfiles retrieved does not match the expected count.');
    
            // Log the retrieved profiles
            echo "\n******* Retrieved VehicleProfiles: " . $profiles->toJson(JSON_PRETTY_PRINT) . " *******\n";
        } catch (\Exception $e) {
            // Simplified error message
            echo "\nError: Unable to complete the test due to unsupported nested query.\n";
            $this->fail('Test failed as expected due to unsupported nested query.');
        } finally {
            // Ensure cleanup
            restore_error_handler();
            restore_exception_handler();
        }
    }

    public function testLatestNonDraftRecordByVin(): void
    {
        echo "\n******* Testing retrieval of the latest non-draft VehicleProfile record by VIN *******\n";

        try {
            // Simulated VIN for testing
            $vin = 'VIN001';

            // Run the query on VehicleProfile
            $record = VehicleProfile::query()
                ->where('vehicle_id', Vehicle::query()->where('vin', $vin)->value('id'))
                ->where('draft', false)
                ->latest('created_at')
                ->first();

            // Verify the result
            $this->assertNotNull($record, 'No VehicleProfile record found for the given VIN and non-draft status.');
            $this->assertEquals(1, $record->vehicle_id, 'Vehicle ID does not match the expected value.');
            $this->assertFalse($record->draft, 'The record is not marked as non-draft.');
            echo "\n*** Retrieved VehicleProfile record: " . $record->toJson(JSON_PRETTY_PRINT) . " ***\n";
        } catch (\Exception $e) {
            echo "\nError: Failed to retrieve the latest non-draft VehicleProfile record for VIN $vin.";
            $this->fail('Test failed due to an exception.');
        } finally {
            // Ensure cleanup
            restore_error_handler();
            restore_exception_handler();
        }
    }

    public function testFindSaleTypeById(): void
    {
        echo "\n******* Testing SaleType retrieval by ID *******\n";

        try {
            // Simulated model object for testing
            $model = (object) ['id' => 2];

            // Retrieve the SaleType by its ID
            //$saleType = SaleType::query()->find($model->id);
            $saleType = SaleType::find(2);

            // Verify the result
            $this->assertNotNull($saleType, "SaleType with ID {$model->id} should exist.");
            $this->assertEquals('INTFLEET', $saleType->code, "The code for SaleType ID {$model->id} does not match the expected value.");
            echo "\n******* SaleType with ID {$model->id} found successfully: " . $saleType->toJson(JSON_PRETTY_PRINT) . " *******\n";
        } catch (\Exception $e) {
            echo "\nError: " . $e->getMessage();
            echo "\nTrace: " . $e->getTraceAsString();
            $this->fail('Test failed due to an exception.');
        } finally {
            restore_error_handler();
            restore_exception_handler();
        }
    }

}
