"use client";

import React, { useState } from "react";
import {
  FiKey,
  FiBook,
  FiCode,
  FiShield,
  FiZap,
  FiList,
  FiSearch,
  FiCopy,
  FiCheck,
  FiChevronRight,
  FiAlertTriangle,
  FiSend,
  FiMail,
} from "react-icons/fi";

// ─── Data ───────────────────────────────────────────────────────────
const API_BASE = (process.env.NEXT_PUBLIC_BACKEND_URL || "http://127.0.0.1:8000").replace(/\/$/, "");

interface Endpoint {
  method: string;
  path: string;
  description: string;
  params?: { name: string; type: string; required: boolean; description: string }[];
  sampleResponse?: string;
}

interface EndpointGroup {
  title: string;
  icon: React.ReactNode;
  endpoints: Endpoint[];
}

const endpointGroups: EndpointGroup[] = [
  {
    title: "Facilities (Hospitals & Clinics)",
    icon: <FiList />,
    endpoints: [
      {
        method: "GET",
        path: "/api/v1/facilities",
        description: "Search and list health facilities with filtering and pagination.",
        params: [
          { name: "search", type: "string", required: false, description: "Search by facility name, state, LGA, or ward" },
          { name: "state_id", type: "integer", required: false, description: "Filter by state ID" },
          { name: "lga_id", type: "integer", required: false, description: "Filter by LGA ID" },
          { name: "ward_id", type: "integer", required: false, description: "Filter by ward ID" },
          { name: "facility_type_id", type: "integer", required: false, description: "Filter by facility type" },
          { name: "facility_level_id", type: "integer", required: false, description: "Filter by level of care" },
          { name: "ownership_id", type: "integer", required: false, description: "Filter by ownership category" },
          { name: "operational_status_id", type: "integer", required: false, description: "Filter by operational status" },
          { name: "registration_status_id", type: "integer", required: false, description: "Filter by registration status" },
          { name: "has_coordinates", type: "boolean", required: false, description: "Filter facilities with/without GPS coordinates" },
          { name: "service_ids", type: "string", required: false, description: "Comma-separated service IDs to filter by" },
          { name: "per_page", type: "integer", required: false, description: "Results per page (max 100, default 25)" },
          { name: "page", type: "integer", required: false, description: "Page number" },
          { name: "sort_by", type: "string", required: false, description: "Sort by: facility_name, state, lga, updated_at" },
          { name: "sort_order", type: "string", required: false, description: "Sort direction: asc or desc" },
        ],
        sampleResponse: `{
  "status": "success",
  "data": {
    "facilities": [
      {
        "id": 123,
        "unique_id": "AB/01/H/1/P/0001",
        "facility_name": "General Hospital Maitama",
        "location": {
          "state": { "id": 25, "name": "FCT" },
          "lga": { "id": 512, "name": "Abuja Municipal" },
          "latitude": 9.0579,
          "longitude": 7.4951
        },
        "classification": {
          "facility_level": { "id": 2, "name": "Secondary" },
          "ownership": { "id": 1, "name": "Public" }
        },
        "status": {
          "operational": { "id": 1, "name": "Operational" },
          "registration": { "id": 1, "name": "Registered" }
        },
        "contact": {
          "phone_number": "+234...",
          "email": "info@hospital.ng"
        }
      }
    ]
  },
  "meta": {
    "current_page": 1,
    "per_page": 25,
    "total": 42567,
    "last_page": 1703
  }
}`,
      },
      {
        method: "GET",
        path: "/api/v1/facilities/{id}",
        description: "Get detailed information about a single facility.",
        params: [
          { name: "id", type: "integer", required: true, description: "The facility ID" },
        ],
      },
      {
        method: "GET",
        path: "/api/v1/facilities/{id}/services",
        description: "Get services offered by a specific facility.",
        params: [
          { name: "id", type: "integer", required: true, description: "The facility ID" },
        ],
      },
      {
        method: "GET",
        path: "/api/v1/facilities/statistics",
        description: "Get summary statistics (totals by ownership, level of care, state).",
        params: [
          { name: "state_id", type: "integer", required: false, description: "Filter stats by state" },
          { name: "lga_id", type: "integer", required: false, description: "Filter stats by LGA" },
        ],
      },
    ],
  },
  {
    title: "Pharmacies",
    icon: <FiList />,
    endpoints: [
      {
        method: "GET",
        path: "/api/v1/pharmacies",
        description: "Search and list pharmaceutical premises.",
        params: [
          { name: "search", type: "string", required: false, description: "Search by facility name" },
          { name: "state_id", type: "integer", required: false, description: "Filter by state" },
          { name: "lga_id", type: "integer", required: false, description: "Filter by LGA" },
          { name: "ownership_id", type: "integer", required: false, description: "Filter by ownership" },
          { name: "per_page", type: "integer", required: false, description: "Results per page (max 100)" },
          { name: "page", type: "integer", required: false, description: "Page number" },
        ],
      },
      {
        method: "GET",
        path: "/api/v1/pharmacies/{id}",
        description: "Get pharmacy details by ID.",
      },
    ],
  },
  {
    title: "Laboratories",
    icon: <FiList />,
    endpoints: [
      {
        method: "GET",
        path: "/api/v1/laboratories",
        description: "Search and list laboratory premises.",
        params: [
          { name: "search", type: "string", required: false, description: "Search by facility name" },
          { name: "state_id", type: "integer", required: false, description: "Filter by state" },
          { name: "lga_id", type: "integer", required: false, description: "Filter by LGA" },
          { name: "accreditation_status_id", type: "integer", required: false, description: "Filter by accreditation" },
          { name: "per_page", type: "integer", required: false, description: "Results per page (max 100)" },
          { name: "page", type: "integer", required: false, description: "Page number" },
        ],
      },
      {
        method: "GET",
        path: "/api/v1/laboratories/{id}",
        description: "Get laboratory details by ID.",
      },
    ],
  },
  {
    title: "Imaging / Radiology",
    icon: <FiList />,
    endpoints: [
      {
        method: "GET",
        path: "/api/v1/imaging",
        description: "Search and list imaging/radiology premises.",
        params: [
          { name: "search", type: "string", required: false, description: "Search by facility name" },
          { name: "state_id", type: "integer", required: false, description: "Filter by state" },
          { name: "per_page", type: "integer", required: false, description: "Results per page (max 100)" },
          { name: "page", type: "integer", required: false, description: "Page number" },
        ],
      },
      {
        method: "GET",
        path: "/api/v1/imaging/{id}",
        description: "Get imaging facility details by ID.",
      },
    ],
  },
  {
    title: "Lookups / Reference Data",
    icon: <FiSearch />,
    endpoints: [
      { method: "GET", path: "/api/v1/lookups/states", description: "List all Nigerian states." },
      { method: "GET", path: "/api/v1/lookups/lgas", description: "List LGAs. Use ?state_id= to filter.", params: [{ name: "state_id", type: "integer", required: false, description: "Filter by state" }] },
      { method: "GET", path: "/api/v1/lookups/wards", description: "List wards. Use ?lga_id= to filter.", params: [{ name: "lga_id", type: "integer", required: false, description: "Filter by LGA" }] },
      { method: "GET", path: "/api/v1/lookups/facility-types", description: "List facility type classifications." },
      { method: "GET", path: "/api/v1/lookups/facility-levels", description: "List levels of care." },
      { method: "GET", path: "/api/v1/lookups/ownership", description: "List ownership categories." },
      { method: "GET", path: "/api/v1/lookups/ownership-types", description: "List ownership types. Use ?ownership_id= to filter.", params: [{ name: "ownership_id", type: "integer", required: false, description: "Filter by ownership category" }] },
      { method: "GET", path: "/api/v1/lookups/operational-statuses", description: "List operational statuses." },
      { method: "GET", path: "/api/v1/lookups/registration-statuses", description: "List registration statuses." },
      { method: "GET", path: "/api/v1/lookups/license-statuses", description: "List license statuses." },
      { method: "GET", path: "/api/v1/lookups/accreditation-statuses", description: "List accreditation statuses." },
      { method: "GET", path: "/api/v1/lookups/service-categories", description: "List health service categories." },
      { method: "GET", path: "/api/v1/lookups/services", description: "List health services. Use ?category_id= to filter.", params: [{ name: "category_id", type: "integer", required: false, description: "Filter by category" }] },
    ],
  },
];

// ─── Subcomponents ──────────────────────────────────────────────────

function CopyButton({ text }: { text: string }) {
  const [copied, setCopied] = useState(false);
  const handleCopy = () => {
    navigator.clipboard.writeText(text);
    setCopied(true);
    setTimeout(() => setCopied(false), 2000);
  };
  return (
    <button
      onClick={handleCopy}
      className="absolute top-2 right-2 p-1.5 rounded bg-gray-700 hover:bg-gray-600 text-gray-300 transition-colors"
      title="Copy"
    >
      {copied ? <FiCheck className="w-4 h-4 text-green-400" /> : <FiCopy className="w-4 h-4" />}
    </button>
  );
}

function MethodBadge({ method }: { method: string }) {
  const colors: Record<string, string> = {
    GET: "bg-green-100 text-green-800",
    POST: "bg-blue-100 text-blue-800",
    PUT: "bg-yellow-100 text-yellow-800",
    DELETE: "bg-red-100 text-red-800",
  };
  return (
    <span className={`inline-block px-2 py-0.5 rounded text-xs font-bold ${colors[method] || "bg-gray-100 text-gray-800"}`}>
      {method}
    </span>
  );
}

function EndpointCard({ endpoint }: { endpoint: Endpoint }) {
  const [open, setOpen] = useState(false);
  return (
    <div className="overflow-hidden border border-gray-200 rounded-lg">
      <button
        onClick={() => setOpen(!open)}
        className="flex items-center w-full gap-3 px-4 py-3 text-left transition-colors hover:bg-gray-50"
      >
        <FiChevronRight className={`w-4 h-4 transition-transform ${open ? "rotate-90" : ""}`} />
        <MethodBadge method={endpoint.method} />
        <code className="flex-1 font-mono text-sm text-gray-700">{endpoint.path}</code>
        <span className="hidden text-sm text-gray-500 md:inline">{endpoint.description}</span>
      </button>
      {open && (
        <div className="px-4 py-4 space-y-4 border-t border-gray-100 bg-gray-50">
          <p className="text-gray-600">{endpoint.description}</p>

          {endpoint.params && endpoint.params.length > 0 && (
            <div>
              <h4 className="mb-2 text-sm font-semibold">Parameters</h4>
              <div className="overflow-x-auto">
                <table className="w-full text-sm">
                  <thead>
                    <tr className="border-b border-gray-200">
                      <th className="py-1 pr-4 text-left">Name</th>
                      <th className="py-1 pr-4 text-left">Type</th>
                      <th className="py-1 pr-4 text-left">Required</th>
                      <th className="py-1 text-left">Description</th>
                    </tr>
                  </thead>
                  <tbody>
                    {endpoint.params.map((p) => (
                      <tr key={p.name} className="border-b border-gray-100">
                        <td className="py-1 pr-4"><code className="px-1 text-xs bg-gray-200 rounded">{p.name}</code></td>
                        <td className="py-1 pr-4 text-gray-500">{p.type}</td>
                        <td className="py-1 pr-4">{p.required ? <span className="font-medium text-red-600">Yes</span> : <span className="text-gray-400">No</span>}</td>
                        <td className="py-1 text-gray-600">{p.description}</td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          )}

          {endpoint.sampleResponse && (
            <div>
              <h4 className="mb-2 text-sm font-semibold">Sample Response</h4>
              <div className="relative">
                <CopyButton text={endpoint.sampleResponse} />
                <pre className="p-4 overflow-x-auto text-xs leading-relaxed text-green-300 bg-gray-900 rounded-lg">
                  {endpoint.sampleResponse}
                </pre>
              </div>
            </div>
          )}
        </div>
      )}
    </div>
  );
}

function SideNav({ active, onChange }: { active: string; onChange: (s: string) => void }) {
  const sections = [
    { id: "overview", label: "Overview", icon: <FiBook /> },
    { id: "request-key", label: "Request API Key", icon: <FiSend /> },
    { id: "authentication", label: "Authentication", icon: <FiKey /> },
    { id: "rate-limiting", label: "Rate Limiting", icon: <FiZap /> },
    { id: "endpoints", label: "API Endpoints", icon: <FiCode /> },
    { id: "errors", label: "Error Handling", icon: <FiAlertTriangle /> },
    { id: "examples", label: "Code Examples", icon: <FiCode /> },
    { id: "versioning", label: "Versioning", icon: <FiShield /> },
  ];

  return (
    <nav className="space-y-1">
      {sections.map((s) => (
        <button
          key={s.id}
          onClick={() => onChange(s.id)}
          className={`w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors ${
            active === s.id ? "bg-green-50 text-green-700 font-medium" : "text-gray-600 hover:bg-gray-100"
          }`}
        >
          {s.icon}
          {s.label}
        </button>
      ))}
    </nav>
  );
}

// ─── Main Component ─────────────────────────────────────────────────

export default function DeveloperDocs() {
  const [activeSection, setActiveSection] = useState("overview");

  // ── Request API Key form state ──
  const [formData, setFormData] = useState({ name: "", email: "", organisation: "", use_case: "" });
  const [formStatus, setFormStatus] = useState<"idle" | "loading" | "success" | "error">("idle");
  const [formMessage, setFormMessage] = useState("");
  const [formErrors, setFormErrors] = useState<Record<string, string[]>>({});

  const handleFormSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setFormStatus("loading");
    setFormErrors({});
    setFormMessage("");

    try {
      const res = await fetch(`${API_BASE}/api/v1/request-key`, {
        method: "POST",
        headers: { "Content-Type": "application/json", Accept: "application/json" },
        body: JSON.stringify(formData),
      });
      const data = await res.json();

      if (res.ok) {
        setFormStatus("success");
        setFormMessage(data.message);
        setFormData({ name: "", email: "", organisation: "", use_case: "" });
      } else {
        setFormStatus("error");
        setFormMessage(data.message || "Something went wrong. Please try again.");
        if (data.errors) setFormErrors(data.errors);
      }
    } catch {
      setFormStatus("error");
      setFormMessage("Network error. Please check your connection and try again.");
    }
  };

  // ── Check request status ──
  const [checkEmail, setCheckEmail] = useState("");
  const [checkResult, setCheckResult] = useState<null | { request_status: string; submitted_at: string; reviewed_at: string | null }>(null);
  const [checkLoading, setCheckLoading] = useState(false);
  const [checkError, setCheckError] = useState("");

  const handleCheckStatus = async (e: React.FormEvent) => {
    e.preventDefault();
    setCheckLoading(true);
    setCheckResult(null);
    setCheckError("");
    try {
      const res = await fetch(`${API_BASE}/api/v1/request-key/status?email=${encodeURIComponent(checkEmail)}`);
      const data = await res.json();
      if (res.ok) {
        setCheckResult(data.data);
      } else {
        setCheckError(data.message || "Not found.");
      }
    } catch {
      setCheckError("Network error.");
    }
    setCheckLoading(false);
  };

  const curlExample = `curl -X GET "${API_BASE}/api/v1/facilities?state_id=25&per_page=10" \\
  -H "X-API-Key: hfr_your_api_key_here" \\
  -H "Accept: application/json"`;

  const pythonExample = `import requests

API_KEY = "hfr_your_api_key_here"
BASE_URL = "${API_BASE}/api/v1"

headers = {
    "X-API-Key": API_KEY,
    "Accept": "application/json"
}

# Search facilities in Lagos
response = requests.get(f"{BASE_URL}/facilities", headers=headers, params={
    "state_id": 25,
    "per_page": 10,
    "search": "General Hospital"
})

data = response.json()
for facility in data["data"]["facilities"]:
    print(f'{facility["facility_name"]} — {facility["location"]["state"]["name"]}')`;

  const jsExample = `const API_KEY = "hfr_your_api_key_here";
const BASE_URL = "${API_BASE}/api/v1";

async function searchFacilities(stateId, search = "") {
  const params = new URLSearchParams({
    state_id: stateId,
    per_page: "10",
    ...(search && { search }),
  });

  const response = await fetch(\`\${BASE_URL}/facilities?\${params}\`, {
    headers: {
      "X-API-Key": API_KEY,
      "Accept": "application/json",
    },
  });

  if (!response.ok) {
    const error = await response.json();
    throw new Error(error.message);
  }

  return response.json();
}

// Usage
const data = await searchFacilities(25, "General Hospital");
console.log(\`Found \${data.meta.total} facilities\`);`;

  const phpExample = `<?php
$apiKey = "hfr_your_api_key_here";
$baseUrl = "${API_BASE}/api/v1";

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => "$baseUrl/facilities?state_id=25&per_page=10",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "X-API-Key: $apiKey",
        "Accept: application/json",
    ],
]);

$response = curl_exec($ch);
$data = json_decode($response, true);

foreach ($data['data']['facilities'] as $facility) {
    echo $facility['facility_name'] . " — " . $facility['location']['state']['name'] . "\\n";
}`;

  return (
    <div className="min-h-screen bg-white">
      {/* Hero */}
      <section className="px-4 py-16 text-white bg-gradient-to-br from-green-700 to-green-900">
        <div className="max-w-6xl mx-auto mt-8 text-center">
          <h1 className="mb-4 text-4xl font-bold md:text-5xl">HFR API Documentation</h1>
          <p className="max-w-3xl mx-auto text-lg text-green-100 md:text-xl">
            Access Nigeria&apos;s Health Facility Registry data programmatically. Build integrations,
            dashboards, and applications with our RESTful API.
          </p>
          <div className="flex flex-wrap justify-center gap-4 mt-8">
            <button
              onClick={() => setActiveSection("request-key")}
              className="bg-white text-green-800 px-6 py-2.5 rounded-lg font-semibold hover:bg-green-50 transition-colors"
            >
              <FiKey className="inline-block w-4 h-4 mr-2 -mt-0.5" />
              Request API Key
            </button>
            <code className="px-4 py-2 font-mono text-sm rounded-lg bg-green-800/50">
              Base URL: {API_BASE}/api/v1
            </code>
            <span className="px-4 py-2 text-sm font-semibold bg-green-600 rounded-lg">
              Current Version: v1
            </span>
          </div>
        </div>
      </section>

      {/* Content */}
      <div className="px-4 py-12 mx-auto max-w-7xl">
        <div className="flex flex-col gap-8 lg:flex-row">
          {/* Sidebar */}
          <aside className="lg:w-56 shrink-0">
            <div className="sticky top-24">
              <SideNav active={activeSection} onChange={setActiveSection} />
            </div>
          </aside>

          {/* Main */}
          <main className="flex-1 min-w-0 space-y-12">
            {/* Overview */}
            {activeSection === "overview" && (
              <section id="overview">
                <h2 className="mb-4 text-2xl font-bold">Overview</h2>
                <div className="space-y-4 prose text-gray-700 max-w-none">
                  <p>
                    The <strong>HFR External API</strong> provides read-only access to Nigeria&apos;s national
                    Health Facility Registry. It covers <strong>hospitals &amp; clinics</strong>,{" "}
                    <strong>pharmacies</strong>, <strong>laboratories</strong>, and{" "}
                    <strong>imaging/radiology</strong> premises across all 36 states and the FCT.
                  </p>
                  <div className="grid grid-cols-1 gap-4 md:grid-cols-3 not-prose">
                    {[
                      { label: "All Endpoints", value: "GET only (read-only)" },
                      { label: "Data Format", value: "JSON" },
                      { label: "Authentication", value: "API Key (X-API-Key header)" },
                    ].map((item) => (
                      <div key={item.label} className="p-4 border rounded-lg bg-gray-50">
                        <div className="text-sm text-gray-500">{item.label}</div>
                        <div className="font-semibold text-gray-800">{item.value}</div>
                      </div>
                    ))}
                  </div>
                  <h3 className="mt-6 text-lg font-semibold">Quick Start</h3>
                  <ol className="space-y-2 list-decimal list-inside">
                    <li>
                      <button onClick={() => setActiveSection("request-key")} className="font-medium text-green-700 underline hover:text-green-900">
                        Request an API key
                      </button>{" "}
                      using the form — approval is typically within 24 hours
                    </li>
                    <li>Include your key in the <code className="px-1 bg-gray-100 rounded">X-API-Key</code> header</li>
                    <li>Make GET requests to <code className="px-1 bg-gray-100 rounded">{API_BASE}/api/v1/facilities</code></li>
                    <li>Parse the standardized JSON response</li>
                  </ol>
                </div>
              </section>
            )}

            {/* Request API Key */}
            {activeSection === "request-key" && (
              <section id="request-key">
                <h2 className="flex items-center gap-2 mb-4 text-2xl font-bold">
                  <FiSend /> Request an API Key
                </h2>
                <p className="mb-6 text-gray-600">
                  Fill out the form below to request access to the HFR API. Once reviewed by an
                  administrator, your API key will be sent to your email address. Approval typically
                  takes less than 24 hours.
                </p>

                {formStatus === "success" ? (
                  <div className="p-6 text-center border border-green-200 rounded-lg bg-green-50">
                    <FiCheck className="w-12 h-12 mx-auto mb-3 text-green-500" />
                    <h3 className="mb-2 text-lg font-semibold text-green-800">Request Submitted!</h3>
                    <p className="text-green-700">{formMessage}</p>
                    <button
                      onClick={() => setFormStatus("idle")}
                      className="mt-4 text-sm text-green-600 underline hover:text-green-800"
                    >
                      Submit another request
                    </button>
                  </div>
                ) : (
                  <form onSubmit={handleFormSubmit} className="max-w-xl space-y-4">
                    {formStatus === "error" && formMessage && (
                      <div className="p-4 text-sm text-red-700 border border-red-200 rounded-lg bg-red-50">
                        <FiAlertTriangle className="inline w-4 h-4 mr-1 -mt-0.5" />
                        {formMessage}
                      </div>
                    )}

                    <div>
                      <label className="block mb-1 text-sm font-medium text-gray-700">Full Name *</label>
                      <input
                        type="text"
                        required
                        value={formData.name}
                        onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                        className="w-full px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        placeholder="John Doe"
                      />
                      {formErrors.name && <p className="mt-1 text-xs text-red-500">{formErrors.name[0]}</p>}
                    </div>

                    <div>
                      <label className="block mb-1 text-sm font-medium text-gray-700">Email Address *</label>
                      <input
                        type="email"
                        required
                        value={formData.email}
                        onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                        className="w-full px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        placeholder="john@organisation.com"
                      />
                      {formErrors.email && <p className="mt-1 text-xs text-red-500">{formErrors.email[0]}</p>}
                    </div>

                    <div>
                      <label className="block mb-1 text-sm font-medium text-gray-700">Organisation</label>
                      <input
                        type="text"
                        value={formData.organisation}
                        onChange={(e) => setFormData({ ...formData, organisation: e.target.value })}
                        className="w-full px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        placeholder="e.g. Federal Ministry of Health"
                      />
                    </div>

                    <div>
                      <label className="block mb-1 text-sm font-medium text-gray-700">
                        Use Case / Purpose *
                      </label>
                      <textarea
                        required
                        minLength={20}
                        rows={4}
                        value={formData.use_case}
                        onChange={(e) => setFormData({ ...formData, use_case: e.target.value })}
                        className="w-full px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        placeholder="Describe how you plan to use the HFR API, what application you are building, and the expected request volume..."
                      />
                      <p className="mt-1 text-xs text-gray-400">Minimum 20 characters</p>
                      {formErrors.use_case && <p className="mt-1 text-xs text-red-500">{formErrors.use_case[0]}</p>}
                    </div>

                    <button
                      type="submit"
                      disabled={formStatus === "loading"}
                      className="bg-green-700 text-white px-6 py-2.5 rounded-lg font-medium hover:bg-green-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                      {formStatus === "loading" ? (
                        <>
                          <svg className="w-4 h-4 animate-spin" viewBox="0 0 24 24"><circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" fill="none" /><path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" /></svg>
                          Submitting...
                        </>
                      ) : (
                        <>
                          <FiSend className="w-4 h-4" /> Submit Request
                        </>
                      )}
                    </button>
                  </form>
                )}

                {/* Check Status */}
                <div className="pt-8 mt-10 border-t">
                  <h3 className="flex items-center gap-2 mb-2 text-lg font-semibold">
                    <FiMail /> Already submitted? Check your request status
                  </h3>
                  <form onSubmit={handleCheckStatus} className="flex max-w-md gap-2">
                    <input
                      type="email"
                      required
                      value={checkEmail}
                      onChange={(e) => setCheckEmail(e.target.value)}
                      className="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                      placeholder="Enter your email"
                    />
                    <button
                      type="submit"
                      disabled={checkLoading}
                      className="px-4 py-2 text-sm text-white transition-colors bg-gray-800 rounded-lg hover:bg-gray-900 disabled:opacity-50"
                    >
                      {checkLoading ? "Checking..." : "Check"}
                    </button>
                  </form>
                  {checkError && <p className="mt-2 text-sm text-red-500">{checkError}</p>}
                  {checkResult && (
                    <div className="max-w-md p-4 mt-3 border rounded-lg bg-gray-50">
                      <div className="flex items-center gap-2 mb-1">
                        <span className="font-medium">Status:</span>
                        <span className={`inline-block px-2 py-0.5 rounded text-xs font-bold ${
                          checkResult.request_status === "approved" ? "bg-green-100 text-green-800" :
                          checkResult.request_status === "pending" ? "bg-yellow-100 text-yellow-800" :
                          "bg-red-100 text-red-800"
                        }`}>
                          {checkResult.request_status.toUpperCase()}
                        </span>
                      </div>
                      <p className="text-sm text-gray-500">
                        Submitted: {new Date(checkResult.submitted_at).toLocaleDateString()}
                        {checkResult.reviewed_at && <> · Reviewed: {new Date(checkResult.reviewed_at).toLocaleDateString()}</>}
                      </p>
                    </div>
                  )}
                </div>
              </section>
            )}

            {/* Authentication */}
            {activeSection === "authentication" && (
              <section id="authentication">
                <h2 className="mb-4 text-2xl font-bold">Authentication</h2>
                <div className="space-y-4 prose text-gray-700 max-w-none">
                  <p>
                    All API endpoints (except the health check at <code>/api/v1/</code>) require authentication
                    via an <strong>API key</strong>. Include your key in every request using the{" "}
                    <code>X-API-Key</code> HTTP header.
                  </p>
                  <div className="relative not-prose">
                    <CopyButton text='curl -H "X-API-Key: hfr_your_api_key_here" https://api.example.com/api/v1/facilities' />
                    <pre className="p-4 overflow-x-auto text-sm text-green-300 bg-gray-900 rounded-lg">
                    {`GET /api/v1/facilities HTTP/1.1
                    Host: ${API_BASE.replace("https://", "")}
                    X-API-Key: hfr_your_api_key_here
                    Accept: application/json`}
                    </pre>
                  </div>

                  <h3 className="text-lg font-semibold">Error Responses</h3>
                  <div className="overflow-x-auto not-prose">
                    <table className="w-full text-sm border">
                      <thead>
                        <tr className="bg-gray-50">
                          <th className="p-2 text-left border">Status</th>
                          <th className="p-2 text-left border">Code</th>
                          <th className="p-2 text-left border">Description</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr><td className="p-2 border">401</td><td className="p-2 border"><code>MISSING_API_KEY</code></td><td className="p-2 border">No X-API-Key header provided</td></tr>
                        <tr><td className="p-2 border">401</td><td className="p-2 border"><code>INVALID_API_KEY</code></td><td className="p-2 border">The API key is not recognized</td></tr>
                        <tr><td className="p-2 border">403</td><td className="p-2 border"><code>API_KEY_INACTIVE</code></td><td className="p-2 border">Key has been deactivated</td></tr>
                        <tr><td className="p-2 border">403</td><td className="p-2 border"><code>API_KEY_EXPIRED</code></td><td className="p-2 border">Key has passed its expiry date</td></tr>
                      </tbody>
                    </table>
                  </div>

                  <div className="p-4 border-l-4 border-yellow-400 bg-yellow-50 not-prose">
                    <div className="flex items-start gap-2">
                      <FiAlertTriangle className="w-5 h-5 text-yellow-600 mt-0.5" />
                      <div>
                        <p className="font-semibold text-yellow-800">Keep your API key secure</p>
                        <p className="text-sm text-yellow-700">
                          Never expose your key in client-side code, public repositories, or URLs.
                          If compromised, contact the administrator to regenerate it.
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </section>
            )}

            {/* Rate Limiting */}
            {activeSection === "rate-limiting" && (
              <section id="rate-limiting">
                <h2 className="mb-4 text-2xl font-bold">Rate Limiting</h2>
                <div className="space-y-4 prose text-gray-700 max-w-none">
                  <p>
                    Each API key has a per-minute rate limit (default: <strong>60 requests/minute</strong>).
                    Your specific limit is set by the HFR administrator and can be viewed in the response headers.
                  </p>
                  <h3 className="text-lg font-semibold">Response Headers</h3>
                  <div className="not-prose">
                    <pre className="p-4 text-sm text-green-300 bg-gray-900 rounded-lg">
{`X-RateLimit-Limit: 60
X-API-Version: v1`}
                    </pre>
                  </div>
                  <p>
                    When you exceed your limit, you&apos;ll receive a <code>429 Too Many Requests</code> response:
                  </p>
                  <div className="not-prose">
                    <pre className="p-4 text-sm text-red-300 bg-gray-900 rounded-lg">
{`{
  "status": "error",
  "message": "Rate limit exceeded. Maximum 60 requests per minute.",
  "code": "RATE_LIMIT_EXCEEDED"
}`}
                    </pre>
                  </div>
                  <h3 className="text-lg font-semibold">Best Practices</h3>
                  <ul className="space-y-1 list-disc list-inside">
                    <li>Cache responses for lookup/reference data (states, LGAs, etc.)</li>
                    <li>Use pagination with reasonable page sizes (25–50)</li>
                    <li>Implement exponential back-off on 429 responses</li>
                    <li>Request a higher limit if your use case requires it</li>
                  </ul>
                </div>
              </section>
            )}

            {/* Endpoints */}
            {activeSection === "endpoints" && (
              <section id="endpoints">
                <h2 className="mb-4 text-2xl font-bold">API Endpoints</h2>
                <p className="mb-6 text-gray-600">
                  All endpoints are read-only (GET) and return JSON. Responses follow a standardized format.
                </p>
                <div className="space-y-8">
                  {endpointGroups.map((group) => (
                    <div key={group.title}>
                      <h3 className="flex items-center gap-2 mb-3 text-lg font-semibold">
                        {group.icon}
                        {group.title}
                      </h3>
                      <div className="space-y-2">
                        {group.endpoints.map((ep) => (
                          <EndpointCard key={ep.path} endpoint={ep} />
                        ))}
                      </div>
                    </div>
                  ))}
                </div>
              </section>
            )}

            {/* Error Handling */}
            {activeSection === "errors" && (
              <section id="errors">
                <h2 className="mb-4 text-2xl font-bold">Error Handling</h2>
                <div className="space-y-4 prose text-gray-700 max-w-none">
                  <p>All error responses follow a consistent JSON structure:</p>
                  <div className="not-prose">
                    <pre className="p-4 text-sm text-red-300 bg-gray-900 rounded-lg">
{`{
  "status": "error",
  "message": "Human-readable error description",
  "code": "MACHINE_READABLE_ERROR_CODE",
  "errors": { }
}`}
                    </pre>
                  </div>
                  <h3 className="text-lg font-semibold">HTTP Status Codes</h3>
                  <div className="overflow-x-auto not-prose">
                    <table className="w-full text-sm border">
                      <thead>
                        <tr className="bg-gray-50">
                          <th className="p-2 text-left border">Code</th>
                          <th className="p-2 text-left border">Meaning</th>
                          <th className="p-2 text-left border">Error Code</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr><td className="p-2 border">200</td><td className="p-2 border">Success</td><td className="p-2 border">—</td></tr>
                        <tr><td className="p-2 border">401</td><td className="p-2 border">Unauthorized — invalid or missing API key</td><td className="p-2 border"><code>MISSING_API_KEY</code>, <code>INVALID_API_KEY</code></td></tr>
                        <tr><td className="p-2 border">403</td><td className="p-2 border">Forbidden — key inactive or expired</td><td className="p-2 border"><code>API_KEY_INACTIVE</code>, <code>API_KEY_EXPIRED</code></td></tr>
                        <tr><td className="p-2 border">404</td><td className="p-2 border">Resource not found</td><td className="p-2 border"><code>NOT_FOUND</code></td></tr>
                        <tr><td className="p-2 border">405</td><td className="p-2 border">Method not allowed</td><td className="p-2 border"><code>METHOD_NOT_ALLOWED</code></td></tr>
                        <tr><td className="p-2 border">422</td><td className="p-2 border">Validation error</td><td className="p-2 border"><code>VALIDATION_ERROR</code></td></tr>
                        <tr><td className="p-2 border">429</td><td className="p-2 border">Rate limit exceeded</td><td className="p-2 border"><code>RATE_LIMIT_EXCEEDED</code></td></tr>
                        <tr><td className="p-2 border">500</td><td className="p-2 border">Server error</td><td className="p-2 border"><code>SERVER_ERROR</code></td></tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </section>
            )}

            {/* Code Examples */}
            {activeSection === "examples" && (
              <section id="examples">
                <h2 className="mb-4 text-2xl font-bold">Code Examples</h2>
                <div className="space-y-6">
                  {[
                    { label: "cURL", code: curlExample, lang: "bash" },
                    { label: "Python", code: pythonExample, lang: "python" },
                    { label: "JavaScript / TypeScript", code: jsExample, lang: "javascript" },
                    { label: "PHP", code: phpExample, lang: "php" },
                  ].map((ex) => (
                    <div key={ex.label}>
                      <h3 className="mb-2 font-semibold">{ex.label}</h3>
                      <div className="relative">
                        <CopyButton text={ex.code} />
                        <pre className="p-4 overflow-x-auto text-sm leading-relaxed text-green-300 bg-gray-900 rounded-lg">
                          {ex.code}
                        </pre>
                      </div>
                    </div>
                  ))}
                </div>
              </section>
            )}

            {/* Versioning */}
            {activeSection === "versioning" && (
              <section id="versioning">
                <h2 className="mb-4 text-2xl font-bold">Versioning &amp; Deprecation</h2>
                <div className="space-y-4 prose text-gray-700 max-w-none">
                  <p>
                    The API uses <strong>URL-based versioning</strong>. The current version is{" "}
                    <code>v1</code>, accessed at <code>/api/v1/</code>.
                  </p>
                  <h3 className="text-lg font-semibold">Version Policy</h3>
                  <ul className="space-y-2 list-disc list-inside">
                    <li>New versions are released under a new prefix (e.g., <code>/api/v2/</code>)</li>
                    <li>Old versions receive a <strong>6-month sunset period</strong> after a new version is released</li>
                    <li>During the sunset period, deprecated versions return a <code>Sunset</code> HTTP header</li>
                    <li>After the sunset date, the old version is removed and returns <code>410 Gone</code></li>
                  </ul>
                  <h3 className="text-lg font-semibold">Response Headers</h3>
                  <div className="not-prose">
                    <pre className="p-4 text-sm text-green-300 bg-gray-900 rounded-lg">
{`X-API-Version: v1

# When a version is deprecated:
Sunset: Sat, 01 Jan 2028 00:00:00 GMT
Deprecation: true`}
                    </pre>
                  </div>
                  <h3 className="text-lg font-semibold">Changelog</h3>
                  <div className="overflow-hidden border rounded-lg not-prose">
                    <table className="w-full text-sm">
                      <thead>
                        <tr className="bg-gray-50">
                          <th className="p-3 text-left border-b">Date</th>
                          <th className="p-3 text-left border-b">Version</th>
                          <th className="p-3 text-left border-b">Changes</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td className="p-3 border-b">2026-02-14</td>
                          <td className="p-3 border-b"><code>v1</code></td>
                          <td className="p-3 border-b">
                            Initial release — Facilities, Pharmacies, Laboratories, Imaging,
                            Lookups, Statistics. API key authentication, per-client rate limiting,
                            request logging.
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </section>
            )}
          </main>
        </div>
      </div>
    </div>
  );
}
