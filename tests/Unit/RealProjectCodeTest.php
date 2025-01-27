<?php

namespace tests\Unit;

use PHPUnit\Framework\TestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


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
    protected $table = 'R_sale_types';
    protected $fillable = ['code', 'bulk_vins'];
}

class TrimLevel extends Model
{
    protected $table = 'R_trim_levels';
    protected $fillable = ['id', 'name'];
}

class VehicleProfile extends Model
{
    protected $table = 'R_vehicle_profiles';
    protected $fillable = ['vehicle_id'];

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

class Student extends Model
{
    protected $table = 'R_students';
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
}
