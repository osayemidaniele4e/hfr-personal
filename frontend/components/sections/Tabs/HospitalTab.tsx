import Input from "@/components/ui/Input";
import SelectComponent from "@/components/ui/SelectComponent";
import { GreenButton, Text } from "@/components/ui/Typography";
import React, {
  useCallback,
  useEffect,
  useMemo,
  useRef,
  useState,
} from "react";
// import HospitalTable from "./HospitalTable";
import dynamic from "next/dynamic";
const HospitalTable = dynamic(() => import("./HospitalTable"), { ssr: false });

import axios from "axios";
import SelectComponent3 from "@/components/ui/SelectComponent3";
import SelectComponent4 from "@/components/ui/SelectComponent4";

interface Facility {
  id: number;
  facility_name: string;
  alt_facility_name?: string | null;
  latitude: number | null;
  longitude: number | null;
  state_id?: number;
  state_name?: string;
  lga_id?: number;
  lga_name?: string;
  ward_id?: number;
  ward_name?: string;
  ownership_id?: number;
  ownership_name?: string;
  ownership_type_id?: string | null;
  facility_level_id?: number;
  facility_level_name?: string;
  facility_level_option_id?: number | null;
  facility_level_options_category_id?: number | null;
  license_status_id?: number;
  license_status_name?: string;
  registration_status_id?: number;
  registration_status_name?: string;
  operational_status_id?: number;
  operational_status_name?: string;
  phone_number?: string;
  email_address?: string;
  website?: string | null;
  physical_location?: string | null;
  postal_address?: string | null;
  beds?: number | null;
  doctors?: number | null;
  nurses?: number | null;
  midwifes?: number | null;
  lab_scientists?: number | null;
  lab_technicians?: number | null;
  pharmacists?: number | null;
  pharmacy_technicians?: number | null;
  him_officers?: number | null;
  env_health_officers?: number | null;
  dental_technicians?: number | null;
  dentist?: number | null;
  attendants?: number | null;
  community_health_officer?: number | null;
  community_extension_workers?: number | null;
  jun_community_extension_worker?: number | null;
  inpatient?: string | null;
  outpatient?: string | null;
  ambulance_services?: string;
  onsite_laboratory?: string | null;
  onsite_imaging?: string | null;
  onsite_pharmarcy?: string | null;
  mortuary_services?: string | null;
  operational_days?: string | null;
  operational_hours?: string | null;
  image_url?: string | null;
  start_date?: string | null;
  close_date?: string | null;
  unique_id?: string;
  registration_no?: string | null;
  publish_note?: string | null;
  published_at?: string | null;
  published_by?: string | null;
  request_note?: string | null;
  requested_at?: string | null;
  requested_by?: string | null;
  validate_note?: string | null;
  validated_at?: string | null;
  validated_by?: string | null;
  verify_note?: string | null;
  verified_at?: string | null;
  verified_by?: string | null;
  status_id?: number;
  created_at?: string;
  updated_at?: string;
  created_by?: string | null;
  requested_by_name?: string | null;
  verified_by_name?: string | null;
  validated_by_name?: string | null;
  published_by_name?: string | null;
}

const HospitalTab = () => {
  const [states, setStates] = useState<any[]>([]); // Store states
  const [lgas, setLgas] = useState<any[]>([]); // Store LGAs
  const [wards, setWards] = useState<any[]>([]); // Store wards
  const [facilityLevels, setFacilityLevel] = useState<any[]>([]);

  const [ownerships, setOwnership] = useState<any[]>([]);
  const [ownershipCategories, setOwnershipCategories] = useState<any[]>([]);
  // const [ownershipCategories, setOwnershipCategories] = useState<
  //   { id: string; name: string }[]
  // >([]);

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
  const [selectedOwnershipCategory, setSelectedOwnershipCategory] =
    useState("");

  const [selectedOperational, setSelectedOperational] = useState("");
  const [selectedRegistration, setSelectedRegistration] = useState("");
  const [selectedLicense, setSelectedLicense] = useState("");

  const [selectedServiceCategory, setSelectedServiceCategory] = useState("");
  const [selectedService, setSelectedService] = useState("");
  const [fetchError, setFetchError] = useState<string>(""); // State for error messages

  const [selectedGeoCode, setSelectedGeoCode] = useState("");
  const [selectedServiceType, setSelectedServiceType] = useState("0");

  const [loading, setLoading] = useState<boolean>(true); // Loading state

  // const [data, setData] = useState([]); // Store API response
  const [data, setData] = useState<Facility[]>([]);
  const [currentPage, setCurrentPage] = useState(1); // Track pagination
  const [totalPages, setTotalPages] = useState(1); // Store total pages
  const [totalRecords, setTotalRecords] = useState(1); // Store total pages

  // const [allFacilities, setAllFacilities] = useState([]);
  const [allFacilities, setAllFacilities] = useState<Facility[]>([]);

  const searchInputRef = useRef<HTMLInputElement>(null);

  const [search, setSearch] = useState("");

  const entryPerPage = useMemo(
    () => [
      { id: "50", name: "50" },
      { id: "100", name: "100" },
      { id: "150", name: "150" },
      { id: "200", name: "200" },
    ],
    []
  );

  const [entriesPerPage, setEntriesPerPage] = useState<number>(() => {
    return parseInt(entryPerPage[0].id); // Ensures it's a number
  });

  useEffect(() => {
    if (typeof window !== "undefined") {
      const storedValue = localStorage.getItem("entriesPerPage");
      if (storedValue) {
        setEntriesPerPage(parseInt(storedValue));
      }
    }
  }, []);

  const fetchFacilities = useCallback(async () => {
    setLoading(true);
    setFetchError("");

    try {
      const response = await axios.post(
        `${process.env.NEXT_PUBLIC_BACKEND_API}/facilities-hospitals-search`,
        {
          state_id: selectedState,
          lga_id: selectedLga,
          ward_id: selectedWard,
          facility_level_id: selectedFacilityLevel,
          ownership_id: selectedownership,
          ownership_type_id: selectedOwnershipCategory,
          operational_status_id: selectedOperational,
          registration_status_id: selectedRegistration,
          license_status_id: selectedLicense,
          // outpatient: selectedServiceType ? 1 : 0,
          // inpatient: selectedServiceType ? 1 : 0,
          geo_codes: selectedGeoCode,

          service_category_id: selectedServiceCategory,
          services: selectedService,
          // facility_name: search,
          page: currentPage,
          per_page: entriesPerPage,

          // 👇 Add this:
          search: search.trim() !== "" ? search.trim() : null,
        }
      );

      // After fetching, update total records and total pages
      const fetchedData = response.data?.data?.facilities?.data;
      const totalRecords = response.data?.data?.facilities?.total;

      // Set new data and calculate total pages based on entriesPerPage
      setData(fetchedData); // Set fetched data
      setAllFacilities(fetchedData);
      setTotalRecords(totalRecords); // Set total records from API
      // setTotalPages(response.data?.data?.facilities?.last_page); // Set total pages from API

      const calculatedTotalPages = Math.ceil(totalRecords / entriesPerPage); // Recalculate total pages based on entriesPerPage
      setTotalPages(calculatedTotalPages); // Update total pages
      console.log("fetchedData by adams", fetchedData);
    } catch (error) {
      console.error("Error fetching data:", error);
    } finally {
      setLoading(false); // Ensure loading stops
    }
  }, [
    currentPage,
    selectedState,
    selectedLga,
    selectedWard,
    selectedFacilityLevel,
    selectedownership,
    selectedOwnershipCategory,
    selectedOperational,
    selectedRegistration,
    selectedLicense,
    selectedServiceType,
    entriesPerPage,
    selectedGeoCode,
    selectedServiceCategory,
    selectedService,
    search,
  ]);

  const fetchFacilities2 = useCallback(async () => {
    setLoading(true);
    setFetchError("");

    try {
      const response = await axios.post(
        `${process.env.NEXT_PUBLIC_BACKEND_API}/facilities-hospitals-search`,
        {
          facility_name: search, // Include facility name search
          per_page: entriesPerPage, // Send number of entries per page
          page: currentPage,
        }
      );

      // setData(response.data?.data?.facilities?.data);
      // setTotalPages(response.data?.data?.facilities?.last_page);

      // After fetching, update total records and total pages
      const fetchedData = response.data?.data?.facilities?.data;
      const totalRecords = response.data?.data?.facilities?.total;

      // Set new data and calculate total pages based on entriesPerPage
      setData(fetchedData); // Set fetched data
      setTotalRecords(totalRecords); // Set total records from API
      // setTotalPages(response.data?.data?.facilities?.last_page); // Set total pages from API

      const calculatedTotalPages = Math.ceil(totalRecords / entriesPerPage); // Recalculate total pages based on entriesPerPage
      setTotalPages(calculatedTotalPages); // Update total pages
      console.log("calculatedTotalPages by daniel", calculatedTotalPages);
    } catch (error) {
      setFetchError("Failed to fetch hospitals");
      console.error("Error fetching hospitals:", error);
    }

    setLoading(false);
  }, [search, entriesPerPage, currentPage]);

  // Fetch states
  const fetchStates = useCallback(async () => {
    setLoading(true);
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
      // setFetchError("Failed to fetch states.");
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
      // setFetchError("Failed to fetch facility-level.");
    } finally {
      setLoading(false);
    }
  }, []);

  // Fetch ownership
  const ownership = useCallback(async () => {
    setLoading(true); // ✅ Add this
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
      // setFetchError("Failed to fetch ownership.");
    } finally {
      setLoading(false);
    }
  }, []);

  // Fetch service-category
  const ownershipCategory = useCallback(async (ownershipId: string) => {
    setLoading(true); // ✅ Add this
    try {
      setOwnershipCategories([]);
      const response = await axios.post(
        `${process.env.NEXT_PUBLIC_BACKEND_API}/ownership-type`,
        { ownership_id: ownershipId },
        { headers: { "Content-Type": "application/json" } }
      );

      const data = response?.data?.data; // Axios automatically parses JSON

      console.log("response ownershipCategory", data);

      if (data && Array.isArray(data)) {
        setOwnershipCategories(data);
      }
    } catch (error) {
      console.error("Error fetching operational:", error);
      // setFetchError("Failed to fetch operational.");
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    if (selectedownership) {
      ownershipCategory(selectedownership);
    }
  }, [selectedownership, ownershipCategory]); // Runs when `selectedLga` changes

  // Fetch operational
  const operational = useCallback(async () => {
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
  const registrationStatus = useCallback(async () => {
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
  const license = useCallback(async () => {
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
      // setFetchError("Failed to fetch operational.");
    } finally {
      setLoading(false);
    }
  }, []);

  // Fetch service-category
  const serviceCategory = useCallback(async () => {
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
      // setFetchError("Failed to fetch operational.");
    } finally {
      setLoading(false);
    }
  }, []);

  // for dynamic download
const handleDownloadAll = async () => {
  try {
    const response = await axios.post(
      `${process.env.NEXT_PUBLIC_BACKEND_API}/facilities-hospitals-search`,
      {
        state_id: selectedState,
        lga_id: selectedLga,
        ward_id: selectedWard,
        facility_level_id: selectedFacilityLevel,
        ownership_id: selectedownership,
        ownership_type_id: selectedOwnershipCategory,
        operational_status_id: selectedOperational,
        registration_status_id: selectedRegistration,
        license_status_id: selectedLicense,
        geo_codes: selectedGeoCode,
        service_category_id: selectedServiceCategory,
        services: selectedService,
        search: search.trim() !== "" ? search.trim() : null,
        per_page: 999999, // Get all records
        page: 1,
      }
    );

    return response.data?.data?.facilities?.data || [];
  } catch (error) {
    console.error("Error fetching all data:", error);
    return [];
  }
};

  const fetchService = useCallback(async (serviceId: string) => {
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
      // setFetchError("Failed to fetch LGAs.");
    }
  }, []);

  // Fetch static data (only on mount)
  useEffect(() => {
    fetchStates();
    facilityLevel();
    ownership();
    operational();
    registrationStatus();
    license();
    serviceCategory();
  }, [
    fetchStates,
    facilityLevel,
    ownership,
    operational,
    registrationStatus,
    license,
    serviceCategory,
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
    fetchFacilities();
    return () => {
      // Cleanup: clear localStorage after fetch if needed
      localStorage.removeItem("entriesPerPage");
    };
    // if (currentPage) {
    // }
  }, [currentPage, fetchFacilities, entriesPerPage]); // Runs when `currentPage` changes

  const handleReset = useCallback(() => {
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
    setSelectedOwnershipCategory("");
    // setEntriesPerPage(50); // Reset to default 50 entries per page
    // setCurrentPage(1); // Reset to first page

    const defaultValue = entryPerPage[0].id; // Default to the first entry (25)
    setEntriesPerPage(parseInt(defaultValue, 10)); // Update state
    localStorage.setItem("entriesPerPage", defaultValue); // Update localStorage
    setCurrentPage(1); // Reset current page to 1 when entries per page change
    // fetchFacilities();
  }, [
    // currentPage,
    setSelectedState,
    setSelectedLga,
    setSelectedWard,
    setSelectedFacilityLevel,
    setSelectedownership,
    setSelectedOperational,
    setSelectedRegistration,
    setSelectedLicense,
    setSelectedServiceCategory,
    setSelectedService,
    setSelectedGeoCode,
    setSelectedServiceType,
    setSearch,
    setSelectedOwnershipCategory,
    entryPerPage,
  ]);

  const handleSelectChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
    const value = parseInt(e.target.value);
    localStorage.setItem("entriesPerPage", String(value));
    setEntriesPerPage(value);
    setCurrentPage(1);
  };

  const [isOpen, setIsOpen] = useState(true); // Accordion open by default

  return (
    <div>
      {/* <Text className="text-2xl pt-5 pb-5">Hospitals and Clinics</Text> */}

      <div className="border border-gray-300 rounded-lg shadow w-full mb-6">
        {/* Accordion Header */}
        <button
          onClick={() => setIsOpen(!isOpen)}
          className="flex items-center justify-between w-full px-4 py-3 bg-green-100 text-green-700 font-semibold rounded-t"
        >
          <span>
            {" "}
            <Text className="text-2xl pt-1 pb-1">Hospitals and Clinics</Text>
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
                  setSelectedLga("");
                  setSelectedWard("");
                  // setSearch("");
                  fetchLgas(selectedId);
                }}
                options={states.map((state) => ({
                  value: state.id, // Ensure value is the ID
                  name: state.name, // Display name
                }))}
                placeholder="Select State"
              />

              {/* lga Selection */}
              <SelectComponent
                className="w-full max-w-[350px]"
                value={selectedLga}
                onChange={(e) => {
                  const lgaId = e.target.value;
                  setSelectedLga(lgaId);
                  setSelectedWard("");
                  // setSearch("");
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
                  // setSearch("");
                }}
                options={wards.map((ward) => ({
                  value: ward.id, // Use Ward ID
                  name: ward.name, // Show Ward name
                }))}
                placeholder="Select Ward"
                disabled={!selectedLga} // Disable until LGA is selected
              />

              {/* facility level Selection */}
              <SelectComponent
                className="w-full max-w-[350px]"
                value={selectedFacilityLevel} // Track selected value
                onChange={(e) => {
                  setSelectedFacilityLevel(e.target.value); // Update state
                  // setSearch("");
                }}
                options={facilityLevels.map((level) => ({
                  value: level.id, // Use level ID
                  name: level.name, // Show level name
                }))}
                placeholder="Select Facility Level"
              />

              {/* Ownership Selection */}
              <SelectComponent
                className="w-full max-w-[350px]"
                value={selectedownership} // Track selected value
                // onChange={(e) => {
                //   const ownershipId = e.target.value;
                //   setSelectedownership(ownershipId);
                //   ownershipCategory(ownershipId); // Fetch Wards for selected LGA
                // }}
                onChange={(e) => {
                  const ownershipId = e.target.value;
                  setSelectedownership(ownershipId); // Update selected ownership
                  setSelectedOwnershipCategory(""); // Clear ownership type selection when ownership is changed
                  // setSearch("");
                  ownershipCategory(ownershipId); // Fetch related ownership categories for the selected ownership
                }}
                options={ownerships.map((item) => ({
                  value: item.id, // Use items ID
                  name: item.name, // Show items name
                }))}
                placeholder="Select Ownership"
              />

              {/* Show Ownership Category Dropdown Only When Ownership is Selected */}
              {selectedownership && (
                <SelectComponent
                  className="w-full max-w-[350px]"
                  value={selectedOwnershipCategory}
                  onChange={(e) => setSelectedOwnershipCategory(e.target.value)}
                  options={ownershipCategories.map((item) => ({
                    value: item.id, // Map `id` to `value`
                    name: item.type,
                  }))}
                  placeholder="Select Ownership Type"
                />
              )}

              {/* operational Selection */}
              <SelectComponent
                className="w-full max-w-[350px]"
                value={selectedOperational} // Track selected value
                onChange={(e) => {
                  setSelectedOperational(e.target.value); // Update state
                  // setSearch("");
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
                  // setSearch("");
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
                  // setSearch("");
                }}
                options={licenses.map((items) => ({
                  value: items.id, // Use items ID
                  name: items.status, // Show items name
                }))}
                placeholder="Select License status"
              />

              {/* Select Coordinates */}
              <SelectComponent4
                className="w-full max-w-[350px]"
                value={selectedGeoCode} // Track selected value
                onChange={(e) => {
                  setSelectedGeoCode(e.target.value); // Update state
                  // setSearch("");
                }}
                options={[
                  // { value: "0", name: "Select Coordinates" },
                  { value: "1", name: "With Coordinates" },
                  { value: "2", name: "With No Coordinates" },
                ]}
                // placeholder="Select Coordinates"
              />

              {/* Select Service type */}
              <SelectComponent
                className="w-full max-w-[350px]"
                value={selectedServiceCategory}
                onChange={(e) => {
                  const serviceId = e.target.value; // Get the state ID
                  setSelectedServiceCategory(serviceId);
                  fetchService(serviceId);
                  // setSearch("");
                }}
                options={serviceCategories.map((items) => ({
                  value: items.id, // Use items ID
                  name: items.description, // Show items name
                }))}
                placeholder="Select Service Category"
              />

              <SelectComponent
                className="w-full max-w-[350px]"
                value={selectedService}
                onChange={(e) => {
                  setSelectedService(e.target.value);
                  // setSearch("");
                }}
                options={services.map((item) => ({
                  value: item.id, // Use item ID
                  name: item.name, // Show item name
                }))}
                placeholder="Select Service"
                disabled={!selectedState} // Disable until State is selected
              />
            </div>
          </div>
        )}
      </div>

      <div className="flex flex-wrap items-center justify-center gap-4 bg-[#D1D1D1] p-4 mt-4 w-full md:grid md:grid-cols-2 lg:flex lg:gap-6">
        {/* Facility Name Input */}
        <Input
          className="w-full max-w-[400px] h-[47px] text-sm"
          placeholder="Facility Name"
          value={search}
          onChange={(e) => setSearch(e.target.value)}
        />

        {/* Entries Per Page Dropdown */}
        <SelectComponent3
          className="w-full max-w-[300px] h-[44px] text-sm"
          placeholder="Entries Per Page"
          options={entryPerPage.map((item) => ({
            value: item.id, // Use item ID
            name: item.name, // Show item name
          }))}
          value={String(entriesPerPage)} // Convert number to string for select compatibility
          onChange={handleSelectChange} // Correct conversion to number
        />

        {/* Reset Button */}
        <button
          className="bg-[#EFEFEF] rounded-lg w-full max-w-[200px] h-[44px] flex items-center justify-center text-sm"
          onClick={handleReset}
        >
          Reset
        </button>

        <GreenButton
          onClick={() => {
            setCurrentPage(1); // Reset to first page before searching
            fetchFacilities();
          }}
          // onClick={filterAndPaginateFacilities}
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
          <HospitalTable
            key={`${entriesPerPage}-${currentPage}`} // force re-render
            data={data}
            currentPage={currentPage}
            setCurrentPage={setCurrentPage}
            totalPages={totalPages}
            totalRecords={totalRecords}
            // fetchFacilities={fetchFacilities} // Pass function to child
            entriesPerPage={entriesPerPage} // Pass entriesPerPage to child component
            onDownloadAll={handleDownloadAll}
          />
        </div>
      </div>
    </div>
  );
};

export default HospitalTab;
