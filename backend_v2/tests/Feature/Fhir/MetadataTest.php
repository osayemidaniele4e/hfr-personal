<?php

namespace Tests\Feature\Fhir;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Feature tests for the FHIR /metadata endpoint.
 * This endpoint is public (no API key required).
 */
class MetadataTest extends TestCase
{
    public function test_metadata_returns_capability_statement(): void
    {
        $response = $this->getJson('/api/fhir/metadata');

        $response->assertStatus(200);
        $response->assertJson([
            'resourceType' => 'CapabilityStatement',
            'status' => 'active',
            'fhirVersion' => '4.0.1',
            'kind' => 'instance',
        ]);
    }

    public function test_metadata_has_fhir_content_type(): void
    {
        $response = $this->getJson('/api/fhir/metadata');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/fhir+json; fhirVersion=4.0');
    }

    public function test_metadata_declares_organization_resource(): void
    {
        $response = $this->getJson('/api/fhir/metadata');

        $data = $response->json();
        $resourceTypes = array_column($data['rest'][0]['resource'] ?? [], 'type');
        $this->assertContains('Organization', $resourceTypes);
    }

    public function test_metadata_declares_location_resource(): void
    {
        $response = $this->getJson('/api/fhir/metadata');

        $data = $response->json();
        $resourceTypes = array_column($data['rest'][0]['resource'] ?? [], 'type');
        $this->assertContains('Location', $resourceTypes);
    }

    public function test_metadata_declares_healthcare_service_resource(): void
    {
        $response = $this->getJson('/api/fhir/metadata');

        $data = $response->json();
        $resourceTypes = array_column($data['rest'][0]['resource'] ?? [], 'type');
        $this->assertContains('HealthcareService', $resourceTypes);
    }

    public function test_metadata_security_declares_api_key(): void
    {
        $response = $this->getJson('/api/fhir/metadata');

        $data = $response->json();
        $security = $data['rest'][0]['security'] ?? [];
        $this->assertArrayHasKey('description', $security);
        $this->assertStringContainsString('API key', $security['description']);
    }
}
