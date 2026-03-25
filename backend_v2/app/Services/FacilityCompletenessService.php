<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FacilityCompletenessService
{
    /**
     * @return array{
     *     percentage: int,
     *     total_columns: int,
     *     filled_columns: int,
     *     missing_count: int,
     *     missing_fields: list<string>,
     *     missing_field_labels?: \stdClass|array<string, string>
     * }|null
     */
    public function computeForFacilityId(int|string $id): ?array
    {
        $meta = DB::table('hs_hospitals_history')
            ->where('id', $id)
            ->first(['id', 'updated_at']);

        if ($meta === null) {
            return null;
        }

        // Include column-set fingerprint so cache invalidates when schema / exclude list changes
        // (avoids stale % from an old denominator with a new missing_fields list).
        $columnFingerprint = $this->getScannableColumnsFingerprint();
        $cacheKey = 'facility_completeness:' . $id . ':' . (string) ($meta->updated_at ?? '') . ':' . $columnFingerprint;

        return Cache::remember($cacheKey, 3600, function () use ($id) {
            $row = DB::table('hs_hospitals_history')->where('id', $id)->first();
            if ($row === null) {
                return $this->allMissingResult();
            }

            return $this->computeFromRow((object) $row);
        });
    }

    /**
     * @return array{percentage: int, total_columns: int, filled_columns: int, missing_count: int, missing_fields: list<string>, missing_field_labels: array<string, string>}
     */
    private function allMissingResult(): array
    {
        $columns = $this->getScannableColumns();
        $labels = $this->labelsForColumns($columns);
        $total = count($columns);

        return [
            'percentage' => 0,
            'total_columns' => $total,
            'filled_columns' => 0,
            'missing_count' => $total,
            'missing_fields' => $columns,
            'missing_field_labels' => $labels,
        ];
    }

    /**
     * @return list<string>
     */
    private function getScannableColumns(): array
    {
        return Cache::remember('facility_completeness:scannable_columns:v3', 86400, function () {
            $table = config('facility_completeness.table', 'hs_hospitals_history');
            if (!Schema::hasTable($table)) {
                return [];
            }

            $exclude = array_flip(array_map('strtolower', config('facility_completeness.exclude_columns', [])));
            $columns = Schema::getColumnListing($table);
            $out = [];
            foreach ($columns as $col) {
                if (isset($exclude[strtolower($col)])) {
                    continue;
                }
                $out[] = $col;
            }
            sort($out);

            return $out;
        });
    }

    private function getScannableColumnsFingerprint(): string
    {
        $columns = $this->getScannableColumns();

        return hash('sha256', implode("\0", $columns));
    }

    /**
     * @param  list<string>  $columns
     * @return array<string, string>
     */
    private function labelsForColumns(array $columns): array
    {
        $map = config('facility_completeness.column_labels', []);
        if (!is_array($map)) {
            $map = [];
        }
        $labels = [];
        foreach ($columns as $col) {
            $labels[$col] = $map[$col] ?? $this->humanizeColumn($col);
        }

        return $labels;
    }

    private function humanizeColumn(string $column): string
    {
        $s = str_replace('_', ' ', $column);

        return ucwords($s);
    }

    /**
     * @return array{percentage: int, total_columns: int, filled_columns: int, missing_count: int, missing_fields: list<string>, missing_field_labels: \stdClass|array<string, string>}
     */
    public function computeFromRow(object $row): array
    {
        $columns = $this->getScannableColumns();
        if ($columns === []) {
            return [
                'percentage' => 100,
                'total_columns' => 0,
                'filled_columns' => 0,
                'missing_count' => 0,
                'missing_fields' => [],
                'missing_field_labels' => new \stdClass(),
            ];
        }

        $missing = [];
        foreach ($columns as $column) {
            $value = $row->{$column} ?? null;
            if (!$this->isColumnFilled($column, $value)) {
                $missing[] = $column;
            }
        }

        $total = count($columns);
        $missingCount = count($missing);
        $filled = $total - $missingCount;
        $percentage = $total > 0 ? (int) round(($filled / $total) * 100) : 100;

        $labelMap = config('facility_completeness.column_labels', []);
        if (!is_array($labelMap)) {
            $labelMap = [];
        }
        $missingLabels = [];
        foreach ($missing as $col) {
            $missingLabels[$col] = $labelMap[$col] ?? $this->humanizeColumn($col);
        }

        return [
            'percentage' => $percentage,
            'total_columns' => $total,
            'filled_columns' => $filled,
            'missing_count' => $missingCount,
            'missing_fields' => $missing,
            'missing_field_labels' => $missingLabels === [] ? new \stdClass() : $missingLabels,
        ];
    }

    private function isColumnFilled(string $column, mixed $value): bool
    {
        if ($column === 'image_url') {
            return $this->isImageUrlFilled($value);
        }

        if ($value === null) {
            return false;
        }

        $fkStyle = str_ends_with($column, '_id');

        if (is_string($value)) {
            $t = trim($value);
            if ($t === '') {
                return false;
            }
            if ($fkStyle && ($t === '0' || strcasecmp($t, 'null') === 0)) {
                return false;
            }

            return true;
        }

        if (is_int($value) || is_float($value)) {
            if ($fkStyle) {
                return $value != 0;
            }

            return true;
        }

        if (is_bool($value)) {
            return true;
        }

        return true;
    }

    private function isImageUrlFilled(mixed $value): bool
    {
        if ($value === null) {
            return false;
        }
        if (!is_string($value)) {
            return true;
        }
        $t = trim($value);
        if ($t === '' || $t === '[]' || $t === '{}') {
            return false;
        }

        return true;
    }
}
