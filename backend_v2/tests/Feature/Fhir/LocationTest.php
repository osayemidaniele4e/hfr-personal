<?php

namespace Tests\Feature\Fhir;

use Tests\TestCase;

/**
 * Feature tests for FHIR Location endpoints.
 */
class LocationTest extends TestCase
{
    public function test_location_search_requires_api_key(): void
    {
        $response = $this->getJson('/api/fhir/Location');

        $response->assertStatus(401);
        $response->assertJson([
            'resourceType' => 'OperationOutcome',
        ]);
    }

    public function test_location_read_requires_api_key(): void
    {
        $response = $this->getJson('/api/fhir/Location/loc-hosp-1');

        $response->assertStatus(401);
        $response->assertJson([
            'resourceType' => 'OperationOutcome',
        ]);
    }
}
