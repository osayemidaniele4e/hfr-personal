import _ from "lodash";

import Input from "@/components/ui/Input";
import SelectComponent from "@/components/ui/SelectComponent";
import { GreenButton, Text } from "@/components/ui/Typography";
import React, { useCallback, useEffect, useState } from "react";
import axios from "axios";

import dynamic from "next/dynamic";
const RadiologyTable = dynamic(() => import("./RadiologyTable"), {
  ssr: false,
});

const RadiologyTab = () => {
  const [states, setStates] = useState<any[]>([]); // Store states
  const [lgas, setLgas] = useState<any[]>([]); // Store LGAs
  const [wards, setWards] = useState<any[]>([]); // Store wards
  const [facilityLevels, setFacilityLevel] = useState<any[]>([]);
  const [ownerships, setOwnership] = useState<any[]>([]);
  const [operationals, setOperational] = useState<any[]>([]);
  const [registrations, setRegistration] = useState<any[]>([]);
  const [licenses, setLicense] = useState<any[]>([]);
  const [serviceCategories, setServiceCategory] = useState<any[]>([]);
  const [services, setService] = useState<any[]>([]);

  const [selectedState, setSelectedState] = useState(""); // Selected state
  const [selectedLga, setSelectedLga] = useState(""); // Selected LGA
  const [selectedWard, setSelectedWard] = useState("");
  const [selectedFacilityLevel, setSelectedFacilityLevel] = useState("");
  const [selectedownership, setSelectedownership] = useState("");
  const [selectedOperational, setSelectedOperational] = useState("");
  const [selectedRegistration, setSelectedRegistration] = useState("");
  const [selectedLicense, setSelectedLicense] = useState("");

  const [selectedServiceCategory, setSelectedServiceCategory] = useState("");
  const [selectedService, setSelectedService] = useState("");

  const [selectedGeoCode, setSelectedGeoCode] = useState("0");
  const [selectedServiceType, setSelectedServiceType] = useState("0");

  const [fetchError, setFetchError] = useState<string>(""); // State for error messages
  const [loading, setLoading] = useState<boolean>(true); // Loading state

  const [data, setData] = useState<any[]>([]); // Store API response
  const [currentPage, setCurrentPage] = useState(1); // Track pagination
  const [totalPages, setTotalPages] = useState(1); // Store total pages

  const [search, setSearch] = useState("");
  const [filteredData, setFilteredData] = useState(data);

  const [entriesPerPage, setEntriesPerPage] = useState(10);

  const fetchFacilities4 = useCallback(async () => {
    setLoading(true);
    setFetchError(""); // Reset errors before fetching
    try {
      const response = await axios.post(
        `${process.env.NEXT_PUBLIC_BACKEND_API}/facilities/imaging-list`,
        {
          state_id: selectedState,
          lga_id: selectedLga,
          ward_id: selectedWard,
          // facility_level_id: selectedFacilityLevel,
          ownership_id: selectedownership,
          operational_status_id: selectedOperational,
          registration_status_id: selectedRegistration,
          license_status_id: selectedLicense,
          // outpatient: selectedServiceType ? 1 : 0,
          // inpatient: selectedServiceType ? 1 : 0,
          // facility_name: searchQuery,
          page: currentPage,
        }
      );

      // console.log("response by adams", response.data.data.facilities);

      setData(response.data?.data?.facilities?.data); // Laravel pagination response (data array)
      setTotalPages(response.data?.data?.facilities?.last_page); // Set total pages from API
      // console.log("response by adams", response.data.data.facilities.last_page);
    } catch (error) {
      console.error("Error fetching data:", error);
    }
    setLoading(false);
  }, [
    currentPage,
    selectedState,
    selectedLga,
    selectedWard,
    selectedownership,
    selectedOperational,
    selectedRegistration,
    selectedLicense,
  ]);

  // Fetch states
  const fetchStates4 = useCallback(async () => {
    try {
      const response = await axios.get(
        `${process.env.NEXT_PUBLIC_BACKEND_API}/states`
      );
      const data = response?.data?.data; // Axios automatically parses JSON

      // console.log("response", data);

      if (data && Array.isArray(data)) {
        setStates(data);
      }
    } catch (error) {
      console.error("Error fetching states:", error);
      setFetchError("Failed to fetch states.");
    } finally {
      setLoading(false);
    }
  }, []);

  // Fetch LGAs based on selected state
  const fetchLgas = useCallback(async (stateId: string) => {
    try {
      setLgas([]); // Reset LGAs
      setWards([]); // Reset wards
      setSelectedLga(""); // Reset selected LGA

      const response = await axios.post(
        `${process.env.NEXT_PUBLIC_BACKEND_API}/lgas-by-state`,
        { state_id: stateId },
        { headers: { "Content-Type": "application/json" } }
      );

      const data = response?.data?.data;

      if (data && Array.isArray(data)) {
        setLgas(data);
      }
    } catch (error) {
      console.error("Error fetching LGAs:", error);
      setFetchError("Failed to fetch LGAs.");
    }
  }, []);

  // Fetch wards based on selected LGA
  const fetchWards = useCallback(async (lgaId: string) => {
    try {
      setWards([]); // Reset wards

      const response = await axios.post(
        `${process.env.NEXT_PUBLIC_BACKEND_API}/ward-by-lga`,
        { lga_id: lgaId },
        { headers: { "Content-Type": "application/json" } }
      );
      const data = response?.data?.data;

      if (data && Array.isArray(data)) {
        setWards(data);
      }
    } catch (error) {
      console.error("Error fetching wards:", error);
      setFetchError("Failed to fetch wards.");
    }
  }, []);

  // Fetch facilityLevel
  const facilityLevel = useCallback(async () => {
    try {
      const response = await axios.get(
        `${process.env.NEXT_PUBLIC_BACKEND_API}/facility-level`
      );
      const data = response?.data?.data; // Axios automatically parses JSON

      // console.log("response", data);

      if (data && Array.isArray(data)) {
        setFacilityLevel(data);
      }
    } catch (error) {
      console.error("Error fetching facility-level:", error);
      setFetchError("Failed to fetch facility-level.");
    } finally {
      setLoading(false);
    }
  }, []);

  // Fetch ownership
  const ownership4 = useCallback(async () => {
    try {
      const response = await axios.get(
        `${process.env.NEXT_PUBLIC_BACKEND_API}/ownership`
      );
      const data = response?.data?.data; // Axios automatically parses JSON

      // console.log("response", data);

      if (data && Array.isArray(data)) {
        setOwnership(data);
      }
    } catch (error) {
      console.error("Error fetching ownership:", error);
      setFetchError("Failed to fetch ownership.");
    } finally {
      setLoading(false);
    }
  }, []);

  // Fetch operational
  const operational4 = useCallback(async () => {
    try {
      const response = await axios.get(
        `${process.env.NEXT_PUBLIC_BACKEND_API}/operational-status`
      );
      const data = response?.data?.data; // Axios automatically parses JSON

      // console.log("response", data);

      if (data && Array.isArray(data)) {
        setOperational(data);
      }
    } catch (error) {
      console.error("Error fetching operational:", error);
      // setFetchError("Failed to fetch operational.");
    } finally {
      setLoading(false);
    }
  }, []);

  // Fetch registration status
  const registrationStatus4 = useCallback(async () => {
    try {
      const response = await axios.get(
        `${process.env.NEXT_PUBLIC_BACKEND_API}/registration-status`
      );
      const data = response?.data?.data; // Axios automatically parses JSON

      // console.log("response", data);

      if (data && Array.isArray(data)) {
        setRegistration(data);
      }
    } catch (error) {
      console.error("Error fetching operational:", error);
      // setFetchError("Failed to fetch operational.");
    } finally {
      setLoading(false);
    }
  }, []);

  // Fetch license status
  const license4 = useCallback(async () => {
    try {
      const response = await axios.get(
        `${process.env.NEXT_PUBLIC_BACKEND_API}/license-status`
      );
      const data = response?.data?.data; // Axios automatically parses JSON

      // console.log("response", data);

      if (data && Array.isArray(data)) {
        setLicense(data);
      }
    } catch (error) {
      console.error("Error fetching operational:", error);
      setFetchError("Failed to fetch operational.");
    } finally {
      setLoading(false);
    }
  }, []);

  // Fetch service-category
  const serviceCategory4 = useCallback(async () => {
    try {
      const response = await axios.get(
        `${process.env.NEXT_PUBLIC_BACKEND_API}/service-category`
      );
      const data = response?.data?.data; // Axios automatically parses JSON

      // console.log("response", data);

      if (data && Array.isArray(data)) {
        setServiceCategory(data);
      }
    } catch (error) {
      console.error("Error fetching operational:", error);
      setFetchError("Failed to fetch operational.");
    } finally {
      setLoading(false);
    }
  }, []);

  const fetchService = async (serviceId: string) => {
    try {
      setService([]); // Reset LGAs
      setSelectedService(""); // Reset selected LGA

      const response = await axios.post(
        `${process.env.NEXT_PUBLIC_BACKEND_API}/services-by-category`,
        { service_category_id: serviceId },
        { headers: { "Content-Type": "application/json" } }
      );

      const data = response?.data?.data;

      if (data && Array.isArray(data)) {
        setService(data);
      }
    } catch (error) {
      console.error("Error fetching LGAs:", error);
      setFetchError("Failed to fetch LGAs.");
    }
  };

  const handleReset = () => {
    setSelectedState("");
    setSelectedLga("");
    setSelectedWard("");
    setSelectedFacilityLevel("");
    setSelectedownership("");
    setSelectedOperational("");
    setSelectedRegistration("");
    setSelectedLicense("");

    setSelectedServiceCategory("");
    setSelectedService("");
    setSelectedGeoCode("");
    setSelectedServiceType("");
    setSearch("");
  };

  useEffect(() => {
    fetchStates4();
    // facilityLevel();
    ownership4();
    operational4();
    registrationStatus4();
    license4();
    serviceCategory4();
  }, [
    fetchStates4,
    ownership4,
    operational4,
    registrationStatus4,
    license4,
    serviceCategory4,
  ]); // Runs only once when the component mounts

  // Fetch LGAs when `states` change
  useEffect(() => {
    if (selectedState) {
      fetchLgas(selectedState);
    }
  }, [selectedState, fetchLgas]); // Runs when `selectedState` changes

  // Fetch Wards when `lgas` change
  useEffect(() => {
    if (selectedLga) {
      fetchWards(selectedLga);
    }
  }, [selectedLga, fetchWards]); // Runs when `selectedLga` changes

  // Fetch paginated facilities when `currentPage` changes
  useEffect(() => {
    if (currentPage) {
      fetchFacilities4();
    }
  }, [currentPage, fetchFacilities4]); // Runs when `currentPage` changes

  const fetchFacilities34 = useCallback(async () => {
    setLoading(true);
    setFetchError("");

    try {
      const response = await axios.post(
        `${process.env.NEXT_PUBLIC_BACKEND_API}/facilities/imaging-list`,
        {
          facility_name: search, // Include facility name search
          per_page: entriesPerPage, // Send number of entries per page
          page: currentPage,
        }
      );

      setData(response.data?.data?.facilities?.data);
      setTotalPages(response.data?.data?.facilities?.last_page);
    } catch (error) {
      setFetchError("Failed to fetch hospitals");
      console.error("Error fetching hospitals:", error);
    }

    setLoading(false);
  }, [search, entriesPerPage, currentPage]);

  const [isOpen, setIsOpen] = useState(true); // Accordion open by default

  return (
    <div>
      {/* <Text className="text-2xl pt-5 pb-5">Radiologies and Imagings</Text> */}
      <div className="border border-gray-300 rounded-lg shadow w-full mb-6">
        {/* Accordion Header */}
        <button
          onClick={() => setIsOpen(!isOpen)}
          className="flex items-center justify-between w-full px-4 py-3 bg-green-100 text-green-700 font-semibold rounded-t"
        >
          <span>
            {" "}
            <Text className="text-2xl pt-1 pb-1">Radiologies and Imagings</Text>
          </span>
          <svg
            className={`w-5 h-5 transition-transform duration-300 ${
              isOpen ? "rotate-180" : ""
            }`}
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              strokeLinecap="round"
              strokeLinejoin="round"
              strokeWidth={2}
              d="M19 9l-7 7-7-7"
            />
          </svg>
        </button>
        {isOpen && (
          <div className="p-4 border-t border-gray-200">
            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 gap-4 w-full">
              {/* State Selection */}
              <SelectComponent
                className="w-full max-w-[350px]"
                value={selectedState}
                onChange={(e) => {
                  const selectedId = e.target.value; // Get the state ID
                  setSelectedState(selectedId);
                  fetchLgas(selectedId);
                }}
                options={states.map((state) => ({
                  value: state.id, // Ensure value is the ID
                  name: state.name, // Display name
                }))}
                placeholder="All State"
              />

              {/* lga Selection */}
              <SelectComponent
                className="w-full max-w-[350px]"
                value={selectedLga}
                onChange={(e) => {
                  const lgaId = e.target.value;
                  setSelectedLga(lgaId);
                  fetchWards(lgaId); // Fetch Wards for selected LGA
                }}
                options={lgas.map((lga) => ({
                  value: lga.id, // Use LGA ID
                  name: lga.name, // Show LGA name
                }))}
                placeholder="Select LGA"
                disabled={!selectedState} // Disable until State is selected
              />

              {/* ward Selection */}
              <SelectComponent
                className="w-full max-w-[350px]"
                value={selectedWard}
                onChange={(e) => {
                  setSelectedWard(e.target.value); // Store selected Ward
                }}
                options={wards.map((ward) => ({
                  value: ward.id, // Use Ward ID
                  name: ward.name, // Show Ward name
                }))}
                placeholder="Select Ward"
                disabled={!selectedLga} // Disable until LGA is selected
              />

              {/* ownership Selection */}
              <SelectComponent
                className="w-full max-w-[350px]"
                value={selectedownership} // Track selected value
                onChange={(e) => {
                  setSelectedownership(e.target.value); // Update state
                }}
                options={ownerships.map((items) => ({
                  value: items.id, // Use items ID
                  name: items.name, // Show items name
                }))}
                placeholder="Select Ownership"
              />

              {/* operational Selection */}
              <SelectComponent
                className="w-full max-w-[350px]"
                value={selectedOperational} // Track selected value
                onChange={(e) => {
                  setSelectedOperational(e.target.value); // Update state
                }}
                options={operationals.map((items) => ({
                  value: items.id, // Use items ID
                  name: items.status, // Show items name
                }))}
                placeholder="Select Operational"
              />

              {/* Registration status Selection */}
              <SelectComponent
                className="w-full max-w-[350px]"
                value={selectedRegistration} // Track selected value
                onChange={(e) => {
                  setSelectedRegistration(e.target.value); // Update state
                }}
                options={registrations.map((items) => ({
                  value: items.id, // Use items ID
                  name: items.status, // Show items name
                }))}
                placeholder="Select Registration status"
              />

              {/* licenses */}
              <SelectComponent
                className="w-full max-w-[350px]"
                value={selectedLicense} // Track selected value
                onChange={(e) => {
                  setSelectedLicense(e.target.value); // Update state
                }}
                options={licenses.map((items) => ({
                  value: items.id, // Use items ID
                  name: items.status, // Show items name
                }))}
                placeholder="Select License status"
              />

              {/* Select Coordinates */}
              <SelectComponent
                className="w-full max-w-[350px]"
                value={selectedGeoCode} // Track selected value
                onChange={(e) => {
                  setSelectedGeoCode(e.target.value); // Update state
                }}
                options={[
                  { value: "0", name: "Select Coordinates" },
                  { value: "1", name: "With Coordinates" },
                  { value: "2", name: "With No Coordinates" },
                ]}
                placeholder="Select Coordinates"
              />
            </div>
          </div>
        )}
      </div>

      <div className="flex flex-wrap items-center justify-center gap-4 bg-[#D1D1D1] p-4 mt-4 w-full md:grid md:grid-cols-2 lg:flex lg:gap-6">
        {/* Facility Name Input */}
        <Input
          className="w-full max-w-[700px] h-[55px] text-sm"
          placeholder="Facility Name"
          value={search}
          onChange={(e) => setSearch(e.target.value)}
        />

        {/* Reset Button */}
        <button
          className="bg-[#EFEFEF] rounded-lg w-full max-w-[200px] h-[44px] flex items-center justify-center text-sm"
          onClick={handleReset}
        >
          Reset
        </button>

        {/* Search Button */}
        {/* <GreenButton className="bg-[#5BBA62] w-full max-w-[200px] h-[44px] flex items-center justify-center text-sm">
          Search
        </GreenButton> */}

        <GreenButton
          onClick={fetchFacilities34}
          className="bg-[#5BBA62] w-full max-w-[200px] h-[44px] flex items-center justify-center text-sm"
        >
          {loading ? "Loading..." : "Search"}
        </GreenButton>
      </div>

      {/* <div className="w-full overflow-x-hidden max-w-[100vw] md:overflow-x-auto">
        <HospitalTable />
      </div> */}
      <div className="w-full overflow-x-auto lg:overflow-x-scroll xl:overflow-x-hidden">
        <div className="min-w-[700px] lg:min-w-[900px]">
          <RadiologyTable
            data={data}
            currentPage={currentPage}
            setCurrentPage={setCurrentPage}
            totalPages={totalPages}
            fetchFacilities={fetchFacilities4} // Pass function to child
            // loading={loading}
          />
        </div>
      </div>
    </div>
  );
};

export default RadiologyTab;
