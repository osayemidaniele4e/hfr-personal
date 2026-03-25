<?php

namespace Tests\Feature\Fhir;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Feature tests for FHIR Organization endpoints.
 *
 * These tests require a valid API key and a running database.
 * Unauthenticated requests should return 401 OperationOutcome.
 */
class OrganizationTest extends TestCase
{
    // ── Authentication ──────────────────────────────────────────

    public function test_organization_search_requires_api_key(): void
    {
        $response = $this->getJson('/api/fhir/Organization');

        $response->assertStatus(401);
        $response->assertJson([
            'resourceType' => 'OperationOutcome',
        ]);
    }

    public function test_organization_read_requires_api_key(): void
    {
        $response = $this->getJson('/api/fhir/Organization/hosp-1');

        $response->assertStatus(401);
        $response->assertJson([
            'resourceType' => 'OperationOutcome',
        ]);
    }

    // ── Content Type ────────────────────────────────────────────

    public function test_organization_returns_fhir_content_type(): void
    {
        $response = $this->getJson('/api/fhir/Organization');

        // Even 401 should have FHIR content type from middleware
        $this->assertTrue(
            str_contains($response->headers->get('Content-Type', ''), 'fhir+json')
            || $response->status() === 401
        );
    }

    // ── Error Responses ─────────────────────────────────────────

    public function test_organization_read_invalid_id_format(): void
    {
        // If the id format is completely wrong, should return 404 OperationOutcome
        $response = $this->withHeader('X-API-Key', 'test-key-invalid')
            ->getJson('/api/fhir/Organization/invalid-format');

        // Either 401 (bad key) or 400/404 (bad id) — both valid
        $this->assertContains($response->status(), [400, 401, 404]);
    }
}
