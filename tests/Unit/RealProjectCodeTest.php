<?php

namespace tests\Unit;

use PHPUnit\Framework\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Phone;

class CampaignManagerVinModelVariant extends Model
{
    protected $table = 'tb_campaign_manager_vin_model_variants';
    protected $fillable = ['vin', 'name'];

    public function campaignRows(): HasMany
    {
        return $this->hasMany(CampaignRow::class, 'vin_model_variant_id');
    }
}

class CampaignRow extends Model
{
    protected $table = 'tb_campaign_rows';
    protected $fillable = ['vin_model_variant_id', 'row_data'];
    
    public function campaignManagerVinModelVariant()
    {
        return $this->belongsTo(CampaignManagerVinModelVariant::class, 'vin_model_variant_id');
    }
}

class SaleType extends Model
{
    protected $table = 'tb_sale_types'; // Table name
    protected $fillable = ['code', 'bulk_vins']; // Fillable fields

    // Ensure auto-incrementing `id` is enabled
    public $incrementing = true; // Enable auto-increment
    protected $keyType = 'int'; // Primary key type is integer
}

class TrimLevel extends Model
{
    protected $table = 'tb_trim_levels';
    protected $fillable = ['id', 'name'];
}

class VehicleProfile extends Model
{
    protected $table = 'tb_vehicle_profiles';
    protected $fillable = ['vehicle_id', 'draft', 'created_at'];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }
}

class Vehicle extends Model
{
    protected $table = 'tb_vehicles';
    protected $fillable = ['id', 'vin','status_id'];

    public function status()
    {
        return $this->belongsTo(VehicleStatus::class, 'status_id');
    }
}

class Event extends Model
{
    protected $table = 'tb_events';
    protected $fillable = ['id'];
}

class SugarCrmLead extends Model
{
    protected $table = 'tb_sugar_crm_leads';
    protected $fillable = ['user_uuid', 'sugar_uuid'];
}

class VehicleStatus extends Model
{
    protected $table = 'tb_vehicle_statuses';
    protected $fillable = ['id', 'slug'];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'status_id');
    }
}

class CareersNZJobsPathway extends Model
{
    protected $table = 'tb_careers_nz_jobs_pathways';
    protected $fillable = ['jobID', 'vocationalPathway'];
}

class AsStudentAssessment extends Model
{
    protected $table = 'tb_as_student_assessments';
    protected $fillable = ['teacher_systemUserCode', 'schoolCode'];
}

class Message extends Model
{
    protected $table = 'tb_messages';
    protected $fillable = ['systemUserCode', 'm_OtherSystemUserCode', 'm_ID'];
}

class KamarStaff extends Model
{
    protected $table = 'tb_kamar_staff';
    protected $fillable = ['last_updated'];
}

class Teacher extends Model
{
    protected $table = 'tb_teachers';
    protected $fillable = ['ui_email'];
}

class Student extends Model
{
    protected $table = 'tb_students';
    protected $fillable = ['sch_email', 'pi_email'];
}

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
                ['id' => 1, 'vin' => 'VIN001', 'status_id' => 1],
                ['id' => 2, 'vin' => 'VIN002', 'status_id' => 2],
                ['id' => 3, 'vin' => 'VIN003', 'status_id' => 5],
            ]);
            
            VehicleProfile::truncate();
            VehicleProfile::insert([
                ['vehicle_id' => 1, 'draft' => false, 'created_at' => now()->subDays(3)],
                ['vehicle_id' => 1, 'draft' => true, 'created_at' => now()->subDays(2)], // Draft record
                ['vehicle_id' => 2, 'draft' => false, 'created_at' => now()->subDays(1)],
            ]);

            echo "\n******** Initializing test data: Event ********\n";
            Event::truncate();
            Event::insert([
                ['id' => 1],
                ['id' => 2],
                ['id' => 3],
            ]);

            echo "\n******** Initializing test data: SugarCrmLead ********\n";
            SugarCrmLead::truncate();
            SugarCrmLead::insert([
                ['user_uuid' => 'existing_user_1', 'sugar_uuid' => 'existing_sugar_1'],
                ['user_uuid' => 'existing_user_2', 'sugar_uuid' => 'existing_sugar_2'],
            ]);

            echo "\n******** Initializing test data: VehicleStatus ********\n";
            VehicleStatus::truncate();
            VehicleStatus::insert([
                ['id' => 1, 'slug' => 'DealerStock', 'available' => true],
                ['id' => 2, 'slug' => 'Profiled', 'available' => true],
                ['id' => 3, 'slug' => 'NotApplicable', 'available' => true],
                ['id' => 4, 'slug' => 'Landed', 'available' => false],
                ['id' => 5, 'slug' => 'Available', 'available' => true],
            ]);

            echo "\n******** Initializing test data: Student ********\n";
            Student::truncate();
            Student::insert([
                ['id' => 1, 'sch_email' => 'student1@school.com', 'pi_email' => 'student1@personal.com'],
                ['id' => 2, 'sch_email' => 'student2@school.com', 'pi_email' => 'student2@personal.com'],
                ['id' => 3, 'sch_email' => 'student3@school.com', 'pi_email' => 'student3@personal.com'],
            ]);

            echo "\n******** Initializing test data: CareersNZJobsPathway ********\n";
            CareersNZJobsPathway::truncate();
            CareersNZJobsPathway::insert([
                ['jobID' => 101, 'vocationalPathway' => 'Health and Wellbeing'],
                ['jobID' => 102, 'vocationalPathway' => 'Construction and Infrastructure'],
            ]);

            echo "\n******** Initializing test data: AsStudentAssessment ********\n";
            AsStudentAssessment::truncate();
            AsStudentAssessment::insert([
                ['teacher_systemUserCode' => 'TCHR001', 'schoolCode' => 'SCH001'],
                ['teacher_systemUserCode' => 'TCHR002', 'schoolCode' => 'SCH002'],
                ['teacher_systemUserCode' => 'TCHR003', 'schoolCode' => 'SCH001'],
                ['teacher_systemUserCode' => 'TCHR004', 'schoolCode' => 'SCH002'],
            ]);

            echo "\n******** Initializing test data: Message ********\n";
            Message::truncate();
            Message::insert([
                ['systemUserCode' => 'TCHR001', 'm_OtherSystemUserCode' => 'TCHR002', 'm_ID' => 1],
                ['systemUserCode' => 'TCHR001', 'm_OtherSystemUserCode' => 'TCHR003', 'm_ID' => 2],
                ['systemUserCode' => 'TCHR002', 'm_OtherSystemUserCode' => 'TCHR004', 'm_ID' => 3],
            ]);

            echo "\n******** Initializing test data: KamarStaff ********\n";
            KamarStaff::truncate();
            KamarStaff::insert([
                ['last_updated' => now()->subDays(10)],
                ['last_updated' => now()->subDays(5)],
                ['last_updated' => now()->subDays(1)],
            ]);

            echo "\n******** Initializing test data: Teacher ********\n";
            Teacher::truncate();
            Teacher::insert([
                ['ui_email' => 'teacher1@example.com'],
                ['ui_email' => 'teacher2@example.com'],
                ['ui_email' => 'teacher3@example.com'],
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
        echo "****!! In the original code, the table name is hardcoded directly into the query string, which poses a risk !!";
    
        try {
            // Simulated vehicle for testing
            $vehicle = (object) ['id' => 1];
    
            // Query VehicleProfiles where the associated Vehicle matches the given vehicle ID
            // Modify the original code to dynamically retrieve and use the correct table name
            $profiles = VehicleProfile::query()->whereHas('vehicle', function ($query) use ($vehicle) {
                $query->where((new Vehicle)->getTable() . '.id', $vehicle->id);
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

    public function testFindEventById(): void
    {
        echo "\n******* Testing Event retrieval by ID *******\n";

        try {
            // Test existing Event ID
            $eventId = 2;

            // Attempt to find the Event
            $event = Event::query()->findOrFail($eventId);

            // Verify the result
            $this->assertNotNull($event, "Event with ID {$eventId} should exist.");
            $this->assertEquals($eventId, $event->id, "The retrieved Event ID does not match the expected value.");
            echo "\n******* Event with ID {$eventId} retrieved successfully: " . $event->toJson(JSON_PRETTY_PRINT) . " *******\n";

            // Test non-existent Event ID
            $nonExistentId = 999;

            try {
                Event::query()->findOrFail($nonExistentId);
                $this->fail("Expected an exception when retrieving a non-existent Event ID: {$nonExistentId}");
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                echo "\n******* Correctly caught ModelNotFoundException for non-existent Event ID: {$nonExistentId} *******\n";
                $this->assertTrue(true, "ModelNotFoundException correctly thrown for non-existent Event ID.");
            }
        } catch (\Exception $e) {
            // Handle unexpected exceptions
            echo "\nError: " . $e->getMessage();
            echo "\nTrace: " . $e->getTraceAsString();
            $this->fail('Test failed due to an unexpected exception.');
        } finally {
            restore_error_handler();
            restore_exception_handler();
        }
    }

    public function testUpdateOrCreateSugarCrmLead(): void
    {
        echo "\n******* Testing updateOrCreate for SugarCrmLead *******\n";
    
        try {
            // Test Case 1: Update an existing record
            $automoteUserId = 'existing_user_1';
            $sugarCrmAccountId = 'updated_sugar_1';
    
            $lead = SugarCrmLead::query()->updateOrCreate(
                ['user_uuid' => $automoteUserId],
                ['sugar_uuid' => $sugarCrmAccountId]
            );
    
            // Verify the updated record
            $this->assertNotNull($lead, 'Lead should not be null.');
            $this->assertEquals($automoteUserId, $lead->user_uuid, 'user_uuid does not match.');
            $this->assertEquals($sugarCrmAccountId, $lead->sugar_uuid, 'sugar_uuid was not updated.');
            echo "\n******* Updated Lead: " . $lead->toJson(JSON_PRETTY_PRINT) . " *******\n"; 

            // Test Case 2: Create a new record
            $newAutomoteUserId = 'new_user_1';
            $newSugarCrmAccountId = 'new_sugar_1';
    
            $newLead = SugarCrmLead::query()->updateOrCreate(
                ['user_uuid' => $newAutomoteUserId],
                ['sugar_uuid' => $newSugarCrmAccountId]
            );
            echo "\n******* newLead: " . $newLead->toJson(JSON_PRETTY_PRINT) . " *******\n";
    
            // Verify the new record
            $this->assertNotNull($newLead, 'New lead should not be null.');
            $this->assertEquals($newAutomoteUserId, $newLead->user_uuid, 'New user_uuid does not match.');
            $this->assertEquals($newSugarCrmAccountId, $newLead->sugar_uuid, 'New sugar_uuid does not match.');
            echo "\n******* Created New Lead: " . $newLead->toJson(JSON_PRETTY_PRINT) . " *******\n";
        } catch (\Exception $e) {
            // Handle unexpected exceptions
            echo "\nError: " . $e->getMessage();
            echo "\nTrace: " . $e->getTraceAsString();
            $this->fail('Test failed due to an exception.');
        } finally {
            restore_error_handler();
            restore_exception_handler();
        }
    }

    public function testVehicleWithLatestStatusSlug(): void
    {
        echo "\n******* Testing retrieval of Vehicle with latest status slug *******\n";

        try {
            // Simulated query for Vehicle with latest status slug
            $vehicle = Vehicle::query()
                ->with('status')
                ->orderByDesc(
                    VehicleStatus::query()
                        ->select('slug')
                        ->whereColumn('id', 'status_id')
                        ->orderBy('slug', 'desc')
                        ->limit(1)
                )
                ->first();

            // Verify the retrieved vehicle
            $this->assertNotNull($vehicle, 'No vehicle was retrieved.');
            echo "\n******* Retrieved Vehicle: " . $vehicle->toJson(JSON_PRETTY_PRINT) . " *******\n";

            // Verify the associated status
            $this->assertNotNull($vehicle->status, 'Vehicle should have an associated status.');
            echo "\n******* Retrieved Status: " . $vehicle->status->toJson(JSON_PRETTY_PRINT) . " *******\n";

            // Additional validations
            $expectedSlug = VehicleStatus::query()->orderBy('slug', 'desc')->value('slug');
            $this->assertEquals($expectedSlug, $vehicle->status->slug, 'The latest status slug does not match the expected value.');
        } catch (\Exception $e) {
            echo "\nError: " . $e->getMessage();
            echo "\nTrace: " . $e->getTraceAsString();
            $this->fail('Test failed due to an exception.');
        } finally {
            restore_error_handler();
            restore_exception_handler();
        }
    }

    public function testFilterOutVehiclesWithSpecificStatuses(): void
    {
        echo "\n******* Testing filtering of vehicles with specific statuses *******\n";

        try {
            // Query to exclude vehicles with specific statuses
            $query = Vehicle::query();
            $query->whereDoesntHave('status', function ($query) {
                // Exclude Dealer Stock, Profiled, and Not Applicable VINs
                $query->whereIn('slug', [
                    'DealerStock',
                    'Profiled',
                    'NotApplicable',
                ]);

                // Exclude Landed VINs that are not available
                $query->orWhere(function ($query) {
                    $query->where('slug', 'Landed')
                    ->where('available', false);
                });
            });

            // Execute the query and retrieve results
            $vehicles = $query->get();

            // Verify the filtered results
            echo "\n******* Retrieved Vehicles: " . $vehicles->toJson(JSON_PRETTY_PRINT) . " *******\n";
            $this->assertNotEmpty($vehicles, 'No vehicles retrieved after filtering.');
            $this->assertCount(1, $vehicles, 'The number of vehicles retrieved does not match the expected count.');
            $this->assertEquals('VIN003', $vehicles->first()->vin, 'The retrieved vehicle does not match the expected result.');
        } catch (\Exception $e) {
            echo "\nError: " . $e->getMessage();
            echo "\nTrace: " . $e->getTraceAsString();
            $this->fail('Test failed due to an exception.');
        } finally {
            // Ensure cleanup
            restore_error_handler();
            restore_exception_handler();
        }
    }

    public function testRetrieveStudentsByEmail(): void
    {
        echo "\n******* Testing retrieval of Students by sch_email or pi_email *******\n";

        try {
            // Test Case 1: Email matches sch_email
            $email = 'student1@school.com';
            $students = Student::query()
                ->where('sch_email', $email)
                ->orWhere('pi_email', $email)
                ->get();

            // Verify the results
            $this->assertNotEmpty($students, "No students found with sch_email or pi_email = {$email}.");
            $this->assertCount(1, $students, "Expected exactly 1 student for email = {$email}.");
            $this->assertEquals(1, $students->first()->id, "The retrieved student ID does not match the expected value.");

            echo "\n******* Retrieved Students (Case 1): " . $students->toJson(JSON_PRETTY_PRINT) . " *******\n";

            // Test Case 2: Email matches pi_email
            $email = 'student2@personal.com';
            $students = Student::query()
                ->where('sch_email', $email)
                ->orWhere('pi_email', $email)
                ->get();

            // Verify the results
            $this->assertNotEmpty($students, "No students found with sch_email or pi_email = {$email}.");
            $this->assertCount(1, $students, "Expected exactly 1 student for email = {$email}.");
            $this->assertEquals(2, $students->first()->id, "The retrieved student ID does not match the expected value.");

            echo "\n******* Retrieved Students (Case 2): " . $students->toJson(JSON_PRETTY_PRINT) . " *******\n";

            // Test Case 3: Email does not match any record
            $email = 'nonexistent@school.com';
            $students = Student::query()
                ->where('sch_email', $email)
                ->orWhere('pi_email', $email)
                ->get();

            // Verify the results
            $this->assertEmpty($students, "Students should not be found with sch_email or pi_email = {$email}.");
            echo "\n******* No Students Retrieved (Case 3) as expected for email: {$email} *******\n";
        } catch (\Exception $e) {
            echo "\nError: " . $e->getMessage();
            $this->fail('Test failed due to an exception.');
        } finally {
            restore_error_handler();
            restore_exception_handler();
        }
    }

    public function testFirstOrCreateCareersNZJobsPathway(): void
    {
        echo "\n******* Testing firstOrCreate for CareersNZJobsPathway *******\n";

        try {
            // Simulated data for testing
            $it = (object) ['ID' => 103];
            $path = (object) ['Name' => 'Creative Industries'];

            // Case 1: Create a new record if it does not exist
            $jobPath = CareersNZJobsPathway::query()->firstOrCreate(
                ['jobID' => $it->ID, 'vocationalPathway' => $path->Name]
            );

            // Verify the created record
            $this->assertNotNull($jobPath, 'The jobPath record should not be null.');
            $this->assertEquals($it->ID, $jobPath->jobID, 'The jobID does not match the expected value.');
            $this->assertEquals($path->Name, $jobPath->vocationalPathway, 'The vocationalPathway does not match the expected value.');

            echo "\n******* Created or Retrieved JobPath (Case 1): " . $jobPath->toJson(JSON_PRETTY_PRINT) . " *******\n";

            // Case 2: Retrieve an existing record
            $it = (object) ['ID' => 101];
            $path = (object) ['Name' => 'Health and Wellbeing'];

            $jobPath = CareersNZJobsPathway::query()->firstOrCreate(
                ['jobID' => $it->ID, 'vocationalPathway' => $path->Name]
            );

            // Verify the retrieved record
            $this->assertNotNull($jobPath, 'The jobPath record should not be null.');
            $this->assertEquals($it->ID, $jobPath->jobID, 'The jobID does not match the expected value.');
            $this->assertEquals($path->Name, $jobPath->vocationalPathway, 'The vocationalPathway does not match the expected value.');

            echo "\n******* Retrieved Existing JobPath (Case 2): " . $jobPath->toJson(JSON_PRETTY_PRINT) . " *******\n";
        } catch (\Exception $e) {
            // Handle exceptions
            echo "\nError: " . $e->getMessage();
            $this->fail('Test failed due to an exception.');
        } finally {
            restore_error_handler();
            restore_exception_handler();
        }
    }

    public function testUpdateAsStudentAssessmentTeacherCode(): void
    {
        echo "\n******* Testing update for AsStudentAssessment teacherSystemUserCode *******\n";

        try {
            // Simulated data for testing
            $teacherCodeToRemove = 'TCHR001';
            $teacherCodeToKeep = 'TCHR005';
            $schoolCode = 'SCH001';

            /* $r = AsStudentAssessment::where([
                'teacher_systemUserCode' => $teacherCodeToRemove,
                'schoolCode' => $schoolCode,
            ])->get();
            echo "\n******* r: " . $r->toJson(JSON_PRETTY_PRINT) . " *******\n";
            return; */

            // Perform the update
            $updatedCount = AsStudentAssessment::query()
                ->withoutGlobalScopes()
                ->where([
                    'teacher_systemUserCode' => $teacherCodeToRemove,
                    'schoolCode' => $schoolCode,
                ])
                ->update(['teacher_systemUserCode' => $teacherCodeToKeep]);

            // Verify the update
            $this->assertGreaterThan(0, $updatedCount, 'No records were updated.');
            echo "\n******* Number of records updated: {$updatedCount} *******\n";

            // Verify the updated record
            $updatedRecords = AsStudentAssessment::query()
                ->where([
                    'teacher_systemUserCode' => $teacherCodeToKeep,
                    'schoolCode' => $schoolCode,
                ])
                ->get();

            $this->assertCount($updatedCount, $updatedRecords, 'The updated record count does not match the expected count.');
            echo "\n******* Updated Records: " . $updatedRecords->toJson(JSON_PRETTY_PRINT) . " *******\n";

            // Verify that no records with the old teacherSystemUserCode exist
            $remainingOldRecords = AsStudentAssessment::query()
                ->where([
                    'teacher_systemUserCode' => $teacherCodeToRemove,
                    'schoolCode' => $schoolCode,
                ])
                ->get();

            $this->assertEmpty($remainingOldRecords, 'There are still records with the old teacherSystemUserCode.');
            echo "\n******* Verified that old records are removed successfully *******\n";
        } catch (\Exception $e) {
            // Handle exceptions
            echo "\nError: " . $e->getMessage();
            $this->fail('Test failed due to an exception.');
        } finally {
            restore_error_handler();
            restore_exception_handler();
        }
    }

    public function testMessagesQuery(): void
    {
        echo "\n******* Testing Message query by systemUserCode and m_OtherSystemUserCode *******\n";

        try {
            // Simulated data for testing
            $teacher = (object) ['systemUserCode' => 'TCHR001'];

            // Execute the query
            $messages = Message::query()
                ->where('systemUserCode', $teacher->systemUserCode)
                ->orWhere('m_OtherSystemUserCode', $teacher->systemUserCode)
                ->orderby('m_ID', 'ASC')
                ->get();

            // Verify the query results
            echo "\n******* Retrieved Messages: " . $messages->toJson(JSON_PRETTY_PRINT) . " *******\n";
            $this->assertNotEmpty($messages, 'No messages were retrieved for the given systemUserCode.');
            $this->assertEquals(2, $messages->count(), 'The number of retrieved messages does not match the expected count.');
        } catch (\Exception $e) {
            echo "\nError: " . $e->getMessage();
            $this->fail('Test failed due to an exception.');
        } finally {
            restore_error_handler();
            restore_exception_handler();
        }
    }

    public function testDeleteOldKamarStaffRecords(): void
    {
        echo "\n******* Testing deletion of old KamarStaff records *******\n";

        try {
            // Simulated threshold date
            $dayBefore = now()->subDays(7);

            // Execute the deletion
            $deletedCount = KamarStaff::query()->where('last_updated', '<', $dayBefore)->delete();

            // Verify the deletion
            $this->assertEquals(1, $deletedCount, 'The number of deleted records does not match the expected count.');
            echo "\n******* Number of records deleted: {$deletedCount} *******\n";

            // Verify that no old records exist
            $remainingRecords = KamarStaff::query()->where('last_updated', '<', $dayBefore)->get();
            $this->assertEmpty($remainingRecords, 'There are still old KamarStaff records remaining.');
        } catch (\Exception $e) {
            echo "\nError: " . $e->getMessage();
            $this->fail('Test failed due to an exception.');
        } finally {
            restore_error_handler();
            restore_exception_handler();
        }
    }

    public function testTeacherQueryByEmail(): void
    {
        echo "\n******* Testing Teacher query by email existence and count *******\n";

        try {
            // Simulated email for testing
            $workEmail = 'teacher2@example.com';

            // Execute the count query
            $count = Teacher::query()->where('ui_email', $workEmail)->count();

            // Verify the count
            $this->assertEquals(1, $count, "The count for email {$workEmail} does not match the expected value.");
            echo "\n******* Count of teachers with email {$workEmail}: {$count} *******\n";

            // Execute the existence query
            $exists = Teacher::query()->where('ui_email', $workEmail)->exists();

            // Verify existence
            $this->assertTrue($exists, "The email {$workEmail} should exist in the database.");
            echo "\n******* Verified existence of email {$workEmail} *******\n";
        } catch (\Exception $e) {
            echo "\nError: " . $e->getMessage();
            $this->fail('Test failed due to an exception.');
        } finally {
            restore_error_handler();
            restore_exception_handler();
        }
    }

}
