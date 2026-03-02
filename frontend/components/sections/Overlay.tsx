"use client";

import React, { useEffect, useState } from "react";
import axios from "axios";

const Overlay = () => {
  const [facilityTypes, setFacilityTypes] = useState<
    { id: string; name: string }[]
  >([]);
  const [facilityLevels, setFacilityLevels] = useState<
    { id: string; name: string }[]
  >([]);

  const [loading, setLoading] = useState(false);
  const [search, setSearch] = useState("");
  const [selectedFacilityLevel, setSelectedFacilityLevel] = useState("");
  const [selectedFacilityType, setSelectedFacilityType] = useState("");

  useEffect(() => {
    const fetchDropdowns = async () => {
      try {
        const [typesRes, levelsRes] = await Promise.all([
          axios.get(`${process.env.NEXT_PUBLIC_BACKEND_API}/facility-type`),
          axios.get(`${process.env.NEXT_PUBLIC_BACKEND_API}/facility-level`),
        ]);
        const typesData = typesRes?.data?.data;
        const levelsData = levelsRes?.data?.data;
        if (Array.isArray(typesData)) setFacilityTypes(typesData);
        if (Array.isArray(levelsData)) setFacilityLevels(levelsData);
      } catch (err) {
        console.error("Failed to fetch dropdowns:", err);
      }
    };
    fetchDropdowns();
  }, []);

  const doSearch = () => {
    if (loading) return;
    setLoading(true);

    const trimmedSearch = (search || "").trim();

    const query = {
      facilityLevel: selectedFacilityLevel || "",
      facilityType: selectedFacilityType || "",
      search: trimmedSearch,
    };

    // Store search params — let the Facility page do the actual API call
    localStorage.setItem("homePageSearchQuery", JSON.stringify(query));
    // Clear any stale results so the Facility page knows to fetch fresh
    localStorage.removeItem("searchResults");

    const sp = new URLSearchParams();
    if (query.search) sp.set("search", query.search);
    if (query.facilityType) sp.set("facilityType", query.facilityType);
    if (query.facilityLevel) sp.set("facilityLevel", query.facilityLevel);
    const qs = sp.toString();

    // Full page navigation to the facility finder page
    window.location.href = qs
      ? `/facilityfinder?${qs}`
      : "/facilityfinder";
  };

  return (
    <div
      style={{ position: "relative", zIndex: 90, pointerEvents: "auto" }}
      className="w-full bg-[#F5F7FA] mx-auto lg:w-[1200px] rounded-lg mt-[-4rem] py-6 px-4 flex flex-col justify-center items-center"
    >
      <p className="font-semibold text-lg text-center">
        Facility <span className="text-[#5CB85C]">Finder</span>
      </p>

      <p className="text-center text-sm mt-1">
        Search for Health Facilities Close To You
      </p>

      <div className="w-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 mt-4 max-w-[1100px]">
        {/* Search input */}
        <input
          type="text"
          className="w-full p-3 border border-gray-300 rounded bg-white placeholder-gray-400"
          placeholder="Enter Location/Facility Name"
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          onKeyDown={(e) => {
            if (e.key === "Enter") {
              e.preventDefault();
              doSearch();
            }
          }}
          style={{ pointerEvents: "auto" }}
        />

        {/* Facility Type */}
        <select
          className="w-full p-3 border border-gray-300 rounded bg-white"
          value={selectedFacilityType}
          onChange={(e) => setSelectedFacilityType(e.target.value)}
          style={{ pointerEvents: "auto" }}
        >
          <option value="">Select Facility Type</option>
          {facilityTypes.map((type) => (
            <option key={type.id} value={type.id}>
              {type.name}
            </option>
          ))}
        </select>

        {/* Facility Level */}
        <select
          className="w-full p-3 border border-gray-300 rounded bg-white"
          value={selectedFacilityLevel}
          onChange={(e) => setSelectedFacilityLevel(e.target.value)}
          style={{ pointerEvents: "auto" }}
        >
          <option value="">Select Facility Level</option>
          {facilityLevels.map((level) => (
            <option key={level.id} value={level.id}>
              {level.name}
            </option>
          ))}
        </select>

        {/* Search button */}
        <button
          type="button"
          disabled={loading}
          onClick={doSearch}
          style={{ pointerEvents: "auto" }}
          className={`w-full p-3 flex items-center justify-center gap-2 rounded-lg text-white bg-[#326F32] ${
            loading ? "opacity-75 cursor-not-allowed" : "hover:bg-[#28562a] cursor-pointer"
          }`}
        >
          {loading ? (
            <>
              <svg
                className="animate-spin h-5 w-5 text-white"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
              >
                <circle
                  className="opacity-25"
                  cx="12"
                  cy="12"
                  r="10"
                  stroke="currentColor"
                  strokeWidth="4"
                />
                <path
                  className="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                />
              </svg>
              Searching...
            </>
          ) : (
            "Search"
          )}
        </button>
      </div>
    </div>
  );
};

export default Overlay;
