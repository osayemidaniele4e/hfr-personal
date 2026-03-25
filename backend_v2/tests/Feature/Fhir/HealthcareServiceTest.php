<?php

namespace Tests\Feature\Fhir;

use Tests\TestCase;

/**
 * Feature tests for FHIR HealthcareService endpoints.
 */
class HealthcareServiceTest extends TestCase
{
    public function test_healthcare_service_search_requires_api_key(): void
    {
        $response = $this->getJson('/api/fhir/HealthcareService');

        $response->assertStatus(401);
        $response->assertJson([
            'resourceType' => 'OperationOutcome',
        ]);
    }

    public function test_healthcare_service_read_requires_api_key(): void
    {
        $response = $this->getJson('/api/fhir/HealthcareService/svc-1');

        $response->assertStatus(401);
        $response->assertJson([
            'resourceType' => 'OperationOutcome',
        ]);
    }
}
