"""
Generate .docx files for HFR API Guide and System Architecture documents.
Run: python generate_docs.py
"""
from docx import Document
from docx.shared import Inches, Pt, Cm, RGBColor, Emu
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.enum.section import WD_ORIENT
from docx.oxml.ns import qn, nsdecls
from docx.oxml import parse_xml
import os

# ─── Color constants ───
GREEN = RGBColor(0x1A, 0x6D, 0x37)
DARK = RGBColor(0x1A, 0x1A, 0x2E)
BLUE = RGBColor(0x0F, 0x4C, 0x75)
ACCENT = RGBColor(0x32, 0x82, 0xB8)
MUTED = RGBColor(0x6B, 0x72, 0x80)
WHITE = RGBColor(0xFF, 0xFF, 0xFF)
BLACK = RGBColor(0x00, 0x00, 0x00)
RED = RGBColor(0xDC, 0x26, 0x26)


def set_cell_shading(cell, color_hex):
    """Set background color of a table cell."""
    shading = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{color_hex}"/>')
    cell._tc.get_or_add_tcPr().append(shading)


def add_styled_table(doc, headers, rows, header_color="0F4C75"):
    """Add a formatted table with colored headers."""
    table = doc.add_table(rows=1 + len(rows), cols=len(headers))
    table.style = 'Table Grid'
    table.alignment = WD_TABLE_ALIGNMENT.CENTER

    # Header row
    for i, h in enumerate(headers):
        cell = table.rows[0].cells[i]
        cell.text = h
        for p in cell.paragraphs:
            for run in p.runs:
                run.font.bold = True
                run.font.color.rgb = WHITE
                run.font.size = Pt(9)
        set_cell_shading(cell, header_color)

    # Data rows
    for r_idx, row_data in enumerate(rows):
        for c_idx, val in enumerate(row_data):
            cell = table.rows[r_idx + 1].cells[c_idx]
            cell.text = str(val)
            for p in cell.paragraphs:
                for run in p.runs:
                    run.font.size = Pt(9)
            if r_idx % 2 == 1:
                set_cell_shading(cell, "F8F9FA")

    return table


def add_heading_styled(doc, text, level=1, color=BLUE):
    """Add a heading with custom color."""
    h = doc.add_heading(text, level=level)
    for run in h.runs:
        run.font.color.rgb = color
    return h


def add_code_block(doc, code_text):
    """Add a monospaced code block paragraph."""
    p = doc.add_paragraph()
    p.style = doc.styles['No Spacing']
    p_format = p.paragraph_format
    p_format.space_before = Pt(6)
    p_format.space_after = Pt(6)
    p_format.left_indent = Cm(0.5)
    run = p.add_run(code_text)
    run.font.name = 'Courier New'
    run.font.size = Pt(8.5)
    run.font.color.rgb = RGBColor(0x1E, 0x29, 0x3B)
    # Set background via xml
    shading = parse_xml(f'<w:shd {nsdecls("w")} w:fill="F1F5F9"/>')
    p._p.get_or_add_pPr().append(shading)
    return p


def add_info_box(doc, text, border_color="3282B8"):
    """Add a callout/info box paragraph."""
    p = doc.add_paragraph()
    p_format = p.paragraph_format
    p_format.space_before = Pt(8)
    p_format.space_after = Pt(8)
    p_format.left_indent = Cm(0.8)
    run = p.add_run(text)
    run.font.size = Pt(9)
    run.font.italic = True
    run.font.color.rgb = RGBColor(0x33, 0x33, 0x33)
    shading = parse_xml(f'<w:shd {nsdecls("w")} w:fill="E8F4FD"/>')
    p._p.get_or_add_pPr().append(shading)
    return p


def add_endpoint_heading(doc, method, path, public=False):
    """Add an endpoint heading like  GET /api/v1/facilities"""
    p = doc.add_paragraph()
    p_format = p.paragraph_format
    p_format.space_before = Pt(12)
    p_format.space_after = Pt(4)

    method_run = p.add_run(f" {method} ")
    method_run.font.bold = True
    method_run.font.size = Pt(10)
    method_run.font.name = 'Courier New'
    if method == "GET":
        method_run.font.color.rgb = RGBColor(0x1D, 0x4E, 0xD8)
    else:
        method_run.font.color.rgb = RGBColor(0x15, 0x80, 0x3D)

    path_run = p.add_run(f"  {path}")
    path_run.font.bold = True
    path_run.font.size = Pt(11)
    path_run.font.name = 'Courier New'

    if public:
        pub_run = p.add_run("  [PUBLIC]")
        pub_run.font.bold = True
        pub_run.font.size = Pt(8)
        pub_run.font.color.rgb = RGBColor(0x15, 0x80, 0x3D)

    return p


# ═══════════════════════════════════════════════════════════════════
#  DOCUMENT 1: API GUIDE
# ═══════════════════════════════════════════════════════════════════

def build_api_guide():
    doc = Document()

    # --- Page setup ---
    section = doc.sections[0]
    section.page_width = Cm(21)
    section.page_height = Cm(29.7)
    section.top_margin = Cm(2)
    section.bottom_margin = Cm(2)
    section.left_margin = Cm(2.5)
    section.right_margin = Cm(2.5)

    # ── COVER PAGE ──
    for _ in range(6):
        doc.add_paragraph()

    title = doc.add_paragraph()
    title.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = title.add_run("🏥")
    run.font.size = Pt(48)

    title2 = doc.add_paragraph()
    title2.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = title2.add_run("Health Facility Registry")
    run.font.size = Pt(32)
    run.font.bold = True
    run.font.color.rgb = BLUE

    sub = doc.add_paragraph()
    sub.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = sub.add_run("External API v1 — Developer Guide")
    run.font.size = Pt(18)
    run.font.color.rgb = ACCENT

    doc.add_paragraph()

    meta = doc.add_paragraph()
    meta.alignment = WD_ALIGN_PARAGRAPH.CENTER
    for line in [
        "Federal Ministry of Health, Nigeria",
        "Document Version: 1.0",
        "Last Updated: February 2026",
        "Base URL: https://hfrapi.fmohconnect.gov.ng/api/v1"
    ]:
        run = meta.add_run(line + "\n")
        run.font.size = Pt(10)
        run.font.color.rgb = MUTED

    doc.add_page_break()

    # ── TABLE OF CONTENTS ──
    add_heading_styled(doc, "Table of Contents", level=1)
    toc_items = [
        "1. Overview",
        "2. Getting Started",
        "3. Authentication",
        "4. Rate Limiting",
        "5. Response Format",
        "6. Pagination",
        "7. Error Handling",
        "8. Facilities Endpoints",
        "9. Pharmacies Endpoints",
        "10. Laboratories Endpoints",
        "11. Imaging / Radiology Endpoints",
        "12. Lookup / Reference Data Endpoints",
        "13. Public Endpoints",
        "14. Code Examples",
        "15. Versioning",
        "16. Support & Contact",
    ]
    for item in toc_items:
        p = doc.add_paragraph(item)
        p.paragraph_format.space_after = Pt(2)
        for run in p.runs:
            run.font.size = Pt(11)
            run.font.color.rgb = BLUE

    doc.add_page_break()

    # ── 1. OVERVIEW ──
    add_heading_styled(doc, "1. Overview", level=1)
    doc.add_paragraph(
        "The Health Facility Registry (HFR) External API provides programmatic access to Nigeria's "
        "comprehensive registry of health facilities, pharmacies, laboratories, and imaging centres. "
        "It is maintained by the Federal Ministry of Health."
    )
    doc.add_paragraph(
        "The API is designed around REST principles. All responses are returned in JSON. "
        "It uses API key authentication and supports pagination, filtering, sorting, and full-text search."
    )
    add_info_box(doc, "Who is this for? — Government agencies, health-tech developers, researchers, NGOs, "
                      "and anyone building applications that rely on accurate health facility data in Nigeria.")

    add_heading_styled(doc, "Key Features", level=2, color=ACCENT)
    add_styled_table(doc,
        ["Category", "Description"],
        [
            ["Facilities", "Search, filter, and retrieve details on hospitals, clinics, and health centres"],
            ["Pharmacies", "Access pharmaceutical premises data with licensing status"],
            ["Laboratories", "Query laboratory data including accreditation status"],
            ["Imaging", "Retrieve radiology / diagnostic imaging centres"],
            ["Lookups", "States, LGAs, wards, facility types, ownership, statuses, and more"],
        ]
    )

    # ── 2. GETTING STARTED ──
    doc.add_page_break()
    add_heading_styled(doc, "2. Getting Started", level=1)

    add_heading_styled(doc, "Step 1 — Request an API Key", level=2, color=ACCENT)
    doc.add_paragraph("Send a POST request to the public key-request endpoint, or fill out the form on the developer portal.")
    add_code_block(doc,
        'POST /api/v1/request-key\n'
        'Content-Type: application/json\n\n'
        '{\n'
        '  "name": "Jane Doe",\n'
        '  "email": "jane@example.com",\n'
        '  "organisation": "My Health App",\n'
        '  "use_case": "Building a facility locator to help Nigerians find nearby hospitals."\n'
        '}'
    )

    add_heading_styled(doc, "Step 2 — Wait for Approval", level=2, color=ACCENT)
    doc.add_paragraph("Your request will be reviewed by an administrator. Once approved, your API key will be sent to your email. Check status at any time:")
    add_code_block(doc, "GET /api/v1/request-key/status?email=jane@example.com")

    add_heading_styled(doc, "Step 3 — Start Making Requests", level=2, color=ACCENT)
    doc.add_paragraph("Include your API key in the X-API-Key header of every request.")
    add_code_block(doc,
        'curl -H "X-API-Key: hfr_your_api_key_here" \\\n'
        '     https://hfrapi.fmohconnect.gov.ng/api/v1/facilities'
    )

    # ── 3. AUTHENTICATION ──
    doc.add_page_break()
    add_heading_styled(doc, "3. Authentication", level=1)
    doc.add_paragraph("Every request to a protected endpoint must include your API key in the X-API-Key HTTP header.")

    add_styled_table(doc,
        ["Header", "Value"],
        [
            ["X-API-Key", "Your API key (starts with hfr_)"],
            ["Accept", "application/json (recommended)"],
        ]
    )

    add_info_box(doc, "⚠ Keep your key secret. Do not embed API keys in client-side code or public repositories. "
                      "If you believe your key has been compromised, contact the HFR team to have it revoked and regenerated.")

    add_heading_styled(doc, "Authentication Errors", level=2, color=ACCENT)
    add_styled_table(doc,
        ["Status", "Code", "Description"],
        [
            ["401", "api_key_required", "Missing X-API-Key header"],
            ["401", "invalid_api_key", "Key not recognised"],
            ["403", "api_key_inactive", "Key has been deactivated"],
            ["403", "api_key_expired", "Key has passed its expiry date"],
        ]
    )

    # ── 4. RATE LIMITING ──
    add_heading_styled(doc, "4. Rate Limiting", level=1)
    doc.add_paragraph("Each API key has an individual rate limit (default: 60 requests per minute). "
                      "Exceeding this limit returns a 429 Too Many Requests response.")
    add_styled_table(doc,
        ["Header", "Description"],
        [
            ["X-RateLimit-Limit", "Maximum requests allowed per minute"],
            ["X-RateLimit-Remaining", "Requests remaining in the current window"],
            ["Retry-After", "Seconds until the window resets (only on 429)"],
        ]
    )
    add_info_box(doc, "💡 Tip: If you need a higher rate limit for production workloads, mention this in your API key request or contact support.")

    # ── 5. RESPONSE FORMAT ──
    doc.add_page_break()
    add_heading_styled(doc, "5. Response Format", level=1)
    doc.add_paragraph("All responses use a consistent JSON envelope:")
    add_code_block(doc,
        '{\n'
        '  "status": "success",\n'
        '  "data": { ... },\n'
        '  "meta": {\n'
        '    "current_page": 1,\n'
        '    "per_page": 25,\n'
        '    "total": 43210,\n'
        '    "last_page": 1729\n'
        '  },\n'
        '  "links": {\n'
        '    "first": "...?page=1",\n'
        '    "last": "...?page=1729",\n'
        '    "prev": null,\n'
        '    "next": "...?page=2"\n'
        '  }\n'
        '}'
    )
    add_styled_table(doc,
        ["Field", "Type", "Description"],
        [
            ["status", "string", "success or error"],
            ["data", "object/array", "Requested resource(s)"],
            ["meta", "object", "Pagination metadata (list endpoints only)"],
            ["links", "object", "Pagination navigation URLs"],
            ["message", "string", "Human-readable message (error responses)"],
        ]
    )

    # ── 6. PAGINATION ──
    add_heading_styled(doc, "6. Pagination", level=1)
    doc.add_paragraph("List endpoints are paginated. Control pagination with these query parameters:")
    add_styled_table(doc,
        ["Parameter", "Default", "Range", "Description"],
        [
            ["page", "1", "1 – ∞", "Page number"],
            ["per_page", "25", "1 – 100", "Results per page"],
        ]
    )

    # ── 7. ERROR HANDLING ──
    add_heading_styled(doc, "7. Error Handling", level=1)
    doc.add_paragraph('Error responses follow the same envelope with "status": "error":')
    add_code_block(doc,
        '{\n'
        '  "status": "error",\n'
        '  "message": "The given data was invalid.",\n'
        '  "errors": {\n'
        '    "email": ["The email field is required."]\n'
        '  }\n'
        '}'
    )
    add_heading_styled(doc, "HTTP Status Codes", level=2, color=ACCENT)
    add_styled_table(doc,
        ["Code", "Meaning"],
        [
            ["200", "Success"],
            ["201", "Resource created (API key request submitted)"],
            ["401", "Unauthorised — missing or invalid API key"],
            ["403", "Forbidden — key inactive or expired"],
            ["404", "Resource not found"],
            ["409", "Conflict — duplicate API key request"],
            ["422", "Validation error"],
            ["429", "Rate limit exceeded"],
            ["500", "Internal server error"],
        ]
    )

    # ── 8. FACILITIES ──
    doc.add_page_break()
    add_heading_styled(doc, "8. Facilities Endpoints", level=1)

    add_endpoint_heading(doc, "GET", "/api/v1/facilities")
    doc.add_paragraph("Search and list health facilities with filtering, sorting, and pagination.")
    add_styled_table(doc,
        ["Parameter", "Type", "Required", "Description"],
        [
            ["search", "string", "No", "Search by name, state, LGA, or ward (max 255)"],
            ["state_id", "integer", "No", "Filter by state"],
            ["lga_id", "integer", "No", "Filter by LGA"],
            ["ward_id", "integer", "No", "Filter by ward"],
            ["facility_type_id", "integer", "No", "Filter by facility type"],
            ["facility_level_id", "integer", "No", "Filter by level of care"],
            ["ownership_id", "integer", "No", "Filter by ownership category"],
            ["ownership_type_id", "integer", "No", "Filter by ownership type"],
            ["operational_status_id", "integer", "No", "Filter by operational status"],
            ["registration_status_id", "integer", "No", "Filter by registration status"],
            ["license_status_id", "integer", "No", "Filter by license status"],
            ["has_coordinates", "boolean", "No", "Only facilities with GPS coordinates"],
            ["service_ids", "string", "No", "Comma-separated service IDs"],
            ["sort_by", "string", "No", "facility_name | state | lga | updated_at"],
            ["sort_order", "string", "No", "asc (default) | desc"],
            ["per_page", "integer", "No", "1–100 (default 25)"],
            ["page", "integer", "No", "Page number"],
        ]
    )

    add_endpoint_heading(doc, "GET", "/api/v1/facilities/{id}")
    doc.add_paragraph("Retrieve detailed information about a single facility by its ID.")
    add_styled_table(doc,
        ["Parameter", "Type", "Required", "Description"],
        [["id", "integer", "Yes", "Facility ID (path parameter)"]]
    )

    add_endpoint_heading(doc, "GET", "/api/v1/facilities/{id}/services")
    doc.add_paragraph("Get all services offered by a facility, grouped by category.")
    add_styled_table(doc,
        ["Parameter", "Type", "Required", "Description"],
        [["id", "integer", "Yes", "Facility ID (path parameter)"]]
    )

    add_endpoint_heading(doc, "GET", "/api/v1/facilities/statistics")
    doc.add_paragraph("Get aggregate statistics: total facilities, breakdown by ownership, level of care, and state.")
    add_styled_table(doc,
        ["Parameter", "Type", "Required", "Description"],
        [
            ["state_id", "integer", "No", "Scope statistics to a state"],
            ["lga_id", "integer", "No", "Scope statistics to an LGA"],
        ]
    )

    # ── 9. PHARMACIES ──
    doc.add_page_break()
    add_heading_styled(doc, "9. Pharmacies Endpoints", level=1)

    add_endpoint_heading(doc, "GET", "/api/v1/pharmacies")
    doc.add_paragraph("Search and list pharmaceutical premises.")
    add_styled_table(doc,
        ["Parameter", "Type", "Required", "Description"],
        [
            ["search", "string", "No", "Search by name (max 255)"],
            ["state_id", "integer", "No", "Filter by state"],
            ["lga_id", "integer", "No", "Filter by LGA"],
            ["ward_id", "integer", "No", "Filter by ward"],
            ["ownership_id", "integer", "No", "Filter by ownership"],
            ["operational_status_id", "integer", "No", "Filter by operational status"],
            ["registration_status_id", "integer", "No", "Filter by registration status"],
            ["license_status_id", "integer", "No", "Filter by license status"],
            ["has_coordinates", "boolean", "No", "Only with GPS coordinates"],
            ["per_page", "integer", "No", "1–100 (default 25)"],
            ["page", "integer", "No", "Page number"],
        ]
    )

    add_endpoint_heading(doc, "GET", "/api/v1/pharmacies/{id}")
    doc.add_paragraph("Get detailed information about a single pharmacy.")
    add_styled_table(doc,
        ["Parameter", "Type", "Required", "Description"],
        [["id", "integer", "Yes", "Pharmacy ID (path parameter)"]]
    )

    # ── 10. LABORATORIES ──
    add_heading_styled(doc, "10. Laboratories Endpoints", level=1)

    add_endpoint_heading(doc, "GET", "/api/v1/laboratories")
    doc.add_paragraph("Search and list laboratory premises.")
    add_styled_table(doc,
        ["Parameter", "Type", "Required", "Description"],
        [
            ["search", "string", "No", "Search by name (max 255)"],
            ["state_id", "integer", "No", "Filter by state"],
            ["lga_id", "integer", "No", "Filter by LGA"],
            ["ward_id", "integer", "No", "Filter by ward"],
            ["facility_level_id", "integer", "No", "Filter by level of care"],
            ["ownership_id", "integer", "No", "Filter by ownership"],
            ["operational_status_id", "integer", "No", "Filter by operational status"],
            ["registration_status_id", "integer", "No", "Filter by registration status"],
            ["license_status_id", "integer", "No", "Filter by license status"],
            ["accreditation_status_id", "integer", "No", "Filter by accreditation status"],
            ["has_coordinates", "boolean", "No", "Only with GPS coordinates"],
            ["per_page", "integer", "No", "1–100 (default 25)"],
            ["page", "integer", "No", "Page number"],
        ]
    )

    add_endpoint_heading(doc, "GET", "/api/v1/laboratories/{id}")
    doc.add_paragraph("Get detailed information about a single laboratory.")
    add_styled_table(doc,
        ["Parameter", "Type", "Required", "Description"],
        [["id", "integer", "Yes", "Laboratory ID (path parameter)"]]
    )

    # ── 11. IMAGING ──
    doc.add_page_break()
    add_heading_styled(doc, "11. Imaging / Radiology Endpoints", level=1)

    add_endpoint_heading(doc, "GET", "/api/v1/imaging")
    doc.add_paragraph("Search and list imaging / radiology centres.")
    add_styled_table(doc,
        ["Parameter", "Type", "Required", "Description"],
        [
            ["search", "string", "No", "Search by name (max 255)"],
            ["state_id", "integer", "No", "Filter by state"],
            ["lga_id", "integer", "No", "Filter by LGA"],
            ["ward_id", "integer", "No", "Filter by ward"],
            ["ownership_id", "integer", "No", "Filter by ownership"],
            ["operational_status_id", "integer", "No", "Filter by operational status"],
            ["registration_status_id", "integer", "No", "Filter by registration status"],
            ["license_status_id", "integer", "No", "Filter by license status"],
            ["has_coordinates", "boolean", "No", "Only with GPS coordinates"],
            ["per_page", "integer", "No", "1–100 (default 25)"],
            ["page", "integer", "No", "Page number"],
        ]
    )

    add_endpoint_heading(doc, "GET", "/api/v1/imaging/{id}")
    doc.add_paragraph("Get detailed information about a single imaging facility.")
    add_styled_table(doc,
        ["Parameter", "Type", "Required", "Description"],
        [["id", "integer", "Yes", "Imaging facility ID (path parameter)"]]
    )

    # ── 12. LOOKUPS ──
    add_heading_styled(doc, "12. Lookup / Reference Data Endpoints", level=1)
    doc.add_paragraph("These endpoints return reference data used as filter values in other endpoints. "
                      "Use the returned IDs when filtering facilities, pharmacies, laboratories, and imaging centres.")

    lookup_endpoints = [
        ("GET", "/api/v1/lookups/states", "All 36 Nigerian states and FCT.", []),
        ("GET", "/api/v1/lookups/lgas", "Local Government Areas. Filter by state_id.",
         [["state_id", "integer", "No", "Filter by state"]]),
        ("GET", "/api/v1/lookups/wards", "Wards. Filter by lga_id.",
         [["lga_id", "integer", "No", "Filter by LGA"]]),
        ("GET", "/api/v1/lookups/facility-types", "All facility type classifications.", []),
        ("GET", "/api/v1/lookups/facility-levels", "Levels of care (Primary, Secondary, Tertiary).", []),
        ("GET", "/api/v1/lookups/ownership", "Ownership categories (Public, Private).", []),
        ("GET", "/api/v1/lookups/ownership-types", "Ownership types. Filter by ownership_id.",
         [["ownership_id", "integer", "No", "Filter by ownership category"]]),
        ("GET", "/api/v1/lookups/operational-statuses", "Operational status options.", []),
        ("GET", "/api/v1/lookups/registration-statuses", "Registration status options.", []),
        ("GET", "/api/v1/lookups/license-statuses", "License status options.", []),
        ("GET", "/api/v1/lookups/accreditation-statuses", "Accreditation status options.", []),
        ("GET", "/api/v1/lookups/service-categories", "Health service categories.", []),
        ("GET", "/api/v1/lookups/services", "Health services. Filter by category_id.",
         [["category_id", "integer", "No", "Filter by service category"]]),
    ]

    for method, path, desc, params in lookup_endpoints:
        add_endpoint_heading(doc, method, path)
        doc.add_paragraph(desc)
        if params:
            add_styled_table(doc,
                ["Parameter", "Type", "Required", "Description"],
                params
            )

    # ── 13. PUBLIC ENDPOINTS ──
    doc.add_page_break()
    add_heading_styled(doc, "13. Public Endpoints", level=1)
    doc.add_paragraph("These endpoints do not require an API key.")

    add_endpoint_heading(doc, "GET", "/api/v1/", public=True)
    doc.add_paragraph("Health check. Returns API version, status, and available endpoint URLs.")

    add_endpoint_heading(doc, "POST", "/api/v1/request-key", public=True)
    doc.add_paragraph("Submit a request for an API key. Rate limited to 5 requests per minute.")
    add_styled_table(doc,
        ["Field", "Type", "Required", "Description"],
        [
            ["name", "string", "Yes", "Full name (max 255 chars)"],
            ["email", "string", "Yes", "Valid email address"],
            ["organisation", "string", "No", "Organisation name (max 255 chars)"],
            ["use_case", "string", "Yes", "Intended usage description (min 20 chars)"],
        ]
    )

    add_endpoint_heading(doc, "GET", "/api/v1/request-key/status", public=True)
    doc.add_paragraph("Check the status of a previously submitted API key request.")
    add_styled_table(doc,
        ["Parameter", "Type", "Required", "Description"],
        [["email", "string", "Yes", "Email address used in the request"]]
    )

    # ── 14. CODE EXAMPLES ──
    doc.add_page_break()
    add_heading_styled(doc, "14. Code Examples", level=1)

    add_heading_styled(doc, "cURL", level=2, color=ACCENT)
    add_code_block(doc,
        '# List facilities in Lagos state (state_id = 25)\n'
        'curl -s -H "X-API-Key: hfr_your_api_key" \\\n'
        '  "https://hfrapi.fmohconnect.gov.ng/api/v1/facilities?state_id=25&per_page=10"\n\n'
        '# Get a single facility\n'
        'curl -s -H "X-API-Key: hfr_your_api_key" \\\n'
        '  "https://hfrapi.fmohconnect.gov.ng/api/v1/facilities/12345"\n\n'
        '# Get facility statistics\n'
        'curl -s -H "X-API-Key: hfr_your_api_key" \\\n'
        '  "https://hfrapi.fmohconnect.gov.ng/api/v1/facilities/statistics"'
    )

    add_heading_styled(doc, "Python (requests)", level=2, color=ACCENT)
    add_code_block(doc,
        'import requests\n\n'
        'API_KEY = "hfr_your_api_key"\n'
        'BASE    = "https://hfrapi.fmohconnect.gov.ng/api/v1"\n\n'
        'headers = {"X-API-Key": API_KEY}\n\n'
        '# Search for hospitals in Abuja\n'
        'resp = requests.get(f"{BASE}/facilities", headers=headers, params={\n'
        '    "search": "Abuja",\n'
        '    "per_page": 10\n'
        '})\n'
        'data = resp.json()\n\n'
        'for facility in data["data"]:\n'
        '    print(facility["facility_name"], "-", facility["state"])'
    )

    add_heading_styled(doc, "JavaScript (fetch)", level=2, color=ACCENT)
    add_code_block(doc,
        'const API_KEY = "hfr_your_api_key";\n'
        'const BASE    = "https://hfrapi.fmohconnect.gov.ng/api/v1";\n\n'
        'async function getFacilities(stateId) {\n'
        '  const url = `${BASE}/facilities?state_id=${stateId}&per_page=20`;\n'
        '  const res = await fetch(url, {\n'
        '    headers: { "X-API-Key": API_KEY }\n'
        '  });\n'
        '  if (!res.ok) throw new Error(`HTTP ${res.status}`);\n'
        '  return res.json();\n'
        '}\n\n'
        'getFacilities(25).then(json => {\n'
        '  console.log(`Found ${json.meta.total} facilities`);\n'
        '  json.data.forEach(f => console.log(f.facility_name));\n'
        '});'
    )

    add_heading_styled(doc, "PHP (Guzzle)", level=2, color=ACCENT)
    add_code_block(doc,
        'use GuzzleHttp\\Client;\n\n'
        '$client = new Client([\n'
        "    'base_uri' => 'https://hfrapi.fmohconnect.gov.ng/api/v1/',\n"
        "    'headers'  => ['X-API-Key' => 'hfr_your_api_key'],\n"
        ']);\n\n'
        '// List pharmacies in a state\n'
        "$response = $client->get('pharmacies', [\n"
        "    'query' => ['state_id' => 25, 'per_page' => 50]\n"
        ']);\n\n'
        '$data = json_decode($response->getBody(), true);\n'
        "echo 'Total: ' . $data['meta']['total'];"
    )

    # ── 15. VERSIONING ──
    doc.add_page_break()
    add_heading_styled(doc, "15. Versioning", level=1)
    doc.add_paragraph("The API is versioned via the URL path: /api/v1/. The current (and only) version is v1.")
    doc.add_paragraph("Every response includes an X-API-Version header indicating the version that handled the request.")
    add_info_box(doc, "Backwards compatibility: When a new version is released, the previous version will continue "
                      "to be available for at least 12 months with a deprecation notice. We will notify all registered "
                      "API key holders before sunsetting any version.")

    # ── 16. SUPPORT ──
    add_heading_styled(doc, "16. Support & Contact", level=1)
    doc.add_paragraph("For questions, bug reports, or higher rate-limit requests, please contact the HFR team:")
    add_styled_table(doc,
        ["Channel", "Details"],
        [
            ["Email", "hfr@fmohconnect.gov.ng"],
            ["Developer Portal", "https://hfr.health.gov.ng/developers"],
            ["Website", "https://hfr.health.gov.ng"],
        ]
    )

    add_info_box(doc, "📦 Postman Collection: A ready-to-import Postman collection is available alongside this guide. "
                      "Import the JSON file into Postman, set the base_url and api_key collection variables, and start testing.")

    # ── Footer ──
    doc.add_paragraph()
    footer = doc.add_paragraph()
    footer.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = footer.add_run("© 2026 Federal Ministry of Health, Nigeria — Health Facility Registry\n"
                         "This document is confidential and intended for registered API consumers.")
    run.font.size = Pt(8)
    run.font.color.rgb = MUTED

    return doc


# ═══════════════════════════════════════════════════════════════════
#  DOCUMENT 2: SYSTEM ARCHITECTURE
# ═══════════════════════════════════════════════════════════════════

def build_architecture_doc():
    doc = Document()

    section = doc.sections[0]
    section.page_width = Cm(21)
    section.page_height = Cm(29.7)
    section.top_margin = Cm(2)
    section.bottom_margin = Cm(2)
    section.left_margin = Cm(2.5)
    section.right_margin = Cm(2.5)

    # ── COVER ──
    for _ in range(6):
        doc.add_paragraph()

    title = doc.add_paragraph()
    title.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = title.add_run("🏥")
    run.font.size = Pt(48)

    title2 = doc.add_paragraph()
    title2.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = title2.add_run("HFR — System Architecture &\nData Flow Diagrams")
    run.font.size = Pt(28)
    run.font.bold = True
    run.font.color.rgb = BLUE

    sub = doc.add_paragraph()
    sub.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = sub.add_run("Hospital Facility Registry Platform")
    run.font.size = Pt(16)
    run.font.color.rgb = ACCENT

    doc.add_paragraph()
    meta = doc.add_paragraph()
    meta.alignment = WD_ALIGN_PARAGRAPH.CENTER
    for line in [
        "Technical Reference Document v1.0",
        "Date: February 2026",
        "Classification: Internal / Technical",
    ]:
        run = meta.add_run(line + "\n")
        run.font.size = Pt(10)
        run.font.color.rgb = MUTED

    doc.add_page_break()

    # ── TOC ──
    add_heading_styled(doc, "Table of Contents", level=1)
    toc = [
        "1. System Overview",
        "2. High-Level Architecture Diagram",
        "3. Technology Stack",
        "4. Component Architecture",
        "5. Database Entity Relationship Diagram",
        "6. API Architecture — Internal vs External",
        "7. Data Flow: Facility Registration",
        "8. Data Flow: External API Request",
        "9. Data Flow: API Key Request & Approval",
        "10. Data Flow: Facility Search (Public Website)",
        "11. Authentication & Authorization Flow",
        "12. Rate Limiting & Security Architecture",
        "13. Deployment Architecture",
        "14. Data Flow Summary Matrix",
        "15. API Versioning & Deprecation Strategy",
        "16. Appendix: Directory Structure",
    ]
    for item in toc:
        p = doc.add_paragraph(item)
        p.paragraph_format.space_after = Pt(2)
        for run in p.runs:
            run.font.size = Pt(11)
            run.font.color.rgb = BLUE

    doc.add_page_break()

    # ── 1. SYSTEM OVERVIEW ──
    add_heading_styled(doc, "1. System Overview", level=1)
    doc.add_paragraph(
        "The Hospital Facility Registry (HFR) is Nigeria's authoritative registry of healthcare facilities. "
        "It serves as a centralized platform for registering, validating, and publishing data about hospitals, "
        "pharmacies, laboratories, imaging centers, and related health infrastructure across all 36 states and the FCT."
    )

    add_heading_styled(doc, "Key Capabilities", level=2, color=ACCENT)
    capabilities = [
        "Facility Registration & Management — Multi-step onboarding with validation workflows",
        "Public Directory — Searchable, filterable public website for citizens",
        "External API — Versioned, authenticated REST API for third-party developers",
        "Admin Dashboard — Internal management console for FMOH administrators",
        "Reporting & Analytics — Facility statistics, geographic distribution, and metrics",
        "Integration Hub — Interoperability with state-level health systems",
    ]
    for c in capabilities:
        doc.add_paragraph(c, style='List Bullet')

    add_heading_styled(doc, "User Roles", level=2, color=ACCENT)
    add_styled_table(doc,
        ["Role", "Description", "Access"],
        [
            ["Public User", "Citizens searching for facilities", "Public website (Next.js)"],
            ["Facility Admin", "Hospital staff registering their facility", "Registration portal"],
            ["State Officer", "State-level reviewer", "Admin dashboard (limited)"],
            ["FMOH Admin", "Federal ministry administrator", "Full admin dashboard"],
            ["Super Admin", "System administrator", "All systems + user mgmt"],
            ["API Consumer", "External developer", "External API v1 endpoints"],
        ]
    )

    # ── 2. HIGH-LEVEL ARCHITECTURE ──
    doc.add_page_break()
    add_heading_styled(doc, "2. High-Level Architecture Diagram", level=1)
    doc.add_paragraph("The HFR platform follows a layered architecture pattern with clear separation between "
                      "the presentation layer, API gateway, application logic, and data storage.")

    add_code_block(doc,
        '┌─────────────────────────────────────────────────────────┐\n'
        '│                      CLIENTS                            │\n'
        '│  Browser    Mobile    Postman    Third-Party Systems    │\n'
        '└──────────────────────┬──────────────────────────────────┘\n'
        '                       │ HTTPS\n'
        '                       ▼\n'
        '┌─────────────────────────────────────────────────────────┐\n'
        '│              PRESENTATION LAYER                         │\n'
        '│                                                         │\n'
        '│  Next.js Frontend          Laravel Admin Views          │\n'
        '│  (React/TypeScript)        (Blade/AdminLTE)            │\n'
        '│  Port: 3000                Port: 8000                  │\n'
        '└──────────────────────┬──────────────────────────────────┘\n'
        '                       │\n'
        '                       ▼\n'
        '┌─────────────────────────────────────────────────────────┐\n'
        '│               API GATEWAY LAYER                         │\n'
        '│                                                         │\n'
        '│  Internal API (/api/*)     External API (/api/v1/*)    │\n'
        '│  - Powers Next.js          - Versioned (v1, v2, ...)   │\n'
        '│  - No auth (public)        - API Key authentication    │\n'
        '│  - throttle: 5000/min      - Per-client rate limiting  │\n'
        '│  - Raw JSON                - Standardized envelope     │\n'
        '└──────────────────────┬──────────────────────────────────┘\n'
        '                       │\n'
        '                       ▼\n'
        '┌─────────────────────────────────────────────────────────┐\n'
        '│              APPLICATION LAYER                          │\n'
        '│              (Laravel backend_v2)                       │\n'
        '│                                                         │\n'
        '│  Middleware → Controllers → Services → Models → DB     │\n'
        '│  API Resources (Transformers)                          │\n'
        '│  Mailables (API key delivery)                          │\n'
        '│  Artisan Commands (key management)                     │\n'
        '└──────────────────────┬──────────────────────────────────┘\n'
        '                       │\n'
        '                       ▼\n'
        '┌─────────────────────────────────────────────────────────┐\n'
        '│                 DATA LAYER                              │\n'
        '│                                                         │\n'
        '│  MySQL Database   │  File Storage  │  Mail Server      │\n'
        '│  (facilities,     │  (uploads,     │  (SMTP, key       │\n'
        '│   api_clients,    │   exports)     │   delivery)       │\n'
        '│   logs, lookups)  │                │                    │\n'
        '└─────────────────────────────────────────────────────────┘'
    )

    # ── 3. TECH STACK ──
    doc.add_page_break()
    add_heading_styled(doc, "3. Technology Stack", level=1)
    add_styled_table(doc,
        ["Component", "Technology", "Purpose"],
        [
            ["Frontend (Public)", "Next.js 14, React 18, TypeScript, Chakra UI", "Public website, facility search, API docs"],
            ["Backend API", "Laravel 10, PHP 8.1+", "REST API, business logic, data access"],
            ["Admin Dashboard", "AdminLTE 3, Blade, jQuery, DataTables", "Internal management for FMOH admins"],
            ["Database", "MySQL 8.0, Eloquent ORM", "Primary data store"],
            ["Auth (Admin)", "Laravel Sanctum, Spatie Permissions", "Session-based + RBAC"],
            ["Auth (API)", "Custom API Key (X-API-Key header)", "Per-client key auth, SHA-256 hashed"],
            ["Email", "Laravel Mail, SMTP", "API key delivery, notifications"],
            ["Styling (Frontend)", "Tailwind CSS, Chakra UI, DaisyUI", "Component styling"],
            ["State Management", "Redux Toolkit", "Frontend state management"],
            ["Maps", "Leaflet.js", "Interactive facility maps"],
        ]
    )

    # ── 4. COMPONENT ARCHITECTURE ──
    add_heading_styled(doc, "4. Component Architecture", level=1)
    doc.add_paragraph("The Laravel backend follows a layered component architecture:")

    add_code_block(doc,
        'HTTP Request\n'
        '     │\n'
        '     ▼\n'
        'Laravel Router\n'
        '  ├── routes/web.php      → Admin Dashboard (Blade views)\n'
        '  ├── routes/api.php      → Internal API (powers Next.js)\n'
        '  └── routes/api_v1.php   → External API v1 (developers)\n'
        '     │\n'
        '     ▼\n'
        'Middleware Pipeline\n'
        '  ├── VerifyApiKey        → Validate X-API-Key header\n'
        '  ├── LogApiRequest       → Record request to DB\n'
        '  ├── ApiVersionHeader    → Add X-API-Version header\n'
        '  └── ThrottleRequests    → Per-client rate enforcement\n'
        '     │\n'
        '     ▼\n'
        'Controllers\n'
        '  ├── FacilityController  → Facility CRUD + search\n'
        '  ├── PharmacyController  → Pharmacy listing + detail\n'
        '  ├── LaboratoryController→ Laboratory listing + detail\n'
        '  ├── ImagingController   → Imaging listing + detail\n'
        '  ├── LookupController    → Reference data (13 endpoints)\n'
        '  └── ApiKeyRequestController → Key request + status\n'
        '     │\n'
        '     ▼\n'
        'API Resources (Transformers)\n'
        '  FacilityResource, PharmacyResource, LaboratoryResource, etc.\n'
        '     │\n'
        '     ▼\n'
        'Models (Eloquent ORM) → MySQL Database'
    )

    # ── 5. ERD ──
    doc.add_page_break()
    add_heading_styled(doc, "5. Database Entity Relationship Diagram", level=1)

    add_code_block(doc,
        '┌──────────────┐     ┌──────────────┐     ┌──────────────┐\n'
        '│   states     │     │    lgas      │     │    wards     │\n'
        '├──────────────┤     ├──────────────┤     ├──────────────┤\n'
        '│ PK id        │◄────│ FK state_id  │◄────│ FK lga_id    │\n'
        '│    name      │     │ PK id        │     │ PK id        │\n'
        '│    geo_zone  │     │    name      │     │    name      │\n'
        '└──────────────┘     └──────────────┘     └──────────────┘\n'
        '       ▲                    ▲                    ▲\n'
        '       │                    │                    │\n'
        '       └────────────────────┼────────────────────┘\n'
        '                            │\n'
        '              ┌─────────────┴────────────────────────────┐\n'
        '              │           facilities                     │\n'
        '              ├──────────────────────────────────────────┤\n'
        '              │ PK id                                    │\n'
        '              │ FK state_id, lga_id, ward_id             │\n'
        '              │ FK facility_level_id, ownership_id       │\n'
        '              │    unique_id, facility_name              │\n'
        '              │    registration_no, facility_code_hfr    │\n'
        '              │    operational_status, license_status    │\n'
        '              │    latitude, longitude, phone, email     │\n'
        '              │    ... (40+ columns)                     │\n'
        '              └──────────┬───────────────────────────────┘\n'
        '                         │\n'
        '          ┌──────────────┼──────────────┐\n'
        '          ▼              ▼              ▼\n'
        '   ┌────────────┐ ┌────────────┐ ┌────────────┐\n'
        '   │ pharmacies │ │laboratories│ │  imaging   │\n'
        '   │ FK facil_id│ │ FK facil_id│ │ FK facil_id│\n'
        '   └────────────┘ └────────────┘ └────────────┘\n'
        '\n'
        '┌──────────────┐     ┌──────────────────┐\n'
        '│ api_clients  │     │ api_request_logs │\n'
        '├──────────────┤     ├──────────────────┤\n'
        '│ PK id        │◄────│ FK api_client_id │\n'
        '│    name      │     │ PK id            │\n'
        '│    email     │     │    method         │\n'
        '│    api_key   │     │    endpoint       │\n'
        '│    status    │     │    ip_address     │\n'
        '│    rate_limit│     │    status_code    │\n'
        '│    is_active │     │    response_time  │\n'
        '│    expires_at│     │    created_at     │\n'
        '│    use_case  │     └──────────────────┘\n'
        '│    reviewed_by│\n'
        '└──────────────┘'
    )

    # ── 6. API ARCHITECTURE ──
    doc.add_page_break()
    add_heading_styled(doc, "6. API Architecture — Internal vs External", level=1)

    add_styled_table(doc,
        ["Aspect", "Internal API (/api/*)", "External API (/api/v1/*)"],
        [
            ["Purpose", "Power the public Next.js website", "Third-party developer access"],
            ["Authentication", "None (public website)", "API Key required (X-API-Key)"],
            ["Rate Limit", "5000/min global", "60/min per client (configurable)"],
            ["Response Format", "Raw JSON (varies)", "Standardized envelope with meta"],
            ["Versioning", "None", "URL-based (v1, v2, ...)"],
            ["Logging", "Standard Laravel logs", "Dedicated api_request_logs table"],
            ["Transformers", "None (raw model data)", "API Resources (curated fields)"],
            ["Breaking Changes", "Coordinated with frontend team", "Managed via versioning + sunset policy"],
            ["Route File", "routes/api.php", "routes/api_v1.php"],
        ]
    )

    # ── 7. DFD FACILITY REGISTRATION ──
    add_heading_styled(doc, "7. Data Flow: Facility Registration", level=1)
    add_code_block(doc,
        'Facility Admin                                    FMOH Admin\n'
        '     │                                                │\n'
        '     │ 1. Fill registration form                      │\n'
        '     ▼                                                │\n'
        '┌────────────────────────────────┐                    │\n'
        '│ 1.0 VALIDATE & GENERATE CODES │                    │\n'
        '│  • Validate all fields        │                    │\n'
        '│  • Generate unique_id         │                    │\n'
        '│  • Check for duplicates       │                    │\n'
        '└────────────┬─────────────────┘                    │\n'
        '              │                                      │\n'
        '              │ 2. Store (status: pending)           │\n'
        '              ▼                                      │\n'
        '       ┌──────────────┐                              │\n'
        '       │  facilities  │                              │\n'
        '       │   (MySQL)    │◄──── 3. Review ──────────────┘\n'
        '       └──────┬───────┘\n'
        '              │\n'
        '              │ 4. Approve → status: approved\n'
        '              ▼\n'
        '       ┌────────────────────┐\n'
        '       │ PUBLISH            │\n'
        '       │ • Public directory │\n'
        '       │ • Available via    │\n'
        '       │   internal &      │\n'
        '       │   external APIs   │\n'
        '       └────────────────────┘'
    )

    # ── 8. DFD EXTERNAL API ──
    doc.add_page_break()
    add_heading_styled(doc, "8. Data Flow: External API Request", level=1)
    doc.add_paragraph("Complete lifecycle of an authenticated external API request:")

    add_code_block(doc,
        'External Developer\n'
        '     │\n'
        '     │ 1. GET /api/v1/facilities?state_id=25\n'
        '     │    Header: X-API-Key: hfr_abc123...\n'
        '     ▼\n'
        '┌──────────────────────────────────────────────────┐\n'
        '│ 1.0 API KEY VERIFICATION [VerifyApiKey]          │\n'
        '│  • Extract X-API-Key header                      │\n'
        '│  • Look up api_clients table (hash match)        │\n'
        '│  • Verify active, approved, not expired          │\n'
        '│  FAIL → 401/403 error response                   │\n'
        '└──────────────────┬───────────────────────────────┘\n'
        '                   │ PASS\n'
        '                   ▼\n'
        '┌──────────────────────────────────────────────────┐\n'
        '│ 2.0 RATE LIMIT CHECK [ThrottleRequests]          │\n'
        '│  • Check client rate_limit (default: 60/min)     │\n'
        '│  • Add X-RateLimit-* response headers            │\n'
        '│  EXCEEDED → 429 error response                   │\n'
        '└──────────────────┬───────────────────────────────┘\n'
        '                   │ OK\n'
        '                   ▼\n'
        '┌──────────────────────────────────────────────────┐\n'
        '│ 3.0 REQUEST PROCESSING [Controller]              │\n'
        '│  • Parse query parameters                        │\n'
        '│  • Build Eloquent query + eager load relations   │\n'
        '│  • Apply filters, search, pagination             │\n'
        '└──────────────────┬───────────────────────────────┘\n'
        '                   │\n'
        '                   ▼\n'
        '            ┌──────────────┐\n'
        '            │   MySQL DB   │\n'
        '            └──────┬───────┘\n'
        '                   │\n'
        '                   ▼\n'
        '┌──────────────────────────────────────────────────┐\n'
        '│ 4.0 RESPONSE TRANSFORMATION [API Resources]      │\n'
        '│  • Transform DB columns → clean API fields       │\n'
        '│  • Nest related data (state, LGA, ward)          │\n'
        '│  • Wrap in standard envelope                     │\n'
        '└──────────────────┬───────────────────────────────┘\n'
        '                   │\n'
        '                   ▼\n'
        '┌──────────────────────────────────────────────────┐\n'
        '│ 5.0 REQUEST LOGGING [LogApiRequest]              │\n'
        '│  • Record: client, method, endpoint, status,     │\n'
        '│    response_time, IP, user_agent                  │\n'
        '└──────────────────┬───────────────────────────────┘\n'
        '                   │\n'
        '                   ▼\n'
        '            JSON Response → Developer'
    )

    # ── 9. DFD API KEY REQUEST ──
    doc.add_page_break()
    add_heading_styled(doc, "9. Data Flow: API Key Request & Approval", level=1)

    add_code_block(doc,
        'Developer                                     FMOH Admin\n'
        '     │                                             │\n'
        '     │ 1. POST /api/v1/request-key                 │\n'
        '     │    {name, email, org, use_case}              │\n'
        '     ▼                                             │\n'
        '┌────────────────────────────────┐                 │\n'
        '│ 1.0 SUBMIT REQUEST             │                 │\n'
        '│  • Validate input              │                 │\n'
        '│  • Check duplicate email       │                 │\n'
        '│  • Rate limit: 5/min           │                 │\n'
        '│  • Create record (pending)     │                 │\n'
        '└──────────┬─────────────────────┘                 │\n'
        '           │                                       │\n'
        '           ▼                                       │\n'
        '    ┌──────────────┐                               │\n'
        '    │ api_clients  │  3. View pending ─────────────┘\n'
        '    │ status:      │     /admin/api-clients/pending\n'
        '    │ "pending"    │\n'
        '    └──────┬───────┘\n'
        '           │\n'
        '           ▼\n'
        '┌────────────────────────────────┐\n'
        '│ 2.0 ADMIN REVIEW               │\n'
        '│  • View name, email, use case  │\n'
        '│  • [Approve] or [Reject]       │\n'
        '└───────┬──────────┬─────────────┘\n'
        '        │          │\n'
        '   Approve      Reject\n'
        '        │          │\n'
        '        ▼          ▼\n'
        '  ┌───────────┐  ┌───────────┐\n'
        '  │ Generate   │  │ Set status│\n'
        '  │ API key    │  │ rejected  │\n'
        '  │ (hfr_...) │  │ + reason  │\n'
        '  └─────┬─────┘  └─────┬─────┘\n'
        '        │              │\n'
        '        ▼              ▼\n'
        '  ┌───────────┐  ┌───────────┐\n'
        '  │ Email:     │  │ Email:     │\n'
        '  │ Approved   │  │ Rejected   │\n'
        '  │ + API key  │  │ + reason   │\n'
        '  └───────────┘  └───────────┘'
    )

    # ── 10. DFD FACILITY SEARCH ──
    add_heading_styled(doc, "10. Data Flow: Facility Search (Public Website)", level=1)

    add_code_block(doc,
        'Citizen (Browser)\n'
        '     │\n'
        '     │ 1. Visit hfrportal.ng\n'
        '     │    Enter: state, LGA, facility name\n'
        '     ▼\n'
        '┌────────────────────────────────┐\n'
        '│ NEXT.JS FRONTEND              │\n'
        '│  • Render search form         │\n'
        '│  • State/LGA dropdowns        │\n'
        '│  • Category filters           │\n'
        '└──────────┬─────────────────────┘\n'
        '           │\n'
        '           │ 2. GET /api/get-facilities-by-state?state=25\n'
        '           │    (Internal API, no auth)\n'
        '           ▼\n'
        '┌────────────────────────────────┐\n'
        '│ LARAVEL INTERNAL API          │\n'
        '│  [FrontendController]         │\n'
        '│  • Query facilities           │\n'
        '│  • Join states, LGAs          │\n'
        '│  • Paginate results           │\n'
        '└──────────┬─────────────────────┘\n'
        '           │\n'
        '           ▼\n'
        '    ┌──────────────┐\n'
        '    │   MySQL DB   │\n'
        '    └──────┬───────┘\n'
        '           │\n'
        '           ▼\n'
        '┌────────────────────────────────┐\n'
        '│ NEXT.JS FRONTEND              │\n'
        '│  • Display facility cards     │\n'
        '│  • Show on interactive map    │\n'
        '│  • Pagination controls        │\n'
        '└────────────────────────────────┘\n'
        '           │\n'
        '           ▼\n'
        '     Citizen Browser'
    )

    # ── 11. AUTH FLOW ──
    doc.add_page_break()
    add_heading_styled(doc, "11. Authentication & Authorization Flow", level=1)

    add_heading_styled(doc, "Flow A: Admin Dashboard (Session-Based)", level=2, color=ACCENT)
    add_code_block(doc,
        'Admin → GET /login → Login Form (Blade)\n'
        '  │\n'
        '  │ POST /login (email + password)\n'
        '  ▼\n'
        'Laravel Auth Guard → Verify credentials (bcrypt)\n'
        '  │\n'
        '  ├── FAIL → Redirect with error\n'
        '  └── SUCCESS → Create session + cookie\n'
        '       │\n'
        '       ▼\n'
        '  Spatie Role Check:\n'
        '    ├── Super Admin → Full access\n'
        '    ├── State Officer → State-scoped\n'
        '    └── Facility Admin → Own facility only\n'
        '       │\n'
        '       ▼\n'
        '  Middleware: auth, verified, role:admin\n'
        '  → Access granted to /admin/* routes'
    )

    add_heading_styled(doc, "Flow B: External API (API Key)", level=2, color=ACCENT)
    add_code_block(doc,
        'Developer → GET /api/v1/facilities\n'
        '            Header: X-API-Key: hfr_a1b2c3...\n'
        '  │\n'
        '  ▼\n'
        'VerifyApiKey Middleware:\n'
        '  ├── No header?     → 401 "API key required"\n'
        '  ├── Not found?     → 401 "Invalid API key"\n'
        '  ├── is_active=false → 403 "API key deactivated"\n'
        '  ├── status≠approved → 403 "API key not approved"\n'
        '  ├── expired?       → 403 "API key expired"\n'
        '  └── VALID          → Bind client to request\n'
        '                       Continue → Rate limiter → Controller'
    )

    # ── 12. SECURITY ARCHITECTURE ──
    add_heading_styled(doc, "12. Rate Limiting & Security Architecture", level=1)
    doc.add_paragraph("The API employs a defense-in-depth approach with 8 security layers:")

    add_code_block(doc,
        'Layer 1: CORS Policy          Block unauthorized origins\n'
        'Layer 2: TLS / HTTPS          Encrypt data in transit\n'
        'Layer 3: API Key Auth          Verify X-API-Key header\n'
        'Layer 4: Rate Limiting         Per-client rate enforcement\n'
        'Layer 5: Input Validation      Form Requests, sanitization\n'
        'Layer 6: Query Protection      Eloquent ORM (parameterized queries)\n'
        'Layer 7: Response Transform    API Resources strip sensitive columns\n'
        'Layer 8: Request Logging       Full audit trail in DB'
    )

    add_heading_styled(doc, "Rate Limiting Tiers", level=2, color=ACCENT)
    add_styled_table(doc,
        ["Tier", "Requests/Min", "Use Case"],
        [
            ["Public (key request)", "5", "Prevent spam on key request endpoint"],
            ["Standard", "60", "Default for all approved API clients"],
            ["Premium", "300", "High-volume integrations (e.g., DHIS2)"],
            ["Internal", "5000", "Next.js frontend (internal API only)"],
        ]
    )

    # ── 13. DEPLOYMENT ──
    doc.add_page_break()
    add_heading_styled(doc, "13. Deployment Architecture", level=1)

    add_code_block(doc,
        '              Internet\n'
        '                 │\n'
        '                 ▼\n'
        '        ┌─────────────────┐\n'
        '        │  DNS / CDN      │\n'
        '        │  (Cloudflare)   │\n'
        '        └────────┬────────┘\n'
        '                 │\n'
        '        ┌────────┴────────┐\n'
        '        │  Nginx          │\n'
        '        │  (Reverse Proxy)│\n'
        '        └───┬─────────┬───┘\n'
        '            │         │\n'
        '   ┌────────┘         └────────┐\n'
        '   ▼                           ▼\n'
        '┌──────────────┐     ┌──────────────┐\n'
        '│ Next.js App  │     │ Laravel App  │\n'
        '│ Port: 3000   │────▶│ Port: 8000   │\n'
        '│              │     │              │\n'
        '│ SSR pages    │     │ Internal API │\n'
        '│ Static pages │     │ External API │\n'
        '│ Dev portal   │     │ Admin views  │\n'
        '└──────────────┘     └──────┬───────┘\n'
        '                            │\n'
        '                   ┌────────┴────────┐\n'
        '                   │                 │\n'
        '            ┌──────┴──────┐  ┌───────┴──────┐\n'
        '            │  MySQL DB   │  │  Mail Server │\n'
        '            │             │  │  (SMTP)      │\n'
        '            └─────────────┘  └──────────────┘'
    )

    # ── 14. MATRIX ──
    add_heading_styled(doc, "14. Data Flow Summary Matrix", level=1)
    add_styled_table(doc,
        ["#", "Flow Name", "Source", "Destination", "Auth", "Data"],
        [
            ["F1", "Public search", "Browser", "Next.js → Internal API", "None", "Facility queries & results"],
            ["F2", "External API query", "Developer app", "External API v1", "X-API-Key", "Facility/pharmacy/lab data"],
            ["F3", "API key request", "Developer (browser)", "Next.js → API v1", "None (rate-limited)", "Name, email, org, use case"],
            ["F4", "Key approval", "Admin dashboard", "Laravel → MySQL → SMTP", "Session", "API key + metadata"],
            ["F5", "Admin login", "Browser", "Laravel Auth", "Email + password", "Session cookie"],
            ["F6", "Facility registration", "Facility admin", "Laravel → MySQL", "Session", "Full facility record"],
            ["F7", "Request logging", "Middleware", "MySQL (api_request_logs)", "System", "Client, endpoint, timing"],
            ["F8", "Lookup data", "Any client", "API → MySQL", "Varies", "States, LGAs, wards, types"],
            ["F9", "Key delivery email", "Laravel", "Developer inbox", "SMTP", "API key, docs link"],
            ["F10", "Analytics/reports", "MySQL", "Admin dashboard", "Session", "Aggregated statistics"],
        ]
    )

    # ── 15. VERSIONING ──
    doc.add_page_break()
    add_heading_styled(doc, "15. API Versioning & Deprecation Strategy", level=1)

    add_heading_styled(doc, "Versioning Rules", level=2, color=ACCENT)
    rules = [
        "URL-based versioning: /api/v1/, /api/v2/, etc.",
        "Non-breaking changes (new fields, new endpoints) added without new version.",
        "Breaking changes (removing fields, restructuring) require a new version.",
        "Sunset period: Minimum 6 months between deprecation and shutdown.",
        "Deprecation headers (Sunset, Deprecation) added to all deprecated responses.",
        "Email notification to all API clients before any version sunset.",
    ]
    for r in rules:
        doc.add_paragraph(r, style='List Number')

    add_heading_styled(doc, "Change Classification", level=2, color=ACCENT)
    add_styled_table(doc,
        ["Change Type", "Example", "Versioning Impact"],
        [
            ["Additive", "New optional param, new response field", "Same version — no action"],
            ["Behavioral", "Performance improvement, better errors", "Same version — changelog"],
            ["Breaking", "Field renamed, endpoint removed", "New version required (v1→v2)"],
            ["Security fix", "Vulnerability patch", "Applied to all versions immediately"],
        ]
    )

    # ── 16. DIRECTORY STRUCTURE ──
    add_heading_styled(doc, "16. Appendix: Directory Structure", level=1)

    add_heading_styled(doc, "Backend (Laravel — backend_v2)", level=2, color=ACCENT)
    add_code_block(doc,
        'backend_v2/\n'
        '├── app/\n'
        '│   ├── Console/Commands/\n'
        '│   │   ├── GenerateApiKey.php\n'
        '│   │   ├── RevokeApiKey.php\n'
        '│   │   └── ListApiKeys.php\n'
        '│   ├── Http/\n'
        '│   │   ├── Controllers/\n'
        '│   │   │   ├── Admin/ApiClientController.php\n'
        '│   │   │   └── Api/V1/\n'
        '│   │   │       ├── BaseApiController.php\n'
        '│   │   │       ├── FacilityController.php\n'
        '│   │   │       ├── PharmacyController.php\n'
        '│   │   │       ├── LaboratoryController.php\n'
        '│   │   │       ├── ImagingController.php\n'
        '│   │   │       ├── LookupController.php\n'
        '│   │   │       └── ApiKeyRequestController.php\n'
        '│   │   ├── Middleware/\n'
        '│   │   │   ├── VerifyApiKey.php\n'
        '│   │   │   ├── LogApiRequest.php\n'
        '│   │   │   └── ApiVersionHeader.php\n'
        '│   │   └── Resources/V1/\n'
        '│   │       ├── FacilityResource.php\n'
        '│   │       ├── FacilityCollection.php\n'
        '│   │       ├── PharmacyResource.php\n'
        '│   │       ├── LaboratoryResource.php\n'
        '│   │       ├── ImagingResource.php\n'
        '│   │       └── LookupResource.php\n'
        '│   ├── Mail/\n'
        '│   │   ├── ApiKeyApproved.php\n'
        '│   │   └── ApiKeyRejected.php\n'
        '│   └── Models/\n'
        '│       ├── ApiClient.php\n'
        '│       └── ApiRequestLog.php\n'
        '├── database/migrations/\n'
        '├── resources/views/\n'
        '│   ├── api-clients/  (index, create, pending, logs)\n'
        '│   └── mails/        (api-key-approved, api-key-rejected)\n'
        '├── routes/\n'
        '│   ├── api.php       (Internal API)\n'
        '│   ├── api_v1.php    (External API v1)\n'
        '│   └── web.php       (Admin routes)\n'
        '└── docs/\n'
        '    ├── HFR_API_v1_Postman_Collection.json\n'
        '    ├── HFR_API_Guide.docx\n'
        '    └── HFR_System_Architecture.docx'
    )

    add_heading_styled(doc, "Frontend (Next.js)", level=2, color=ACCENT)
    add_code_block(doc,
        'frontend/\n'
        '├── app/(landingpage)/developers/page.tsx\n'
        '├── components/sections/developers/DeveloperDocs.tsx\n'
        '└── data/NavLinks.ts'
    )

    # ── FOOTER ──
    doc.add_paragraph()
    footer = doc.add_paragraph()
    footer.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = footer.add_run("© 2026 Federal Ministry of Health, Nigeria — Health Facility Registry\n"
                         "For Internal Technical Use")
    run.font.size = Pt(8)
    run.font.color.rgb = MUTED

    return doc


# ═══════════════════════════════════════════════════════════════════
#  MAIN
# ═══════════════════════════════════════════════════════════════════

if __name__ == "__main__":
    script_dir = os.path.dirname(os.path.abspath(__file__))

    print("Generating HFR API Guide (.docx)...")
    api_doc = build_api_guide()
    api_path = os.path.join(script_dir, "HFR_API_Guide.docx")
    api_doc.save(api_path)
    print(f"  ✓ Saved: {api_path}")

    print("Generating HFR System Architecture (.docx)...")
    arch_doc = build_architecture_doc()
    arch_path = os.path.join(script_dir, "HFR_System_Architecture.docx")
    arch_doc.save(arch_path)
    print(f"  ✓ Saved: {arch_path}")

    print("\nDone! Both documents generated successfully.")
