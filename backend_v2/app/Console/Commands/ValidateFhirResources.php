<?php

namespace App\Console\Commands;

use App\Fhir\Transformers\OrganizationTransformer;
use App\Fhir\Transformers\LocationTransformer;
use App\Fhir\Validation\FhirValidator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Artisan command to validate generated FHIR resources against
 * the official HL7 FHIR Validator.
 *
 * Usage:
 *   php artisan fhir:validate
 *   php artisan fhir:validate --resource=Organization --sample=5
 *   php artisan fhir:validate --resource=Location --id=hosp-123
 */
class ValidateFhirResources extends Command
{
    protected $signature = 'fhir:validate
        {--resource=all : Resource type to validate (Organization, Location, HealthcareService, all)}
        {--id= : Specific resource ID to validate}
        {--sample=10 : Number of random resources to validate}
        {--quick : Only run quick local checks (no external validator)}';

    protected $description = 'Validate FHIR resources against the official HL7 FHIR Validator';

    public function handle(): int
    {
        $resourceType = $this->option('resource');
        $specificId = $this->option('id');
        $sample = (int) $this->option('sample');
        $quickOnly = $this->option('quick');

        $this->info('FHIR Resource Validation');
        $this->info('========================');
        $this->info("Validator URL: " . config('hfr.fhir.validator_url'));
        $this->newLine();

        $validator = new FhirValidator();
        $orgTransformer = new OrganizationTransformer();
        $locTransformer = new LocationTransformer();

        $totalErrors = 0;
        $totalWarnings = 0;
        $totalValid = 0;

        // Validate Organization resources
        if (in_array($resourceType, ['all', 'Organization'])) {
            $this->info('--- Organization Resources ---');
            $resources = $this->getOrganizationResources($orgTransformer, $sample);

            foreach ($resources as $resource) {
                $quickErrors = FhirValidator::quickCheck($resource);
                if (!empty($quickErrors)) {
                    $this->error("  QUICK CHECK FAILED: {$resource['id']}");
                    foreach ($quickErrors as $err) {
                        $this->line("    - {$err}");
                    }
                    $totalErrors++;
                    continue;
                }

                if ($quickOnly) {
                    $this->info("  OK (quick): {$resource['id']}");
                    $totalValid++;
                    continue;
                }

                $result = $validator->validate($resource);
                if ($result->valid) {
                    $this->info("  VALID: {$resource['id']}");
                    $totalValid++;
                } else {
                    $this->error("  INVALID: {$resource['id']}");
                    foreach ($result->errors as $err) {
                        $this->line("    ERROR: {$err}");
                    }
                    $totalErrors++;
                }
                foreach ($result->warnings as $warn) {
                    $this->warn("    WARNING: {$warn}");
                    $totalWarnings++;
                }
            }
        }

        // Validate Location resources
        if (in_array($resourceType, ['all', 'Location'])) {
            $this->newLine();
            $this->info('--- Location Resources ---');
            $resources = $this->getLocationResources($locTransformer, $sample);

            foreach ($resources as $resource) {
                $quickErrors = FhirValidator::quickCheck($resource);
                if (!empty($quickErrors)) {
                    $this->error("  QUICK CHECK FAILED: {$resource['id']}");
                    foreach ($quickErrors as $err) {
                        $this->line("    - {$err}");
                    }
                    $totalErrors++;
                    continue;
                }

                if ($quickOnly) {
                    $this->info("  OK (quick): {$resource['id']}");
                    $totalValid++;
                    continue;
                }

                $result = $validator->validate($resource);
                if ($result->valid) {
                    $this->info("  VALID: {$resource['id']}");
                    $totalValid++;
                } else {
                    $this->error("  INVALID: {$resource['id']}");
                    foreach ($result->errors as $err) {
                        $this->line("    ERROR: {$err}");
                    }
                    $totalErrors++;
                }
                foreach ($result->warnings as $warn) {
                    $this->warn("    WARNING: {$warn}");
                    $totalWarnings++;
                }
            }
        }

        // Summary
        $this->newLine();
        $this->info('========================');
        $this->info("Valid: {$totalValid} | Errors: {$totalErrors} | Warnings: {$totalWarnings}");

        return $totalErrors > 0 ? 1 : 0;
    }

    protected function getOrganizationResources(OrganizationTransformer $transformer, int $sample): array
    {
        $resources = [];

        $facilities = DB::table('hs_hospitals_history')
            ->leftJoin('ou_states', 'hs_hospitals_history.state_id', '=', 'ou_states.id')
            ->leftJoin('ou_lgas', 'hs_hospitals_history.lga_id', '=', 'ou_lgas.id')
            ->leftJoin('ou_wards', 'hs_hospitals_history.ward_id', '=', 'ou_wards.id')
            ->leftJoin('lst_facility_types', 'hs_hospitals_history.facility_type_id', '=', 'lst_facility_types.id')
            ->leftJoin('lst_level_of_care', 'hs_hospitals_history.facility_level_id', '=', 'lst_level_of_care.id')
            ->leftJoin('lst_ownerships', 'hs_hospitals_history.ownership_id', '=', 'lst_ownerships.id')
            ->leftJoin('lst_ownership_types', 'hs_hospitals_history.ownership_type_id', '=', 'lst_ownership_types.id')
            ->leftJoin('lst_oparational_status', 'hs_hospitals_history.operational_status_id', '=', 'lst_oparational_status.id')
            ->leftJoin('lst_registration_status', 'hs_hospitals_history.registration_status_id', '=', 'lst_registration_status.id')
            ->leftJoin('lst_license_status', 'hs_hospitals_history.license_status_id', '=', 'lst_license_status.id')
            ->select(
                'hs_hospitals_history.*',
                'ou_states.name as state_name',
                'ou_lgas.name as lga_name',
                'ou_wards.name as ward_name',
                'lst_facility_types.name as facility_type_name',
                'lst_level_of_care.name as facility_level_name',
                'lst_ownerships.name as ownership_name',
                'lst_ownership_types.type as ownership_type',
                'lst_oparational_status.status as operational_status_name',
                'lst_registration_status.status as registration_status_name',
                'lst_license_status.status as license_status_name'
            )
            ->inRandomOrder()
            ->limit($sample)
            ->get();

        foreach ($facilities as $f) {
            $resources[] = $transformer->transform($f, 'hospital');
        }

        return $resources;
    }

    protected function getLocationResources(LocationTransformer $transformer, int $sample): array
    {
        $resources = [];

        $facilities = DB::table('hs_hospitals_history')
            ->leftJoin('ou_states', 'hs_hospitals_history.state_id', '=', 'ou_states.id')
            ->leftJoin('ou_lgas', 'hs_hospitals_history.lga_id', '=', 'ou_lgas.id')
            ->leftJoin('ou_wards', 'hs_hospitals_history.ward_id', '=', 'ou_wards.id')
            ->leftJoin('lst_facility_types', 'hs_hospitals_history.facility_type_id', '=', 'lst_facility_types.id')
            ->leftJoin('lst_level_of_care', 'hs_hospitals_history.facility_level_id', '=', 'lst_level_of_care.id')
            ->leftJoin('lst_ownerships', 'hs_hospitals_history.ownership_id', '=', 'lst_ownerships.id')
            ->leftJoin('lst_ownership_types', 'hs_hospitals_history.ownership_type_id', '=', 'lst_ownership_types.id')
            ->leftJoin('lst_oparational_status', 'hs_hospitals_history.operational_status_id', '=', 'lst_oparational_status.id')
            ->leftJoin('lst_registration_status', 'hs_hospitals_history.registration_status_id', '=', 'lst_registration_status.id')
            ->leftJoin('lst_license_status', 'hs_hospitals_history.license_status_id', '=', 'lst_license_status.id')
            ->select(
                'hs_hospitals_history.*',
                'ou_states.name as state_name',
                'ou_lgas.name as lga_name',
                'ou_wards.name as ward_name',
                'lst_facility_types.name as facility_type_name',
                'lst_level_of_care.name as facility_level_name',
                'lst_ownerships.name as ownership_name',
                'lst_ownership_types.type as ownership_type',
                'lst_oparational_status.status as operational_status_name',
                'lst_registration_status.status as registration_status_name',
                'lst_license_status.status as license_status_name'
            )
            ->inRandomOrder()
            ->limit($sample)
            ->get();

        foreach ($facilities as $f) {
            $resources[] = $transformer->transform($f, 'hospital');
        }

        return $resources;
    }
}
