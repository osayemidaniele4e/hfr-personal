<?php

namespace App\Fhir\Transformers;

use Illuminate\Support\Str;

/**
 * FHIR R4 Bundle Transformer
 *
 * Wraps FHIR resources into a Bundle of type "searchset" for
 * search endpoint responses, including pagination links and total count.
 *
 * @see https://hl7.org/fhir/R4/bundle.html
 */
class BundleTransformer
{
    /**
     * Build a FHIR searchset Bundle from an array of FHIR resources.
     *
     * @param array  $resources   Array of FHIR resource arrays
     * @param int    $total       Total matching resources (for pagination)
     * @param string $selfUrl     The current request URL
     * @param string|null $nextUrl    URL for the next page (null if last page)
     * @param string|null $prevUrl    URL for the previous page (null if first page)
     * @param string|null $firstUrl   URL for the first page
     * @param string|null $lastUrl    URL for the last page
     * @return array FHIR Bundle resource
     */
    public function buildSearchBundle(
        array $resources,
        int $total,
        string $selfUrl,
        ?string $nextUrl = null,
        ?string $prevUrl = null,
        ?string $firstUrl = null,
        ?string $lastUrl = null
    ): array {
        $bundle = [
            'resourceType' => 'Bundle',
            'id' => (string) Str::uuid(),
            'meta' => [
                'lastUpdated' => now()->toIso8601String(),
            ],
            'type' => 'searchset',
            'total' => $total,
            'link' => $this->buildLinks($selfUrl, $nextUrl, $prevUrl, $firstUrl, $lastUrl),
            'entry' => $this->buildEntries($resources, $selfUrl),
        ];

        return $bundle;
    }

    /**
     * Build a FHIR Bundle containing a single resource (for read operations
     * that return related resources, e.g. Organization + Location + Services).
     */
    public function buildCollectionBundle(array $resources): array
    {
        $baseUrl = url(config('hfr.fhir.base_url', '/api/fhir'));

        return [
            'resourceType' => 'Bundle',
            'id' => (string) Str::uuid(),
            'meta' => [
                'lastUpdated' => now()->toIso8601String(),
            ],
            'type' => 'collection',
            'total' => count($resources),
            'entry' => array_map(fn ($r) => [
                'fullUrl' => "{$baseUrl}/{$r['resourceType']}/{$r['id']}",
                'resource' => $r,
            ], $resources),
        ];
    }

    /**
     * Build Bundle.link array.
     */
    protected function buildLinks(
        string $selfUrl,
        ?string $nextUrl,
        ?string $prevUrl,
        ?string $firstUrl,
        ?string $lastUrl
    ): array {
        $links = [
            ['relation' => 'self', 'url' => $selfUrl],
        ];

        if ($firstUrl) {
            $links[] = ['relation' => 'first', 'url' => $firstUrl];
        }

        if ($prevUrl) {
            $links[] = ['relation' => 'previous', 'url' => $prevUrl];
        }

        if ($nextUrl) {
            $links[] = ['relation' => 'next', 'url' => $nextUrl];
        }

        if ($lastUrl) {
            $links[] = ['relation' => 'last', 'url' => $lastUrl];
        }

        return $links;
    }

    /**
     * Build Bundle.entry array from FHIR resources.
     */
    protected function buildEntries(array $resources, string $baseSearchUrl): array
    {
        $baseUrl = url(config('hfr.fhir.base_url', '/api/fhir'));

        return array_map(fn ($resource) => [
            'fullUrl' => "{$baseUrl}/{$resource['resourceType']}/{$resource['id']}",
            'resource' => $resource,
            'search' => [
                'mode' => 'match',
            ],
        ], $resources);
    }

    /**
     * Build pagination URLs from a Laravel paginator.
     *
     * @param \Illuminate\Contracts\Pagination\LengthAwarePaginator $paginator
     * @param string $baseUrl
     * @param array $queryParams  Current search parameters (to preserve in pagination links)
     * @return array [selfUrl, nextUrl, prevUrl, firstUrl, lastUrl]
     */
    public function buildPaginationUrls($paginator, string $baseUrl, array $queryParams = []): array
    {
        // FHIR uses _count and _offset instead of page/per_page
        $perPage = $paginator->perPage();
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();

        $buildUrl = function (int $page) use ($baseUrl, $queryParams, $perPage) {
            $params = array_merge($queryParams, [
                '_count' => $perPage,
                '_offset' => ($page - 1) * $perPage,
            ]);
            return $baseUrl . '?' . http_build_query($params);
        };

        return [
            'self' => $buildUrl($currentPage),
            'next' => $currentPage < $lastPage ? $buildUrl($currentPage + 1) : null,
            'prev' => $currentPage > 1 ? $buildUrl($currentPage - 1) : null,
            'first' => $buildUrl(1),
            'last' => $buildUrl($lastPage),
        ];
    }
}
