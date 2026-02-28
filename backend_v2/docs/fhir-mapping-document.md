# HFR → FHIR R4 Resource Mapping Document

**Version:** 1.0  
**FHIR Version:** R4 (4.0.1)  
**Date:** 2025-02-25  
**Publisher:** Federal Ministry of Health, Nigeria  
**Status:** Draft  

---

## 1. Overview

This document defines the field-level mapping between Nigeria's Health Facility Registry (HFR) data model and FHIR R4 resources. The HFR FHIR API exposes four registries (hospitals, laboratories, pharmacies, imaging centres) through three FHIR resource types:

| FHIR Resource | Purpose | HFR Source Tables |
|---|---|---|
| **Organization** | Facility identity, ownership, contacts, staffing | `hs_hospitals_history`, `lb_laboratories`, `pharmacies`, `im_imagings` |
| **Location** | Physical location, coordinates, hours, status | Same tables (geographic/operational data) |
| **HealthcareService** | Services offered by a facility | `hs_hospital_services`, `lst_hosp_services`, `lst_hosp_service_category` |

---

## 2. Identifier Systems

| System URI | Description | Example |
|---|---|---|
| `https://hfr.health.gov.ng/fhir/facility-code` | Facility code assigned by HFR | `RC/0001/2024` |
| `https://hfr.health.gov.ng/fhir/registration-no` | Registration number | `HFR-0001` |
| `https://hfr.health.gov.ng/fhir/hospital-id` | Hospital registry internal ID | `1234` |
| `https://hfr.health.gov.ng/fhir/laboratory-id` | Laboratory registry internal ID | `567` |
| `https://hfr.health.gov.ng/fhir/pharmacy-id` | Pharmacy registry internal ID | `890` |
| `https://hfr.health.gov.ng/fhir/imaging-id` | Imaging centre registry internal ID | `234` |

---

## 3. Resource ID Conventions

Resources are prefixed to indicate their source registry:

| Registry | Organization ID | Location ID |
|---|---|---|
| Hospital | `hosp-{id}` | `loc-hosp-{id}` |
| Laboratory | `lab-{id}` | `loc-lab-{id}` |
| Pharmacy | `pharm-{id}` | `loc-pharm-{id}` |
| Imaging | `img-{id}` | `loc-img-{id}` |
| HealthcareService | `svc-{id}` | — |

---

## 4. Organization Resource Mapping

### 4.1 Core Elements

| FHIR Element | HFR Field | Notes |
|---|---|---|
| `id` | `{prefix}-{id}` | See ID conventions above |
| `meta.lastUpdated` | `updated_at` | ISO 8601 format |
| `meta.profile` | — | `https://hfr.health.gov.ng/fhir/StructureDefinition/HfrOrganization` |
| `active` | `operation_status` | "Operational" → `true`, all others → `false` |
| `name` | `facility_name` | Direct mapping |
| `identifier[0].system` | — | `https://hfr.health.gov.ng/fhir/facility-code` |
| `identifier[0].value` | `facility_code` | Direct mapping |
| `identifier[1].system` | — | `https://hfr.health.gov.ng/fhir/registration-no` |
| `identifier[1].value` | `registration_no` | Direct mapping |

### 4.2 Type (CodeableConcept)

Organization.type carries multiple codings to classify the facility:

| Coding | System | Code Source | HFR Field |
|---|---|---|---|
| Registry discriminator | `.../CodeSystem/registry-type` | `hospital`, `laboratory`, `pharmacy`, `imaging` | Source table |
| Facility type | `.../CodeSystem/facility-type` | `facility_type_id` | `lst_facility_types.name` |
| Level of care | `.../CodeSystem/level-of-care` | `facility_level_id` | `lst_level_of_care.name` |
| Ownership | `.../CodeSystem/ownership` | `ownership_id` | `lst_ownerships.name` |

### 4.3 Telecom

| FHIR Element | HFR Field | system | use |
|---|---|---|---|
| `telecom[0]` | `phone_number` | `phone` | `work` |
| `telecom[1]` | `alternate_number` | `phone` | `work` |
| `telecom[2]` | `email` | `email` | `work` |
| `telecom[3]` | `website` | `url` | `work` |

Null values are omitted from the array.

### 4.4 Address

| FHIR Element | HFR Field |
|---|---|
| `address[0].use` | — (always `work`) |
| `address[0].type` | — (always `physical`) |
| `address[0].text` | `physical_location` |
| `address[0].state` | `state_name` |
| `address[0].district` | `lga_name` |
| `address[0].city` | `ward_name` |
| `address[0].postalCode` | `postal_address` |
| `address[0].country` | — (always `NG`) |

### 4.5 Extensions

#### 4.5.1 Staffing Extension
**URL:** `https://hfr.health.gov.ng/fhir/StructureDefinition/staffing`

| Sub-extension | HFR Field | valueInteger |
|---|---|---|
| `doctors` | `num_doctors` | ✓ |
| `pharmacists` | `num_pharmacists` | ✓ |
| `pharmacy-technicians` | `num_pharmacy_technicians` | ✓ |
| `dentists` | `num_dentists` | ✓ |
| `dental-technicians` | `num_dental_technicians` | ✓ |
| `nurses` | `num_nurses` | ✓ |
| `midwives` | `num_midwifes` | ✓ |
| `nurse-midwives` | `num_nurse_midwife` | ✓ |
| `lab-technicians` | `num_lab_technicians` | ✓ |
| `lab-scientists` | `num_lab_scientists` | ✓ |
| `him-officers` | `num_him_officers` | ✓ |
| `community-health-officers` | `num_community_health_officer` | ✓ |
| `community-extension-workers` | `num_community_extension_workers` | ✓ |
| `junior-community-extension-workers` | `num_jun_community_extension_worker` | ✓ |
| `env-health-officers` | `num_env_health_officers` | ✓ |

#### 4.5.2 License Information Extension
**URL:** `https://hfr.health.gov.ng/fhir/StructureDefinition/license-info`

| Sub-extension | HFR Field | valueString |
|---|---|---|
| `registration-status` | `registration_status` | ✓ |
| `license-status` | `license_status` | ✓ |

#### 4.5.3 Operational Details Extension
**URL:** `https://hfr.health.gov.ng/fhir/StructureDefinition/operational-details`

| Sub-extension | HFR Field | Type |
|---|---|---|
| `beds` | `num_beds` | valueInteger |
| `ambulance-services` | `ambulance_services` | valueBoolean |
| `onsite-pharmacy` | `onsite_pharmacy` | valueBoolean |
| `onsite-laboratory` | `onsite_laboratory` | valueBoolean |
| `onsite-imaging` | `onsite_imaging` | valueBoolean |

---

## 5. Location Resource Mapping

### 5.1 Core Elements

| FHIR Element | HFR Field | Notes |
|---|---|---|
| `id` | `loc-{prefix}-{id}` | See ID conventions |
| `meta.lastUpdated` | `updated_at` | ISO 8601 |
| `status` | `operation_status` | "Operational" → `active`, "Temporarily Closed" → `suspended`, others → `inactive` |
| `name` | `facility_name` | Direct mapping |
| `mode` | — | Always `instance` |
| `managingOrganization.reference` | — | `Organization/{prefix}-{id}` |

### 5.2 Operational Status

| FHIR Element | System | HFR Value → FHIR Code |
|---|---|---|
| `operationalStatus` | `http://terminology.hl7.org/CodeSystem/v2-0116` | Operational → `O`, Closed → `C`, Temporarily Closed → `K` |

### 5.3 Type

| Coding | System | Source |
|---|---|---|
| HL7 RoleCode | `http://terminology.hl7.org/CodeSystem/v3-RoleCode` | Registry type → `HOSP`/`MBL`/`PHARM`/`RADDX` |
| Facility type | `.../CodeSystem/facility-type` | `facility_type_id` |
| Level of care | `.../CodeSystem/level-of-care` | `facility_level_id` |

### 5.4 Position

| FHIR Element | HFR Field | Type |
|---|---|---|
| `position.latitude` | `latitude` | decimal |
| `position.longitude` | `longitude` | decimal |

### 5.5 Address

Same mapping as Organization.address (see §4.4).

### 5.6 Hours of Operation

| FHIR Element | HFR Field | Notes |
|---|---|---|
| `hoursOfOperation[].daysOfWeek` | `days_of_operation` | Parsed from comma-separated list |
| `hoursOfOperation[].openingTime` | `hours_of_operation` | Parsed from `HH:MM-HH:MM` format |
| `hoursOfOperation[].closingTime` | `hours_of_operation` | Parsed from `HH:MM-HH:MM` format |

### 5.7 Laboratory-Specific Extensions

| Extension URL | HFR Field |
|---|---|
| `.../StructureDefinition/accreditation-status` | `accreditation_status` |

### 5.8 Pharmacy-Specific Extensions

| Extension URL | HFR Field |
|---|---|
| `.../StructureDefinition/premises-type` | `premises_type` |
| `.../StructureDefinition/outlet-category` | `outlet_category` |

---

## 6. HealthcareService Resource Mapping

| FHIR Element | HFR Field | Notes |
|---|---|---|
| `id` | `svc-{id}` | `hs_hospital_services.id` |
| `meta.lastUpdated` | `updated_at` | ISO 8601 |
| `active` | — | Always `true` |
| `providedBy.reference` | `hospital_id` | `Organization/hosp-{hospital_id}` |
| `location[0].reference` | `hospital_id` | `Location/loc-hosp-{hospital_id}` |
| `name` | `service_name` | From `lst_hosp_services.name` |
| `category[0].coding[0].code` | `service_category_id` | From `lst_hosp_service_category` |
| `category[0].coding[0].display` | `service_category_name` | From `lst_hosp_service_category` |
| `type[0].coding[0].code` | `service_id` | From `lst_hosp_services` |
| `type[0].coding[0].display` | `service_name` | From `lst_hosp_services` |

---

## 7. Code System Mappings

### 7.1 HFR Code Systems

| Code System URI | Source Table | Description |
|---|---|---|
| `.../CodeSystem/registry-type` | — | Discriminator: hospital, laboratory, pharmacy, imaging |
| `.../CodeSystem/facility-type` | `lst_facility_types` | Hospital, Clinic, Health Centre, etc. |
| `.../CodeSystem/level-of-care` | `lst_level_of_care` | Primary, Secondary, Tertiary |
| `.../CodeSystem/ownership` | `lst_ownerships` | Federal, State, Private, Mission, etc. |
| `.../CodeSystem/ownership-type` | `lst_ownership_types` | Public, Private |
| `.../CodeSystem/operational-status` | `lst_oparational_status` | Operational, Non-Operational, etc. |
| `.../CodeSystem/registration-status` | `lst_registration_status` | Registered, Unregistered |
| `.../CodeSystem/license-status` | `lst_license_status` | Licensed, Unlicensed |
| `.../CodeSystem/accreditation-status` | `lst_accreditation_status` | Accredited, Not Accredited |
| `.../CodeSystem/service-category` | `lst_hosp_service_category` | Clinical Services, etc. |

### 7.2 HL7 Standard Code System Mappings

| HL7 Code System | Usage | HFR Source |
|---|---|---|
| `http://terminology.hl7.org/CodeSystem/organization-type` | Organization.type | Ownership mapped to HL7 codes (govt, bus, reli, etc.) |
| `http://terminology.hl7.org/CodeSystem/v3-RoleCode` | Location.type | Registry type mapped (HOSP, MBL, PHARM, RADDX) |
| `http://terminology.hl7.org/CodeSystem/v2-0116` | Location.operationalStatus | Operational status mapped (O, C, K) |

---

## 8. Search Parameters

### 8.1 Organization

| Parameter | Type | FHIR Path | DB Column |
|---|---|---|---|
| `name` | string | `Organization.name` | `facility_name` |
| `identifier` | token | `Organization.identifier` | `facility_code`, `registration_no` |
| `type` | token | `Organization.type` | `facility_type_id`, registry prefix |
| `address` | string | `Organization.address` | `physical_location` |
| `address-state` | string | `Organization.address.state` | `state_name` |
| `active` | token | `Organization.active` | `operational_status_id` |
| `_id` | token | `Organization.id` | Parsed from prefixed ID |

### 8.2 Location

| Parameter | Type | FHIR Path | DB Column |
|---|---|---|---|
| `name` | string | `Location.name` | `facility_name` |
| `identifier` | token | `Location.identifier` | `facility_code` |
| `type` | token | `Location.type` | `facility_type_id` |
| `address` | string | `Location.address` | `physical_location` |
| `address-state` | string | `Location.address.state` | `state_name` |
| `status` | token | `Location.status` | `operational_status_id` |
| `near` | special | `Location.position` | `latitude`, `longitude` (Haversine) |
| `organization` | reference | `Location.managingOrganization` | Parsed from Organization ID |
| `_id` | token | `Location.id` | Parsed from prefixed ID |

### 8.3 HealthcareService

| Parameter | Type | FHIR Path | DB Column |
|---|---|---|---|
| `name` | string | `HealthcareService.name` | `service_name` |
| `organization` | reference | `HealthcareService.providedBy` | `hospital_id` |
| `location` | reference | `HealthcareService.location` | `hospital_id` |
| `service-category` | token | `HealthcareService.category` | `service_category_id` |
| `service-type` | token | `HealthcareService.type` | `service_id` |
| `_id` | token | `HealthcareService.id` | Parsed from `svc-` prefix |

---

## 9. Governance

### 9.1 Update Frequency
- Code mappings are maintained in the `fhir_code_mappings` database table
- Updates require running the `FhirCodeMappingSeeder` after lookup table changes
- Cache is cleared automatically when mappings are updated (`FhirCodeMapping::clearCache()`)

### 9.2 Extension Governance
- All custom extensions are published under `https://hfr.health.gov.ng/fhir/StructureDefinition/`
- Extension definitions should be registered with the Nigeria FHIR Implementation Guide
- New extensions require approval from the HFR governance committee

### 9.3 Breaking Change Policy
- The CapabilityStatement at `/api/fhir/metadata` documents all supported resources and search parameters
- Removals or type changes to existing elements constitute breaking changes
- New optional elements or extensions can be added without version bump
- Major changes require incrementing the CapabilityStatement version

---

## 10. Conformance Notes

1. **Read-only API**: All endpoints support `GET` only (read + search-type interactions)
2. **Pagination**: Uses `_count` and `_offset` parameters; default page size is 20
3. **Search modifiers**: `:exact` and `:contains` supported for string parameters
4. **Token search**: Supports `system|code` composite format
5. **Near search**: Uses Haversine formula with `latitude|longitude|distance(km)` format
6. **Content negotiation**: Returns `application/fhir+json; fhirVersion=4.0`
7. **Error responses**: All errors return FHIR OperationOutcome resources
