<?php

namespace Tests\Unit\Fhir;

use Tests\TestCase;
use App\Fhir\Transformers\OrganizationTransformer;
use App\Fhir\Transformers\LocationTransformer;
use App\Fhir\Transformers\HealthcareServiceTransformer;
use App\Fhir\Transformers\BundleTransformer;
use App\Fhir\Transformers\CapabilityStatementBuilder;

class TransformerTest extends TestCase
{
    private function makeFacility(array $overrides = []): object
    {
        return (object) array_merge([
            'id' => 1,
            'facility_name' => 'Lagos General Hospital',
            'facility_code' => 'RC/0001/2024',
            'registration_no' => 'HFR-0001',
            'state_name' => 'Lagos',
            'lga_name' => 'Ikeja',
            'ward_name' => 'Alausa',
            'physical_location' => '10 Allen Avenue, Ikeja',
            'postal_address' => '100001',
            'phone_number' => '+2341234567',
            'alternate_number' => null,
            'email' => 'info@lagoshospital.ng',
            'website' => 'https://lagoshospital.ng',
            'latitude' => '6.6018',
            'longitude' => '3.3515',
            'facility_type_name' => 'General Hospital',
            'facility_type_id' => 2,
            'facility_level' => 'Secondary',
            'facility_level_id' => 2,
            'ownership' => 'State',
            'ownership_id' => 2,
            'ownership_type' => 'Public',
            'ownership_type_id' => 1,
            'operation_status' => 'Operational',
            'operational_status_id' => 1,
            'registration_status' => 'Registered',
            'registration_status_id' => 1,
            'license_status' => 'Licensed',
            'license_status_id' => 1,
            'start_date' => '2010-01-01',
            'hours_of_operation' => 'Monday-Friday,08:00-17:00',
            'days_of_operation' => 'Monday,Tuesday,Wednesday,Thursday,Friday',
            'num_beds' => 200,
            'num_doctors' => 50,
            'num_pharmacists' => 10,
            'num_pharmacy_technicians' => 5,
            'num_dentists' => 4,
            'num_dental_technicians' => 3,
            'num_nurses' => 80,
            'num_midwifes' => 25,
            'num_nurse_midwife' => 15,
            'num_lab_technicians' => 12,
            'num_lab_scientists' => 8,
            'num_him_officers' => 3,
            'num_community_health_officer' => 6,
            'num_community_extension_workers' => 10,
            'num_jun_community_extension_worker' => 4,
            'num_env_health_officers' => 2,
            'onsite_pharmacy' => 1,
            'onsite_laboratory' => 1,
            'onsite_imaging' => 0,
            'ambulance_services' => 1,
            'updated_at' => '2024-01-15 10:30:00',
        ], $overrides);
    }

    // ── Organization Transformer ────────────────────────────────

    public function test_organization_transform_returns_valid_structure(): void
    {
        $transformer = new OrganizationTransformer();
        $result = $transformer->transform($this->makeFacility(), 'hospital');

        $this->assertEquals('Organization', $result['resourceType']);
        $this->assertStringStartsWith('hosp-', $result['id']);
        $this->assertTrue($result['active']);
        $this->assertEquals('Lagos General Hospital', $result['name']);
        $this->assertArrayHasKey('identifier', $result);
        $this->assertArrayHasKey('type', $result);
        $this->assertArrayHasKey('address', $result);
        $this->assertArrayHasKey('telecom', $result);
        $this->assertArrayHasKey('extension', $result);
        $this->assertArrayHasKey('meta', $result);
    }

    public function test_organization_id_prefix_varies_by_source(): void
    {
        $transformer = new OrganizationTransformer();

        $hosp = $transformer->transform($this->makeFacility(), 'hospital');
        $this->assertStringStartsWith('hosp-', $hosp['id']);

        $lab = $transformer->transform($this->makeFacility(), 'laboratory');
        $this->assertStringStartsWith('lab-', $lab['id']);

        $pharm = $transformer->transform($this->makeFacility(), 'pharmacy');
        $this->assertStringStartsWith('pharm-', $pharm['id']);

        $img = $transformer->transform($this->makeFacility(), 'imaging');
        $this->assertStringStartsWith('img-', $img['id']);
    }

    public function test_organization_telecom_includes_phone_and_email(): void
    {
        $transformer = new OrganizationTransformer();
        $result = $transformer->transform($this->makeFacility(), 'hospital');

        $systems = array_column($result['telecom'], 'system');
        $this->assertContains('phone', $systems);
        $this->assertContains('email', $systems);
    }

    public function test_organization_active_maps_operational_status(): void
    {
        $transformer = new OrganizationTransformer();

        $operational = $transformer->transform(
            $this->makeFacility(['operation_status' => 'Operational']),
            'hospital'
        );
        $this->assertTrue($operational['active']);

        $closed = $transformer->transform(
            $this->makeFacility(['operation_status' => 'Closed']),
            'hospital'
        );
        $this->assertFalse($closed['active']);
    }

    public function test_organization_address_includes_state_and_lga(): void
    {
        $transformer = new OrganizationTransformer();
        $result = $transformer->transform($this->makeFacility(), 'hospital');

        $address = $result['address'][0] ?? null;
        $this->assertNotNull($address);
        $this->assertEquals('Lagos', $address['state']);
        $this->assertEquals('Ikeja', $address['district']);
    }

    // ── Location Transformer ────────────────────────────────────

    public function test_location_transform_returns_valid_structure(): void
    {
        $transformer = new LocationTransformer();
        $result = $transformer->transform($this->makeFacility(), 'hospital');

        $this->assertEquals('Location', $result['resourceType']);
        $this->assertStringStartsWith('loc-hosp-', $result['id']);
        $this->assertEquals('Lagos General Hospital', $result['name']);
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('position', $result);
        $this->assertArrayHasKey('address', $result);
        $this->assertArrayHasKey('type', $result);
        $this->assertArrayHasKey('managingOrganization', $result);
    }

    public function test_location_position_contains_coordinates(): void
    {
        $transformer = new LocationTransformer();
        $result = $transformer->transform($this->makeFacility(), 'hospital');

        $this->assertEquals(6.6018, $result['position']['latitude']);
        $this->assertEquals(3.3515, $result['position']['longitude']);
    }

    public function test_location_status_maps_correctly(): void
    {
        $transformer = new LocationTransformer();

        $active = $transformer->transform(
            $this->makeFacility(['operation_status' => 'Operational']),
            'hospital'
        );
        $this->assertEquals('active', $active['status']);

        $suspended = $transformer->transform(
            $this->makeFacility(['operation_status' => 'Temporarily Closed']),
            'hospital'
        );
        $this->assertEquals('suspended', $suspended['status']);
    }

    public function test_location_managing_organization_reference(): void
    {
        $transformer = new LocationTransformer();
        $result = $transformer->transform($this->makeFacility(['id' => 42]), 'hospital');

        $this->assertEquals('Organization/hosp-42', $result['managingOrganization']['reference']);
    }

    // ── HealthcareService Transformer ───────────────────────────

    public function test_healthcare_service_transform_returns_valid_structure(): void
    {
        $transformer = new HealthcareServiceTransformer();
        $service = (object) [
            'id' => 10,
            'hospital_id' => 1,
            'service_name' => 'Cardiology',
            'service_category_name' => 'Clinical Services',
            'service_category_id' => 1,
            'service_id' => 5,
        ];

        $result = $transformer->transform($service, 'hospital');

        $this->assertEquals('HealthcareService', $result['resourceType']);
        $this->assertEquals('svc-10', $result['id']);
        $this->assertEquals('Cardiology', $result['name']);
        $this->assertArrayHasKey('providedBy', $result);
        $this->assertArrayHasKey('category', $result);
        $this->assertArrayHasKey('type', $result);
    }

    // ── Bundle Transformer ──────────────────────────────────────

    public function test_bundle_search_set_structure(): void
    {
        $transformer = new BundleTransformer();
        $resources = [
            ['resourceType' => 'Organization', 'id' => 'hosp-1'],
            ['resourceType' => 'Organization', 'id' => 'hosp-2'],
        ];

        $bundle = $transformer->buildSearchBundle($resources, 2, 'http://example.com/Organization');

        $this->assertEquals('Bundle', $bundle['resourceType']);
        $this->assertEquals('searchset', $bundle['type']);
        $this->assertEquals(2, $bundle['total']);
        $this->assertCount(2, $bundle['entry']);
    }

    public function test_bundle_entries_have_full_url_and_search_mode(): void
    {
        $transformer = new BundleTransformer();
        $resources = [
            ['resourceType' => 'Organization', 'id' => 'hosp-1'],
        ];

        $bundle = $transformer->buildSearchBundle($resources, 1, 'http://example.com/Organization');

        $entry = $bundle['entry'][0];
        $this->assertArrayHasKey('fullUrl', $entry);
        $this->assertStringContainsString('Organization/hosp-1', $entry['fullUrl']);
        $this->assertEquals('match', $entry['search']['mode']);
    }

    // ── CapabilityStatement ─────────────────────────────────────

    public function test_capability_statement_has_required_fields(): void
    {
        $builder = new CapabilityStatementBuilder();
        $cs = $builder->build();

        $this->assertEquals('CapabilityStatement', $cs['resourceType']);
        $this->assertEquals('active', $cs['status']);
        $this->assertEquals('4.0.1', $cs['fhirVersion']);
        $this->assertArrayHasKey('rest', $cs);
        $this->assertNotEmpty($cs['rest']);
    }

    public function test_capability_statement_declares_three_resources(): void
    {
        $builder = new CapabilityStatementBuilder();
        $cs = $builder->build();

        $resourceTypes = array_column($cs['rest'][0]['resource'], 'type');
        $this->assertContains('Organization', $resourceTypes);
        $this->assertContains('Location', $resourceTypes);
        $this->assertContains('HealthcareService', $resourceTypes);
    }

    public function test_capability_statement_declares_read_and_search(): void
    {
        $builder = new CapabilityStatementBuilder();
        $cs = $builder->build();

        foreach ($cs['rest'][0]['resource'] as $resource) {
            $interactions = array_column($resource['interaction'], 'code');
            $this->assertContains('read', $interactions);
            $this->assertContains('search-type', $interactions);
        }
    }
}
