<?php

namespace App\Fhir\Validation;

/**
 * FHIR Validation Result DTO.
 *
 * Holds the parsed result of a FHIR validator response.
 */
class ValidationResult
{
    public function __construct(
        public readonly bool $valid,
        public readonly array $errors = [],
        public readonly array $warnings = [],
        public readonly array $information = [],
        public readonly string $resourceType = 'Resource'
    ) {
    }

    /**
     * Get a summary string for display.
     */
    public function summary(): string
    {
        $status = $this->valid ? 'VALID' : 'INVALID';
        $parts = ["{$this->resourceType}: {$status}"];

        if (!empty($this->errors)) {
            $parts[] = count($this->errors) . ' error(s)';
        }
        if (!empty($this->warnings)) {
            $parts[] = count($this->warnings) . ' warning(s)';
        }

        return implode(' — ', $parts);
    }

    /**
     * Convert to array for JSON serialization.
     */
    public function toArray(): array
    {
        return [
            'valid' => $this->valid,
            'resourceType' => $this->resourceType,
            'errors' => $this->errors,
            'warnings' => $this->warnings,
            'information' => $this->information,
        ];
    }
}
