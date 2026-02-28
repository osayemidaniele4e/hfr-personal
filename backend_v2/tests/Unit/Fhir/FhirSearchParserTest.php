<?php

namespace Tests\Unit\Fhir;

use Tests\TestCase;
use App\Fhir\Search\FhirSearchParser;
use Illuminate\Http\Request;

class FhirSearchParserTest extends TestCase
{
    private function makeParser(array $query = []): FhirSearchParser
    {
        $request = Request::create('/api/fhir/Organization', 'GET', $query);
        return new FhirSearchParser($request);
    }

    // ── Pagination ──────────────────────────────────────────────

    public function test_default_count_returns_config_value(): void
    {
        $parser = $this->makeParser();
        $this->assertEquals(config('hfr.fhir.page_size', 20), $parser->getCount());
    }

    public function test_count_respects_max_page_size(): void
    {
        $parser = $this->makeParser(['_count' => '9999']);
        $this->assertLessThanOrEqual(config('hfr.fhir.max_page_size', 100), $parser->getCount());
    }

    public function test_offset_converts_to_page(): void
    {
        $parser = $this->makeParser(['_count' => '10', '_offset' => '30']);
        $this->assertEquals(4, $parser->getPage()); // offset 30 / count 10 + 1 = 4
    }

    public function test_zero_offset_returns_page_one(): void
    {
        $parser = $this->makeParser(['_offset' => '0']);
        $this->assertEquals(1, $parser->getPage());
    }

    // ── String Search ───────────────────────────────────────────

    public function test_string_search_default_contains(): void
    {
        $parser = $this->makeParser(['name' => 'General']);
        $result = $parser->getStringSearch('name');
        $this->assertNotNull($result);
        $this->assertEquals('contains', $result['modifier']);
        $this->assertEquals('General', $result['value']);
    }

    public function test_string_search_exact_modifier(): void
    {
        $parser = $this->makeParser(['name:exact' => 'Lagos General Hospital']);
        $result = $parser->getStringSearch('name');
        $this->assertNotNull($result);
        $this->assertEquals('exact', $result['modifier']);
        $this->assertEquals('Lagos General Hospital', $result['value']);
    }

    public function test_string_search_contains_modifier(): void
    {
        $parser = $this->makeParser(['name:contains' => 'General']);
        $result = $parser->getStringSearch('name');
        $this->assertNotNull($result);
        $this->assertEquals('contains', $result['modifier']);
    }

    public function test_string_search_returns_null_when_missing(): void
    {
        $parser = $this->makeParser([]);
        $this->assertNull($parser->getStringSearch('name'));
    }

    // ── Token Search ────────────────────────────────────────────

    public function test_token_search_code_only(): void
    {
        $parser = $this->makeParser(['type' => 'HOSP']);
        $result = $parser->getTokenSearch('type');
        $this->assertNotNull($result);
        $this->assertNull($result['system']);
        $this->assertEquals('HOSP', $result['code']);
    }

    public function test_token_search_system_and_code(): void
    {
        $parser = $this->makeParser(['type' => 'http://terminology.hl7.org/CodeSystem/v3-RoleCode|HOSP']);
        $result = $parser->getTokenSearch('type');
        $this->assertNotNull($result);
        $this->assertEquals('http://terminology.hl7.org/CodeSystem/v3-RoleCode', $result['system']);
        $this->assertEquals('HOSP', $result['code']);
    }

    public function test_token_search_returns_null_when_missing(): void
    {
        $parser = $this->makeParser([]);
        $this->assertNull($parser->getTokenSearch('type'));
    }

    // ── Sort ────────────────────────────────────────────────────

    public function test_sort_ascending(): void
    {
        $parser = $this->makeParser(['_sort' => 'name']);
        $sorts = $parser->parseSortParams(['name' => 'facility_name']);
        $this->assertCount(1, $sorts);
        $this->assertEquals('facility_name', $sorts[0]['column']);
        $this->assertEquals('asc', $sorts[0]['direction']);
    }

    public function test_sort_descending(): void
    {
        $parser = $this->makeParser(['_sort' => '-name']);
        $sorts = $parser->parseSortParams(['name' => 'facility_name']);
        $this->assertCount(1, $sorts);
        $this->assertEquals('facility_name', $sorts[0]['column']);
        $this->assertEquals('desc', $sorts[0]['direction']);
    }

    public function test_sort_ignores_unknown_params(): void
    {
        $parser = $this->makeParser(['_sort' => 'unknown']);
        $sorts = $parser->parseSortParams(['name' => 'facility_name']);
        $this->assertEmpty($sorts);
    }

    public function test_multiple_sort_params(): void
    {
        $parser = $this->makeParser(['_sort' => 'name,-status']);
        $sorts = $parser->parseSortParams([
            'name' => 'facility_name',
            'status' => 'operational_status',
        ]);
        $this->assertCount(2, $sorts);
        $this->assertEquals('asc', $sorts[0]['direction']);
        $this->assertEquals('desc', $sorts[1]['direction']);
    }
}
