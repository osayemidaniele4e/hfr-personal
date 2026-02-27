# FHIR R4 Integration Guide — Nigeria Health Facility Registry

**Version:** 1.0  
**Base URL:** `https://hfr.health.gov.ng/api/fhir`  
**FHIR Version:** R4 (4.0.1)  

---

## Table of Contents

1. [Getting Started](#1-getting-started)
2. [Authentication](#2-authentication)
3. [Endpoints](#3-endpoints)
4. [Search Parameters](#4-search-parameters)
5. [Pagination](#5-pagination)
6. [Example Requests & Responses](#6-example-requests--responses)
7. [Error Handling](#7-error-handling)
8. [Rate Limiting](#8-rate-limiting)
9. [Content Negotiation](#9-content-negotiation)
10. [Deployment & Setup](#10-deployment--setup)

---

## 1. Getting Started

The HFR FHIR API provides read-only access to Nigeria's health facility data using the HL7 FHIR R4 standard. It exposes four registries (hospitals, laboratories, pharmacies, imaging centres) through three FHIR resource types.

### Quick Start

```bash
# 1. Check server capabilities (no auth required)
curl https://hfr.health.gov.ng/api/fhir/metadata

# 2. Search for facilities (requires API key)
curl -H "X-API-Key: YOUR_KEY" \
     https://hfr.health.gov.ng/api/fhir/Organization?name=Lagos

# 3. Get a specific facility
curl -H "X-API-Key: YOUR_KEY" \
     https://hfr.health.gov.ng/api/fhir/Organization/hosp-123
```

### Prerequisites

- A valid API key (request from the HFR admin portal)
- HTTP client that supports JSON (`application/fhir+json`)

---

## 2. Authentication

All endpoints except `/metadata` require an API key passed via the `X-API-Key` header.

```
X-API-Key: your-api-key-here
```

Unauthenticated requests return a `401` response with a FHIR OperationOutcome:

```json
{
  "resourceType": "OperationOutcome",
  "issue": [{
    "severity": "error",
    "code": "security",
    "diagnostics": "Invalid or missing API key"
  }]
}
```

---

## 3. Endpoints

### 3.1 Capability Statement

```
GET /api/fhir/metadata
```

Returns a FHIR CapabilityStatement describing server capabilities, supported resources, search parameters, and security requirements. **No authentication required.**

### 3.2 Organization

| Method | Path | Description |
|---|---|---|
| `GET` | `/api/fhir/Organization` | Search across all facility registries |
| `GET` | `/api/fhir/Organization/{id}` | Read a specific facility |

**Resource ID format:** `hosp-{id}`, `lab-{id}`, `pharm-{id}`, `img-{id}`

### 3.3 Location

| Method | Path | Description |
|---|---|---|
| `GET` | `/api/fhir/Location` | Search facility locations |
| `GET` | `/api/fhir/Location/{id}` | Read a specific location |

**Resource ID format:** `loc-hosp-{id}`, `loc-lab-{id}`, `loc-pharm-{id}`, `loc-img-{id}`

### 3.4 HealthcareService

| Method | Path | Description |
|---|---|---|
| `GET` | `/api/fhir/HealthcareService` | Search facility services |
| `GET` | `/api/fhir/HealthcareService/{id}` | Read a specific service |

**Resource ID format:** `svc-{id}`

---

## 4. Search Parameters

### 4.1 Organization Search

| Parameter | Type | Example |
|---|---|---|
| `name` | string | `?name=Lagos` |
| `name:exact` | string | `?name:exact=Lagos General Hospital` |
| `name:contains` | string | `?name:contains=General` |
| `identifier` | token | `?identifier=RC/0001/2024` |
| `type` | token | `?type=hospital` (filter by registry) |
| `address` | string | `?address=Allen Avenue` |
| `address-state` | string | `?address-state=Lagos` |
| `active` | token | `?active=true` |
| `_id` | token | `?_id=hosp-123` |

### 4.2 Location Search

| Parameter | Type | Example |
|---|---|---|
| `name` | string | `?name=Lagos` |
| `identifier` | token | `?identifier=RC/0001/2024` |
| `type` | token | `?type=HOSP` |
| `address` | string | `?address=Ikeja` |
| `address-state` | string | `?address-state=Lagos` |
| `status` | token | `?status=active` |
| `near` | special | `?near=6.5244\|3.3792\|10` (lat\|lng\|km) |
| `organization` | reference | `?organization=Organization/hosp-123` |
| `_id` | token | `?_id=loc-hosp-123` |

### 4.3 HealthcareService Search

| Parameter | Type | Example |
|---|---|---|
| `name` | string | `?name=Cardiology` |
| `organization` | reference | `?organization=Organization/hosp-123` |
| `location` | reference | `?location=Location/loc-hosp-123` |
| `service-category` | token | `?service-category=1` |
| `service-type` | token | `?service-type=5` |
| `_id` | token | `?_id=svc-10` |

### 4.4 Sorting

Use `_sort` to order results. Prefix with `-` for descending:

```
GET /api/fhir/Organization?_sort=name         # A-Z
GET /api/fhir/Organization?_sort=-name        # Z-A
GET /api/fhir/Organization?_sort=name,-status  # Multiple
```

---

## 5. Pagination

Responses are paginated using FHIR Bundle `link` entries.

| Parameter | Default | Maximum | Description |
|---|---|---|---|
| `_count` | 20 | 100 | Number of results per page |
| `_offset` | 0 | — | Starting offset |

### Example

```
GET /api/fhir/Organization?_count=10&_offset=0
```

The response Bundle includes navigation links:

```json
{
  "resourceType": "Bundle",
  "type": "searchset",
  "total": 250,
  "link": [
    { "relation": "self", "url": "...?_count=10&_offset=0" },
    { "relation": "next", "url": "...?_count=10&_offset=10" },
    { "relation": "last", "url": "...?_count=10&_offset=240" }
  ],
  "entry": [...]
}
```

---

## 6. Example Requests & Responses

### 6.1 Search Organizations by State

**Request:**
```bash
curl -H "X-API-Key: YOUR_KEY" \
     "https://hfr.health.gov.ng/api/fhir/Organization?address-state=Lagos&_count=2"
```

**Response:**
```json
{
  "resourceType": "Bundle",
  "type": "searchset",
  "total": 1542,
  "link": [
    { "relation": "self", "url": "https://hfr.health.gov.ng/api/fhir/Organization?address-state=Lagos&_count=2&_offset=0" },
    { "relation": "next", "url": "https://hfr.health.gov.ng/api/fhir/Organization?address-state=Lagos&_count=2&_offset=2" }
  ],
  "entry": [
    {
      "fullUrl": "https://hfr.health.gov.ng/api/fhir/Organization/hosp-101",
      "resource": {
        "resourceType": "Organization",
        "id": "hosp-101",
        "meta": {
          "lastUpdated": "2024-11-20T14:30:00+01:00",
          "profile": ["https://hfr.health.gov.ng/fhir/StructureDefinition/HfrOrganization"]
        },
        "active": true,
        "name": "Lagos Island Maternity Hospital",
        "identifier": [
          {
            "system": "https://hfr.health.gov.ng/fhir/facility-code",
            "value": "RC/1234/2020"
          },
          {
            "system": "https://hfr.health.gov.ng/fhir/registration-no",
            "value": "HFR-1234"
          }
        ],
        "type": [
          {
            "coding": [{
              "system": "https://hfr.health.gov.ng/fhir/CodeSystem/registry-type",
              "code": "hospital",
              "display": "Hospital"
            }]
          },
          {
            "coding": [{
              "system": "https://hfr.health.gov.ng/fhir/CodeSystem/facility-type",
              "code": "5",
              "display": "Maternity Home"
            }]
          }
        ],
        "telecom": [
          { "system": "phone", "value": "+2341234567", "use": "work" },
          { "system": "email", "value": "info@limh.ng", "use": "work" }
        ],
        "address": [{
          "use": "work",
          "type": "physical",
          "text": "Marina Road, Lagos Island",
          "state": "Lagos",
          "district": "Lagos Island",
          "city": "Lagos Island",
          "postalCode": "100001",
          "country": "NG"
        }]
      },
      "search": { "mode": "match" }
    }
  ]
}
```

### 6.2 Read a Specific Location

**Request:**
```bash
curl -H "X-API-Key: YOUR_KEY" \
     "https://hfr.health.gov.ng/api/fhir/Location/loc-hosp-101"
```

**Response:**
```json
{
  "resourceType": "Location",
  "id": "loc-hosp-101",
  "meta": {
    "lastUpdated": "2024-11-20T14:30:00+01:00",
    "profile": ["https://hfr.health.gov.ng/fhir/StructureDefinition/HfrLocation"]
  },
  "status": "active",
  "name": "Lagos Island Maternity Hospital",
  "mode": "instance",
  "type": [
    {
      "coding": [{
        "system": "http://terminology.hl7.org/CodeSystem/v3-RoleCode",
        "code": "HOSP",
        "display": "Hospital"
      }]
    }
  ],
  "position": {
    "latitude": 6.4541,
    "longitude": 3.4082
  },
  "address": {
    "use": "work",
    "type": "physical",
    "text": "Marina Road, Lagos Island",
    "state": "Lagos",
    "district": "Lagos Island",
    "country": "NG"
  },
  "managingOrganization": {
    "reference": "Organization/hosp-101",
    "display": "Lagos Island Maternity Hospital"
  }
}
```

### 6.3 Find Nearby Facilities (Geo Search)

**Request:**
```bash
# Find facilities within 5km of coordinates 6.5244, 3.3792
curl -H "X-API-Key: YOUR_KEY" \
     "https://hfr.health.gov.ng/api/fhir/Location?near=6.5244|3.3792|5"
```

### 6.4 Search Healthcare Services

**Request:**
```bash
curl -H "X-API-Key: YOUR_KEY" \
     "https://hfr.health.gov.ng/api/fhir/HealthcareService?organization=Organization/hosp-101"
```

---

## 7. Error Handling

All errors return FHIR OperationOutcome resources with appropriate HTTP status codes.

| Status | Meaning | Example |
|---|---|---|
| `400` | Bad Request | Invalid search parameter or value |
| `401` | Unauthorized | Missing or invalid API key |
| `404` | Not Found | Resource ID does not exist |
| `406` | Not Acceptable | Unsupported Accept header |
| `429` | Too Many Requests | Rate limit exceeded |
| `500` | Internal Server Error | Unexpected server error |

**Example 404:**
```json
{
  "resourceType": "OperationOutcome",
  "issue": [{
    "severity": "error",
    "code": "not-found",
    "diagnostics": "Organization/hosp-99999 not found"
  }]
}
```

---

## 8. Rate Limiting

API requests are rate-limited per API key. The default limit is shown in your API key registration. When exceeded:

- HTTP status `429` is returned
- `Retry-After` header indicates when you can retry
- Response is a FHIR OperationOutcome

---

## 9. Content Negotiation

### Request Headers

| Header | Value |
|---|---|
| `Accept` | `application/fhir+json` or `application/json` |
| `X-API-Key` | Your API key |

### Response Headers

| Header | Value |
|---|---|
| `Content-Type` | `application/fhir+json; fhirVersion=4.0` |

Requesting `application/xml` or `application/fhir+xml` will return `406 Not Acceptable` — only JSON is supported.

---

## 10. Deployment & Setup

### 10.1 Environment Variables

Add these to your `.env` file:

```env
# FHIR Configuration
FHIR_ENABLED=true
FHIR_VALIDATOR_URL=https://validator.fhir.org/validate
```

### 10.2 Database Migration

```bash
php artisan migrate
```

This creates the `fhir_code_mappings` table used for HFR → FHIR code translations.

### 10.3 Seed Code Mappings

```bash
php artisan db:seed --class=FhirCodeMappingSeeder
```

This reads all existing HFR lookup tables and creates FHIR code system mappings.

### 10.4 Validate Resources (Optional)

```bash
# Validate a sample of Organization resources against FHIR R4
php artisan fhir:validate --resource=Organization --sample=10

# Quick local structural check (no external validator)
php artisan fhir:validate --resource=all --quick

# Validate a specific resource
php artisan fhir:validate --resource=Organization --id=hosp-123
```

### 10.5 Cache Management

Code mappings are cached for 1 hour. To clear manually:

```bash
php artisan cache:forget fhir_code_mappings
```

Or programmatically:

```php
\App\Models\FhirCodeMapping::clearCache();
```

---

## Appendix A: Resource Relationships

```
Organization (hosp-123)
  ├── Location (loc-hosp-123)
  │     └── managingOrganization → Organization/hosp-123
  └── HealthcareService (svc-456)
        ├── providedBy → Organization/hosp-123
        └── location → Location/loc-hosp-123
```

## Appendix B: FHIR Client Libraries

| Language | Library | URL |
|---|---|---|
| Python | fhir.resources | https://pypi.org/project/fhir.resources/ |
| JavaScript | fhir-kit-client | https://github.com/Vermonster/fhir-kit-client |
| Java | HAPI FHIR | https://hapifhir.io/ |
| .NET | Firely SDK | https://fire.ly/fhir-api/ |
| PHP | php-fhir | https://github.com/dcarbone/php-fhir |

## Appendix C: Useful Links

- [FHIR R4 Specification](https://hl7.org/fhir/R4/)
- [FHIR Organization Resource](https://hl7.org/fhir/R4/organization.html)
- [FHIR Location Resource](https://hl7.org/fhir/R4/location.html)
- [FHIR HealthcareService Resource](https://hl7.org/fhir/R4/healthcareservice.html)
- [FHIR Search](https://hl7.org/fhir/R4/search.html)
- [FHIR Bundle](https://hl7.org/fhir/R4/bundle.html)
- [FHIR CapabilityStatement](https://hl7.org/fhir/R4/capabilitystatement.html)
