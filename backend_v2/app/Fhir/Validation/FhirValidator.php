<?php

namespace App\Fhir\Validation;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

/**
 * FHIR External Validator Service
 *
 * Validates FHIR resources against the official HL7 FHIR Validator API.
 * Used in tests, artisan commands, and optional development middleware.
 *
 * @see https://confluence.hl7.org/display/FHIR/Using+the+FHIR+Validator
 */
class FhirValidator
{
    protected Client $httpClient;
    protected string $validatorUrl;

    public function __construct(?string $validatorUrl = null)
    {
        $this->validatorUrl = $validatorUrl ?? config('hfr.fhir.validator_url', 'https://validator.fhir.org');
        $this->httpClient = new Client([
            'base_uri' => $this->validatorUrl,
            'timeout' => 30,
            'headers' => [
                'Content-Type' => 'application/fhir+json',
                'Accept' => 'application/fhir+json',
            ],
        ]);
    }

    /**
     * Validate a FHIR resource against the official validator.
     *
     * Posts the resource to the $validate operation endpoint and
     * parses the returned OperationOutcome.
     *
     * @param array $resource  FHIR resource array with resourceType
     * @return ValidationResult
     */
    public function validate(array $resource): ValidationResult
    {
        $resourceType = $resource['resourceType'] ?? 'Resource';

        try {
            $response = $this->httpClient->post("/validate", [
                'json' => $resource,
                'query' => [
                    'profile' => "http://hl7.org/fhir/StructureDefinition/{$resourceType}",
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            return $this->parseOperationOutcome($body, $resourceType);
        } catch (GuzzleException $e) {
            Log::error("FHIR validation request failed: {$e->getMessage()}");

            return new ValidationResult(
                valid: false,
                errors: ["Validator unavailable: {$e->getMessage()}"],
                warnings: [],
                resourceType: $resourceType
            );
        }
    }

    /**
     * Validate multiple resources and return results for each.
     *
     * @param array $resources  Array of FHIR resource arrays
     * @return array<ValidationResult>
     */
    public function validateBatch(array $resources): array
    {
        return array_map(fn ($r) => $this->validate($r), $resources);
    }

    /**
     * Parse an OperationOutcome response into a ValidationResult.
     */
    protected function parseOperationOutcome(array $outcome, string $resourceType): ValidationResult
    {
        if (($outcome['resourceType'] ?? '') !== 'OperationOutcome') {
            return new ValidationResult(
                valid: false,
                errors: ['Unexpected response from validator (not an OperationOutcome)'],
                warnings: [],
                resourceType: $resourceType
            );
        }

        $errors = [];
        $warnings = [];
        $information = [];

        foreach ($outcome['issue'] ?? [] as $issue) {
            $severity = $issue['severity'] ?? 'error';
            $message = $issue['diagnostics'] ?? $issue['details']['text'] ?? 'Unknown issue';
            $location = implode(', ', $issue['location'] ?? $issue['expression'] ?? []);

            $entry = $location ? "{$message} (at {$location})" : $message;

            match ($severity) {
                'fatal', 'error' => $errors[] = $entry,
                'warning' => $warnings[] = $entry,
                default => $information[] = $entry,
            };
        }

        return new ValidationResult(
            valid: empty($errors),
            errors: $errors,
            warnings: $warnings,
            information: $information,
            resourceType: $resourceType
        );
    }

    /**
     * Quick check if a resource is structurally valid (has required fields).
     * Does NOT call the external validator — this is a fast local check.
     */
    public static function quickCheck(array $resource): array
    {
        $errors = [];

        if (empty($resource['resourceType'])) {
            $errors[] = 'Missing required field: resourceType';
        }

        if (empty($resource['id'])) {
            $errors[] = 'Missing required field: id';
        }

        $type = $resource['resourceType'] ?? '';

        // Resource-specific required field checks
        match ($type) {
            'Organization' => self::checkOrganizationRequired($resource, $errors),
            'Location' => self::checkLocationRequired($resource, $errors),
            'HealthcareService' => self::checkHealthcareServiceRequired($resource, $errors),
            'Bundle' => self::checkBundleRequired($resource, $errors),
            'CapabilityStatement' => self::checkCapabilityStatementRequired($resource, $errors),
            default => null,
        };

        return $errors;
    }

    protected static function checkOrganizationRequired(array $r, array &$errors): void
    {
        // Organization requires no mandatory fields beyond resourceType in R4
        // but we check for useful fields
        if (empty($r['name']) && empty($r['identifier'])) {
            $errors[] = 'Organization should have at least a name or identifier';
        }
    }

    protected static function checkLocationRequired(array $r, array &$errors): void
    {
        // Location has no mandatory fields in R4 but we check for useful ones
        if (empty($r['name']) && empty($r['identifier'])) {
            $errors[] = 'Location should have at least a name or identifier';
        }

        if (isset($r['status']) && !in_array($r['status'], ['active', 'suspended', 'inactive'])) {
            $errors[] = "Location.status must be active|suspended|inactive, got: {$r['status']}";
        }

        if (isset($r['mode']) && !in_array($r['mode'], ['instance', 'kind'])) {
            $errors[] = "Location.mode must be instance|kind, got: {$r['mode']}";
        }
    }

    protected static function checkHealthcareServiceRequired(array $r, array &$errors): void
    {
        // No mandatory fields beyond resourceType
    }

    protected static function checkBundleRequired(array $r, array &$errors): void
    {
        if (empty($r['type'])) {
            $errors[] = 'Bundle.type is required';
        }
    }

    protected static function checkCapabilityStatementRequired(array $r, array &$errors): void
    {
        foreach (['status', 'date', 'kind', 'fhirVersion', 'format'] as $field) {
            if (empty($r[$field])) {
                $errors[] = "CapabilityStatement.{$field} is required";
            }
        }
    }
}
