<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds the fhir_code_mappings table with HFR → FHIR code translations.
 *
 * This seeder dynamically reads existing lookup tables and creates
 * corresponding FHIR Coding entries. Run with:
 *   php artisan db:seed --class=FhirCodeMappingSeeder
 */
class FhirCodeMappingSeeder extends Seeder
{
    private const BASE = 'https://hfr.health.gov.ng/fhir/CodeSystem';
    private const HL7_ORG_TYPE = 'http://terminology.hl7.org/CodeSystem/organization-type';
    private const HL7_ROLE_CODE = 'http://terminology.hl7.org/CodeSystem/v3-RoleCode';
    private const HL7_SERVICE_CAT = 'http://terminology.hl7.org/CodeSystem/service-category';
    private const HL7_V2_0116 = 'http://terminology.hl7.org/CodeSystem/v2-0116';

    public function run(): void
    {
        $this->seedFacilityTypes();
        $this->seedLevelsOfCare();
        $this->seedOwnerships();
        $this->seedOwnershipTypes();
        $this->seedOperationalStatuses();
        $this->seedRegistrationStatuses();
        $this->seedLicenseStatuses();
        $this->seedAccreditationStatuses();
        $this->seedServiceCategories();
        $this->seedRegistryTypes();

        $this->command->info('FHIR code mappings seeded successfully.');
    }

    /**
     * lst_facility_types → FHIR Organization.type / Location.type
     */
    private function seedFacilityTypes(): void
    {
        $rows = DB::table('lst_facility_types')->select('id', 'name')->get();

        // Map common Nigerian facility type names to FHIR RoleCode where possible
        $fhirMap = [
            'hospital'                  => ['HOSP', 'Hospital'],
            'general hospital'          => ['GACH', 'General Acute Care Hospital'],
            'teaching hospital'         => ['HOSP', 'Hospital'],
            'specialist hospital'       => ['HOSP', 'Hospital'],
            'federal medical centre'    => ['HOSP', 'Hospital'],
            'clinic'                    => ['OF', 'Outpatient Facility'],
            'health centre'             => ['CHR', 'Community Health Center'],
            'health center'             => ['CHR', 'Community Health Center'],
            'primary health centre'     => ['CHR', 'Community Health Center'],
            'primary health center'     => ['CHR', 'Community Health Center'],
            'maternity home'            => ['MHSP', 'Military Hospital'],
            'nursing home'              => ['NCCF', 'Nursing or Custodial Care Facility'],
            'dispensary'                => ['PHARM', 'Pharmacy'],
            'health post'               => ['CHR', 'Community Health Center'],
            'cottage hospital'          => ['GACH', 'General Acute Care Hospital'],
            'comprehensive health centre' => ['CHR', 'Community Health Center'],
            'comprehensive health center' => ['CHR', 'Community Health Center'],
            'ward health centre'        => ['CHR', 'Community Health Center'],
            'ward health center'        => ['CHR', 'Community Health Center'],
        ];

        foreach ($rows as $row) {
            $key = strtolower(trim($row->name));
            $fhir = $fhirMap[$key] ?? [strtoupper(substr(preg_replace('/[^a-z]/', '', $key), 0, 6)), $row->name];

            $this->upsert(
                'lst_facility_types',
                $row->id,
                $row->name,
                self::BASE . '/facility-type',
                (string) $row->id,
                $row->name,
                'Organization',
                'type'
            );

            // Also store the HL7 RoleCode mapping for Location.type
            $this->upsert(
                'lst_facility_types',
                $row->id,
                $row->name,
                self::HL7_ROLE_CODE,
                $fhir[0],
                $fhir[1],
                'Location',
                'type'
            );
        }

        $this->command->info("  - Facility types: {$rows->count()} mapped");
    }

    /**
     * lst_level_of_care → FHIR Organization.type / Location.type
     */
    private function seedLevelsOfCare(): void
    {
        $rows = DB::table('lst_level_of_care')->select('id', 'name')->get();

        foreach ($rows as $row) {
            $this->upsert(
                'lst_level_of_care',
                $row->id,
                $row->name,
                self::BASE . '/level-of-care',
                (string) $row->id,
                $row->name,
                'Organization',
                'type'
            );
        }

        $this->command->info("  - Levels of care: {$rows->count()} mapped");
    }

    /**
     * lst_ownerships → FHIR Organization.type
     */
    private function seedOwnerships(): void
    {
        $rows = DB::table('lst_ownerships')->select('id', 'name')->get();

        // Map ownership to HL7 organization-type where possible
        $hl7Map = [
            'federal'    => ['govt', 'Government'],
            'state'      => ['govt', 'Government'],
            'lga'        => ['govt', 'Government'],
            'local government' => ['govt', 'Government'],
            'private'    => ['bus', 'Non-Healthcare Business'],
            'mission'    => ['reli', 'Religious Institution'],
            'faith-based' => ['reli', 'Religious Institution'],
            'ngo'        => ['crs', 'Community/Rehabilitation'],
            'military'   => ['govt', 'Government'],
        ];

        foreach ($rows as $row) {
            // HFR-native code
            $this->upsert(
                'lst_ownerships',
                $row->id,
                $row->name,
                self::BASE . '/ownership',
                (string) $row->id,
                $row->name,
                'Organization',
                'type'
            );

            // HL7 organization-type mapping
            $key = strtolower(trim($row->name));
            $hl7 = $hl7Map[$key] ?? null;
            if ($hl7) {
                $this->upsert(
                    'lst_ownerships',
                    $row->id,
                    $row->name,
                    self::HL7_ORG_TYPE,
                    $hl7[0],
                    $hl7[1],
                    'Organization',
                    'type'
                );
            }
        }

        $this->command->info("  - Ownerships: {$rows->count()} mapped");
    }

    /**
     * lst_ownership_types → FHIR Organization.type
     */
    private function seedOwnershipTypes(): void
    {
        $exists = DB::getSchemaBuilder()->hasTable('lst_ownership_types');
        if (!$exists) {
            $this->command->warn('  - lst_ownership_types table not found, skipping');
            return;
        }

        $rows = DB::table('lst_ownership_types')->select('id', 'type')->get();

        foreach ($rows as $row) {
            $this->upsert(
                'lst_ownership_types',
                $row->id,
                $row->type,
                self::BASE . '/ownership-type',
                (string) $row->id,
                $row->type,
                'Organization',
                'type'
            );
        }

        $this->command->info("  - Ownership types: {$rows->count()} mapped");
    }

    /**
     * lst_oparational_status → FHIR Location.operationalStatus (HL7 v2-0116)
     */
    private function seedOperationalStatuses(): void
    {
        $rows = DB::table('lst_oparational_status')->select('id', 'status')->get();

        $v2Map = [
            'operational'       => ['O', 'Occupied'],
            'non-operational'   => ['C', 'Closed'],
            'non operational'   => ['C', 'Closed'],
            'temporarily closed' => ['K', 'Contaminated'],
            'closed'            => ['C', 'Closed'],
            'pending'           => ['U', 'Unoccupied'],
        ];

        foreach ($rows as $row) {
            // HFR-native code
            $this->upsert(
                'lst_oparational_status',
                $row->id,
                $row->status,
                self::BASE . '/operational-status',
                (string) $row->id,
                $row->status,
                'Location',
                'operationalStatus'
            );

            // HL7 v2-0116 mapping
            $key = strtolower(trim($row->status));
            $v2 = $v2Map[$key] ?? null;
            if ($v2) {
                $this->upsert(
                    'lst_oparational_status',
                    $row->id,
                    $row->status,
                    self::HL7_V2_0116,
                    $v2[0],
                    $v2[1],
                    'Location',
                    'operationalStatus'
                );
            }
        }

        $this->command->info("  - Operational statuses: {$rows->count()} mapped");
    }

    /**
     * lst_registration_status → FHIR extension
     */
    private function seedRegistrationStatuses(): void
    {
        $exists = DB::getSchemaBuilder()->hasTable('lst_registration_status');
        if (!$exists) {
            $this->command->warn('  - lst_registration_status table not found, skipping');
            return;
        }

        $rows = DB::table('lst_registration_status')->select('id', 'status')->get();

        foreach ($rows as $row) {
            $this->upsert(
                'lst_registration_status',
                $row->id,
                $row->status,
                self::BASE . '/registration-status',
                (string) $row->id,
                $row->status,
                'Organization',
                'extension:registration-status'
            );
        }

        $this->command->info("  - Registration statuses: {$rows->count()} mapped");
    }

    /**
     * lst_license_status → FHIR extension
     */
    private function seedLicenseStatuses(): void
    {
        $exists = DB::getSchemaBuilder()->hasTable('lst_license_status');
        if (!$exists) {
            $this->command->warn('  - lst_license_status table not found, skipping');
            return;
        }

        $rows = DB::table('lst_license_status')->select('id', 'status')->get();

        foreach ($rows as $row) {
            $this->upsert(
                'lst_license_status',
                $row->id,
                $row->status,
                self::BASE . '/license-status',
                (string) $row->id,
                $row->status,
                'Organization',
                'extension:license-status'
            );
        }

        $this->command->info("  - License statuses: {$rows->count()} mapped");
    }

    /**
     * lst_accreditation_status → FHIR extension (primarily for laboratories)
     */
    private function seedAccreditationStatuses(): void
    {
        $exists = DB::getSchemaBuilder()->hasTable('lst_accreditation_status');
        if (!$exists) {
            $this->command->warn('  - lst_accreditation_status table not found, skipping');
            return;
        }

        $rows = DB::table('lst_accreditation_status')->select('id', 'status')->get();

        foreach ($rows as $row) {
            $this->upsert(
                'lst_accreditation_status',
                $row->id,
                $row->status,
                self::BASE . '/accreditation-status',
                (string) $row->id,
                $row->status,
                'Location',
                'extension:accreditation-status'
            );
        }

        $this->command->info("  - Accreditation statuses: {$rows->count()} mapped");
    }

    /**
     * lst_hosp_service_category → FHIR HealthcareService.category
     */
    private function seedServiceCategories(): void
    {
        $exists = DB::getSchemaBuilder()->hasTable('lst_hosp_service_category');
        if (!$exists) {
            $this->command->warn('  - lst_hosp_service_category table not found, skipping');
            return;
        }

        $rows = DB::table('lst_hosp_service_category')->select('id', 'description')->get();

        foreach ($rows as $row) {
            $this->upsert(
                'lst_hosp_service_category',
                $row->id,
                $row->description,
                self::BASE . '/service-category',
                (string) $row->id,
                $row->description,
                'HealthcareService',
                'category'
            );
        }

        $this->command->info("  - Service categories: {$rows->count()} mapped");
    }

    /**
     * Static registry type codes used by Organization.type to identify facility source
     */
    private function seedRegistryTypes(): void
    {
        $types = [
            ['hospital', 'Hospital', 'prov', 'Healthcare Provider'],
            ['laboratory', 'Laboratory', 'prov', 'Healthcare Provider'],
            ['pharmacy', 'Pharmacy', 'prov', 'Healthcare Provider'],
            ['imaging', 'Imaging Centre', 'prov', 'Healthcare Provider'],
        ];

        foreach ($types as [$code, $display, $hl7Code, $hl7Display]) {
            DB::table('fhir_code_mappings')->updateOrInsert(
                [
                    'hfr_table' => 'registry_type',
                    'hfr_id' => $code,
                    'fhir_system' => self::BASE . '/registry-type',
                ],
                [
                    'hfr_display' => $display,
                    'fhir_code' => $code,
                    'fhir_display' => $display,
                    'resource_type' => 'Organization',
                    'element_path' => 'type',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Also provide HL7 organization-type mapping
            DB::table('fhir_code_mappings')->updateOrInsert(
                [
                    'hfr_table' => 'registry_type',
                    'hfr_id' => $code,
                    'fhir_system' => self::HL7_ORG_TYPE,
                ],
                [
                    'hfr_display' => $display,
                    'fhir_code' => $hl7Code,
                    'fhir_display' => $hl7Display,
                    'resource_type' => 'Organization',
                    'element_path' => 'type',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('  - Registry types: 4 mapped');
    }

    /**
     * Upsert a single code mapping row.
     */
    private function upsert(
        string $hfrTable,
        mixed $hfrId,
        string $hfrDisplay,
        string $fhirSystem,
        string $fhirCode,
        string $fhirDisplay,
        string $resourceType,
        string $elementPath
    ): void {
        DB::table('fhir_code_mappings')->updateOrInsert(
            [
                'hfr_table' => $hfrTable,
                'hfr_id' => (string) $hfrId,
                'fhir_system' => $fhirSystem,
            ],
            [
                'hfr_display' => $hfrDisplay,
                'fhir_code' => $fhirCode,
                'fhir_display' => $fhirDisplay,
                'resource_type' => $resourceType,
                'element_path' => $elementPath,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
