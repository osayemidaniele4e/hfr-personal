<?php

namespace App\Fhir\Search;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * FHIR Search Parameter Parser
 *
 * Parses FHIR R4 search parameters from an HTTP request and converts them
 * to Laravel DB query builder conditions. Supports string modifiers (:exact,
 * :contains), token format (system|code), special near parameter, and
 * standard pagination (_count, _offset) and sorting (_sort).
 *
 * @see https://hl7.org/fhir/R4/search.html
 */
class FhirSearchParser
{
    protected Request $request;
    protected int $defaultPageSize;
    protected int $maxPageSize;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->defaultPageSize = config('hfr.fhir.page_size', 20);
        $this->maxPageSize = config('hfr.fhir.max_page_size', 100);
    }

    // ──────────────────────────────────────────────────────────
    // Pagination
    // ──────────────────────────────────────────────────────────

    /**
     * Get the FHIR _count (page size).
     */
    public function getCount(): int
    {
        $count = (int) $this->request->input('_count', $this->defaultPageSize);
        return max(1, min($count, $this->maxPageSize));
    }

    /**
     * Get the FHIR _offset and convert to page number.
     */
    public function getPage(): int
    {
        $offset = (int) $this->request->input('_offset', 0);
        $count = $this->getCount();

        return max(1, (int) floor($offset / $count) + 1);
    }

    /**
     * Get the raw search parameters (excluding FHIR control params).
     */
    public function getSearchParams(): array
    {
        $ignore = ['_count', '_offset', '_sort', '_format', '_pretty', '_summary', '_elements'];
        $params = [];

        foreach ($this->request->query() as $key => $value) {
            if (!in_array($key, $ignore) && $value !== null && $value !== '') {
                $params[$key] = $value;
            }
        }

        return $params;
    }

    // ──────────────────────────────────────────────────────────
    // Sorting
    // ──────────────────────────────────────────────────────────

    /**
     * Parse _sort parameter into [column, direction] pairs.
     *
     * FHIR sort: `_sort=name` (asc), `_sort=-name` (desc), comma-separated for multiple.
     *
     * @param array $allowedFields  Map of FHIR sort names to DB columns
     * @return array Array of [column, direction] pairs
     */
    public function parseSortParams(array $allowedFields): array
    {
        $sortParam = $this->request->input('_sort');
        if (empty($sortParam)) {
            return [];
        }

        $sorts = [];
        foreach (explode(',', $sortParam) as $field) {
            $field = trim($field);
            if (empty($field)) continue;

            $direction = 'asc';
            if (str_starts_with($field, '-')) {
                $direction = 'desc';
                $field = substr($field, 1);
            }

            if (isset($allowedFields[$field])) {
                $sorts[] = [$allowedFields[$field], $direction];
            }
        }

        return $sorts;
    }

    // ──────────────────────────────────────────────────────────
    // String Search Parameters
    // ──────────────────────────────────────────────────────────

    /**
     * Apply a FHIR string search parameter to the query.
     *
     * Handles modifiers: :exact, :contains (default is starts-with).
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $paramName   FHIR parameter name (e.g. 'name')
     * @param string $dbColumn    Database column to search
     * @param string|null $modifier  Search modifier (:exact, :contains, or null for default)
     */
    public function applyStringSearch($query, string $paramName, string $dbColumn, ?string $modifier = null): void
    {
        // Check for paramName with modifiers in the request
        $value = $this->request->input($paramName);
        $exactValue = $this->request->input("{$paramName}:exact");
        $containsValue = $this->request->input("{$paramName}:contains");

        if ($exactValue !== null) {
            $query->where($dbColumn, '=', $exactValue);
            return;
        }

        if ($containsValue !== null) {
            $query->whereRaw("LOWER({$dbColumn}) LIKE ?", ['%' . strtolower($containsValue) . '%']);
            return;
        }

        if ($value !== null) {
            // Default string search is "starts with" per FHIR spec, but we use contains for usability
            $query->whereRaw("LOWER({$dbColumn}) LIKE ?", ['%' . strtolower($value) . '%']);
        }
    }

    // ──────────────────────────────────────────────────────────
    // Token Search Parameters
    // ──────────────────────────────────────────────────────────

    /**
     * Parse a FHIR token parameter value.
     *
     * Token format: system|code, |code (no system), or code (value only).
     *
     * @param string $value  The raw token parameter value
     * @return array ['system' => string|null, 'code' => string]
     */
    public function parseToken(string $value): array
    {
        if (str_contains($value, '|')) {
            $parts = explode('|', $value, 2);
            return [
                'system' => $parts[0] !== '' ? $parts[0] : null,
                'code' => $parts[1],
            ];
        }

        return [
            'system' => null,
            'code' => $value,
        ];
    }

    /**
     * Apply a FHIR token search for an identifier parameter.
     *
     * Searches across one or more identifier columns.
     */
    public function applyIdentifierSearch($query, string $paramName, array $identifierColumns): void
    {
        $value = $this->request->input($paramName);
        if ($value === null) return;

        $token = $this->parseToken($value);

        $query->where(function ($q) use ($identifierColumns, $token) {
            foreach ($identifierColumns as $column) {
                $q->orWhere($column, '=', $token['code']);
            }
        });
    }

    /**
     * Apply a FHIR token search for a coded type parameter.
     *
     * Handles both code-only and system|code formats.
     */
    public function applyTokenSearch($query, string $paramName, string $dbColumn, ?string $nameColumn = null): void
    {
        $value = $this->request->input($paramName);
        if ($value === null) return;

        $token = $this->parseToken($value);

        // If the code is numeric, search by ID column
        if (is_numeric($token['code'])) {
            $query->where($dbColumn, '=', (int) $token['code']);
        } elseif ($nameColumn) {
            // Otherwise search by display name
            $query->whereRaw("LOWER({$nameColumn}) LIKE ?", ['%' . strtolower($token['code']) . '%']);
        }
    }

    /**
     * Apply a FHIR boolean token search (active = true|false).
     */
    public function applyBooleanSearch($query, string $paramName, string $dbColumn, int $trueValue = 1): void
    {
        $value = $this->request->input($paramName);
        if ($value === null) return;

        $isTrue = in_array(strtolower($value), ['true', '1', 'yes']);

        if ($isTrue) {
            $query->where($dbColumn, '=', $trueValue);
        } else {
            $query->where($dbColumn, '<>', $trueValue);
        }
    }

    // ──────────────────────────────────────────────────────────
    // Special Search Parameters
    // ──────────────────────────────────────────────────────────

    /**
     * Apply FHIR Location "near" search parameter.
     *
     * Format: latitude|longitude|distance|units
     * Example: near=6.5244|3.3792|10|km
     *
     * Uses the Haversine formula for distance calculation.
     */
    public function applyNearSearch($query, string $latColumn, string $lngColumn): void
    {
        $value = $this->request->input('near');
        if ($value === null) return;

        $parts = explode('|', $value);
        if (count($parts) < 2) return;

        $lat = (float) $parts[0];
        $lng = (float) $parts[1];
        $distance = isset($parts[2]) ? (float) $parts[2] : 10; // default 10
        $units = $parts[3] ?? 'km';

        // Earth radius based on units
        $earthRadius = strtolower($units) === 'mi' ? 3959 : 6371; // miles or km

        // Haversine distance formula in SQL
        $haversine = "(
            {$earthRadius} * acos(
                cos(radians(?)) * cos(radians({$latColumn})) *
                cos(radians({$lngColumn}) - radians(?)) +
                sin(radians(?)) * sin(radians({$latColumn}))
            )
        )";

        $query->whereNotNull($latColumn)
            ->where($latColumn, '<>', '')
            ->whereNotNull($lngColumn)
            ->where($lngColumn, '<>', '')
            ->whereRaw("{$haversine} <= ?", [$lat, $lng, $lat, $distance])
            ->orderByRaw($haversine, [$lat, $lng, $lat]);
    }

    /**
     * Apply address (free text) search across multiple address columns.
     */
    public function applyAddressSearch($query, string $paramName, array $addressColumns): void
    {
        $value = $this->request->input($paramName);
        if ($value === null) return;

        $search = strtolower(trim($value));

        $query->where(function ($q) use ($addressColumns, $search) {
            foreach ($addressColumns as $column) {
                $q->orWhereRaw("LOWER({$column}) LIKE ?", ["%{$search}%"]);
            }
        });
    }

    /**
     * Apply FHIR _id search parameter (direct resource ID lookup).
     */
    public function applyIdSearch($query, string $dbIdColumn): void
    {
        $value = $this->request->input('_id');
        if ($value === null) return;

        // Strip the prefix (hosp-, lab-, pharm-, img-, loc-hosp-, etc.)
        $numericId = preg_replace('/^(loc-)?(hosp|lab|pharm|img)-/', '', $value);

        if (is_numeric($numericId)) {
            $query->where($dbIdColumn, '=', (int) $numericId);
        }
    }

    /**
     * Apply FHIR reference search (e.g. organization=Organization/hosp-123).
     */
    public function applyReferenceSearch($query, string $paramName, string $dbColumn): void
    {
        $value = $this->request->input($paramName);
        if ($value === null) return;

        // Handle both "Organization/hosp-123" and "hosp-123" formats
        if (str_contains($value, '/')) {
            $value = explode('/', $value, 2)[1];
        }

        // Strip prefix
        $numericId = preg_replace('/^(loc-)?(hosp|lab|pharm|img)-/', '', $value);

        if (is_numeric($numericId)) {
            $query->where($dbColumn, '=', (int) $numericId);
        }
    }

    /**
     * Apply FHIR address-state search parameter.
     */
    public function applyAddressStateSearch($query, string $stateNameColumn, ?string $stateIdColumn = null): void
    {
        $value = $this->request->input('address-state');
        if ($value === null) return;

        // If the value is numeric, search by state ID
        if (is_numeric($value) && $stateIdColumn) {
            $query->where($stateIdColumn, '=', (int) $value);
        } else {
            $query->whereRaw("LOWER({$stateNameColumn}) LIKE ?", ['%' . strtolower($value) . '%']);
        }
    }
}
