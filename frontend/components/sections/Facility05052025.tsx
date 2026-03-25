"use client";

import { Button, Card } from "@chakra-ui/react";
import { GreenButton, Text, WhiteButton } from "../ui/Typography";
import { FaInfoCircle, FaMapMarkerAlt, FaPhoneAlt } from "react-icons/fa";

import React, {
  useState,
  useRef,
  useCallback,
  useEffect,
  useMemo,
} from "react";
import {
  LoadScript,
  LoadScriptNext,
  GoogleMap,
  Marker,
  InfoWindow,
  DirectionsRenderer,
  DirectionsService,
} from "@react-google-maps/api";

import {
  MapPin,
  Search,
  SlidersHorizontal,
  LayoutDashboard,
  Menu,
  Copy,
} from "lucide-react";

import Image from "next/image";
import axios from "axios";

import { useRouter, usePathname } from "next/navigation";
// import { useRouter } from "next/router";
import { format } from "url";
import Link from "next/link";
import Swal from "sweetalert2";

const libraries: "places"[] = ["places"];
const itemsPerPage = 10;

const defaultLocation = { lat: 9.058, lng: 7.489 }; // 📍 Abuja (Fallback)

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
  facility_type_id?: number;
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
}

function Facility() {
  const router = useRouter();
  const pathname = usePathname();

  useEffect(() => {
    // Only run on the /facilityfinder route
    if (!pathname.startsWith("/facilityfinder")) return;

    const handleRouteChange = (url: string) => {
      // If user navigates away from /facilityfinder, clear storage
      if (!url.startsWith("/facilityfinder")) {
        localStorage.removeItem("facilitySearchState");
        localStorage.removeItem("searchResults");
        localStorage.removeItem("homePageSearchQuery");
        console.log("Storage cleared on route change to:", url);
      }
    };

    // Listen to route changes
    window.addEventListener("beforeunload", () => {
      // Optional: Clear on page refresh if desired
      localStorage.removeItem("facilitySearchState");
      localStorage.removeItem("searchResults");
      localStorage.removeItem("homePageSearchQuery");
    });

    // @ts-ignore - router is NextRouter but no types for events here in App Router
    router.events?.on("routeChangeStart", handleRouteChange);

    return () => {
      // @ts-ignore
      router.events?.off("routeChangeStart", handleRouteChange);
    };
  }, [pathname, router]);

  const [map, setMap] = useState<google.maps.Map | null>(null);

  const [hospitals, setHospitals] = useState<any[]>([]);

  const [selectedHospital, setSelectedHospital] = useState<any | null>(null);
  const [showHospitalInfoCard, setShowHospitalInfoCard] = useState(false);

  const [center, setCenter] = useState({ lat: 9.0765, lng: 7.3986 }); // Abuja coordinates
  // const [center, setCenter] = useState({ lat: 6.5244, lng: 3.3792 }); // Default: Lagos
  const [loading, setLoading] = useState(false);

  const searchInputRef = useRef<HTMLInputElement>(null);

  const [facilityTypes, setFacilityTypes] = useState<
    { id: string; name: string }[]
  >([]);

  const [facilityLevels, setFacilityLevel] = useState<any[]>([]);

  const [fetchError, setFetchError] = useState<string>(""); // State for error

  const [selectedFacilityLevel, setSelectedFacilityLevel] = useState("");
  const [selectedFacilityType, setSelectedFacilityType] = useState("");

  const [currentPage, setCurrentPage] = useState(1); // Track pagination
  const [search, setSearch] = useState("");

  ////////////////////////////////////////////////////////////////////////////////////////////

  const [userAddress, setUserAddress] = useState<string | null>(null);
  const [googleMapsLoaded, setGoogleMapsLoaded] = useState(false);
  // const [focusedHospital, setFocusedHospital] = useState(null);
  const [focusedHospital, setFocusedHospital] = useState<Facility | null>(null);
  const [showUserInfo, setShowUserInfo] = useState(false);
  const [isGridView, setIsGridView] = useState(true); // State to track the layout
  // const [viewType, setViewType] = useState<"list" | "grid">("list");
  // const itemsPerPage = viewType === "list" ? 10 : 20;

  const itemsPerPage = isGridView ? 10 : 20;
  // Calculate total pages
  // const totalPages = Math.ceil(hospitals.length / itemsPerPage);

  // Get hospitals for the current page
  const indexOfLastItem = currentPage * itemsPerPage;
  const indexOfFirstItem = indexOfLastItem - itemsPerPage;
  const currentHospitals = hospitals.slice(indexOfFirstItem, indexOfLastItem);

  const [selectedStateId, setSelectedStateId] = useState<string>("");
  const [statesInResults, setStatesInResults] = useState<
    { id: string; name: string }[]
  >([]);

  const [selectedLgaId, setSelectedLgaId] = useState<string>("");
  const [lgasInResults, setLgasInResults] = useState<
    { id: string; name: string }[]
  >([]);

  const [selectedWardId, setSelectedWardId] = useState<string>("");
  const [wardsInResults, setWardsInResults] = useState<
    { id: string; name: string }[]
  >([]);

  const [showFilters, setShowFilters] = useState(false);
  const [showLgaDropdown, setShowLgaDropdown] = useState(false);
  const [showWardDropdown, setShowWardDropdown] = useState(false);
  const [hasSearched, setHasSearched] = useState(false);
  ////////////////////////////////////////////////////////////////////////////////////////////

  const [directions, setDirections] =
    useState<google.maps.DirectionsResult | null>(null);

  const [directionsRenderer, setDirectionsRenderer] = useState(null);

  const [directionsService, setDirectionsService] =
    useState<google.maps.DirectionsService | null>(null);
  const [userLocation, setUserLocation] =
    useState<google.maps.LatLngLiteral | null>(null);

  // Initialize Google Directions Service
  const onMapLoad = (map: any) => {
    setMap(map);
    setDirectionsService(() => new window.google.maps.DirectionsService());
  };

  // 1️⃣ 📍 Detect User’s Initial Location (Runs Once)
  useEffect(() => {
    if (!navigator.geolocation) {
      console.warn("Geolocation is not supported");
      setUserLocation(defaultLocation);
      return;
    }

    navigator.geolocation.getCurrentPosition(
      (position) => {
        console.log("📍 Accurate Location:", position.coords);
        setUserLocation({
          lat: position.coords.latitude,
          lng: position.coords.longitude,
        });
      },
      (error) => {
        console.error("🚨 Location Error:", error);
        setUserLocation(defaultLocation); // Fallback to Abuja
      },
      {
        enableHighAccuracy: true, // Forces GPS instead of IP
        timeout: 15000, // Waits 15s before failing
        maximumAge: 0, // Prevents cached locations
      }
    );
  }, []);

  // 2️⃣ 🛰️ Continuously Track User’s Location (Watches for Changes)
  // useEffect(() => {
  //   const watchId = navigator.geolocation.watchPosition(
  //     (position) => {
  //       setUserLocation({
  //         lat: position.coords.latitude,
  //         lng: position.coords.longitude,
  //       });
  //       console.log("📍 Updated Location:", position.coords);
  //     },
  //     (error) => console.error("❌ Error tracking location:", error),
  //     { enableHighAccuracy: true, maximumAge: 0, timeout: 5000 }
  //   );

  //   return () => navigator.geolocation.clearWatch(watchId); // Cleanup
  // }, []);

  // UseEffect for location tracking
  useEffect(() => {
    const watchId = navigator.geolocation.watchPosition(
      (position) => {
        const updatedLocation = {
          lat: position.coords.latitude,
          lng: position.coords.longitude,
        };

        // Only update the user location if it's not the initial location
        if (
          !userLocation ||
          (userLocation.lat !== updatedLocation.lat &&
            userLocation.lng !== updatedLocation.lng)
        ) {
          setUserLocation(updatedLocation);
          console.log("📍 Updated Location:", position.coords);
        }
      },
      (error) => console.error("❌ Error tracking location:", error),
      { enableHighAccuracy: true, maximumAge: 0, timeout: 5000 }
    );

    return () => navigator.geolocation.clearWatch(watchId); // Cleanup
  }, [userLocation]);

  // 3️⃣ 🗺️ Update Map & Get Directions When a Hospital is Selected
  useEffect(() => {
    if (!selectedHospital || !userLocation) return;

    setCenter({
      lat: selectedHospital.latitude,
      lng: selectedHospital.longitude,
    });

    const directionsService = new window.google.maps.DirectionsService();

    directionsService.route(
      {
        origin: userLocation, // User's location
        destination: {
          lat: selectedHospital.latitude,
          lng: selectedHospital.longitude,
        },
        travelMode: window.google.maps.TravelMode.DRIVING,
      },
      (result, status) => {
        if (status === window.google.maps.DirectionsStatus.OK) {
          setDirections(result);
        } else {
          console.error("❌ Error fetching directions:", status);
        }
      }
    );
  }, [selectedHospital, userLocation]);

  const handleGetDirections = (hospital: any) => {
    if (!userLocation) {
      console.error("User location not available yet.");
      return;
    }

    const hasValidCoordinates =
      hospital &&
      hospital.latitude !== null &&
      hospital.longitude !== null &&
      parseFloat(hospital.latitude) !== 0 &&
      parseFloat(hospital.longitude) !== 0 &&
      !isNaN(parseFloat(hospital.latitude)) &&
      !isNaN(parseFloat(hospital.longitude));

    // console.log({ hasValidCoordinates, hospital });

    if (!hasValidCoordinates) {
      Swal.fire({
        icon: "warning",
        title: "Location Unavailable",
        text: "This facility does not have valid location coordinates and cannot be shown on the map.",
        confirmButtonColor: "#2563EB", // Optional styling
      });
      return;
    }

    const destination = {
      lat: parseFloat(hospital.latitude),
      lng: parseFloat(hospital.longitude),
    };

    if (isNaN(destination.lat) || isNaN(destination.lng)) {
      console.error("Invalid coordinates for hospital:", hospital);
      return;
    }

    console.log("Getting directions from:", userLocation, "to:", destination);

    const directionsService = new google.maps.DirectionsService();

    directionsService.route(
      {
        origin: userLocation, // Use current location
        destination, // Use parsed coordinates
        travelMode: google.maps.TravelMode.DRIVING,
      },
      (result, status) => {
        if (status === google.maps.DirectionsStatus.OK) {
          setDirections(result);
          setCenter(destination); // Center map on selected hospital

          // ✅ Only keep user location & selected hospital markers
          setSelectedHospital(hospital); // ✅ Set the selected hospital
          setFocusedHospital(hospital); // ✅ Persist focus even if InfoWindow closes
          setShowUserInfo(false);
          setShowHospitalInfoCard(false);
        } else {
          console.error("Directions request failed:", status);
        }
      }
    );
  };

  const fetchFacilities = useCallback(
    async (
      searchValues: {
        facilityLevel?: string;
        facilityType?: string;
        search?: string;
      } = {}
    ) => {
      setLoading(true);
      setFetchError("");

      try {
        const params: any = {};

        // If there are search values, append them as query parameters
        if (searchValues.facilityLevel) {
          params.facility_level_id = searchValues.facilityLevel;
        }
        if (searchValues.facilityType) {
          params.facility_type_id = searchValues.facilityType;
        }
        if (searchValues.search) {
          params.facility_name = searchValues.search;
        }

        // Make GET request with query parameters
        const response = await axios.get(
          `${process.env.NEXT_PUBLIC_BACKEND_API}/facilities-hospitals-search3`,
          { params }
        );

        // Process the response to extract the facilities
        // Directly assign fetchedFacilities using optional chaining and nullish coalescing
        // let fetchedFacilities =
        //   response.data?.data?.facilities?.data ??
        //   response.data?.data?.facilities ??
        //   [];

        let fetchedFacilities: Facility[] =
          response.data?.data?.facilities?.data ??
          response.data?.data?.facilities ??
          [];

        // console.log("checking raw", fetchedFacilities);

        // Ensure latitude and longitude are numbers
        const validFacilities: Facility[] = fetchedFacilities
          .map(
            (facility: any): Facility => ({
              ...facility,
              latitude:
                facility.latitude && !isNaN(Number(facility.latitude))
                  ? Number(facility.latitude)
                  : null,
              longitude:
                facility.longitude && !isNaN(Number(facility.longitude))
                  ? Number(facility.longitude)
                  : null,
            })
          )
          .filter(
            (facility: Facility) =>
              facility.latitude !== null && facility.longitude !== null
          );

        const defaultLat = 0; // or center of the country/map
        const defaultLng = 0;

        const mappedFacilities = fetchedFacilities.map((f) => ({
          ...f,
          latitude:
            f.latitude && !isNaN(Number(f.latitude))
              ? Number(f.latitude)
              : userLocation?.lat ?? null,
          longitude:
            f.longitude && !isNaN(Number(f.longitude))
              ? Number(f.longitude)
              : userLocation?.lng ?? null,
        }));

        // Update state with valid facilities
        // setHospitals(validFacilities);
        setHospitals(mappedFacilities);
        setSelectedHospital(null);

        // 🔥 Extract unique state_id and state_name
        const uniqueStates = Array.from(
          new Map(
            // validFacilities
            mappedFacilities
              .filter(
                (f) => f.state_id !== undefined && f.state_name !== undefined
              )
              .map((f) => [
                f.state_id,
                { id: String(f.state_id), name: String(f.state_name) },
              ])
          ).values()
        );

        // Save to state to populate dropdown
        setStatesInResults(uniqueStates);
      } catch (error) {
        console.error("Error fetching data:", error);
      } finally {
        setLoading(false);
      }
    },
    []
  );

  const handleSearch = async () => {
    setLoading(true);
    setHasSearched(false); // reset before search

    // ✅ Clear previous map states BEFORE search
    setDirections(null);
    setSelectedHospital(null);
    setFocusedHospital(null);
    setShowHospitalInfoCard(false);

    // ✅ Reset Facility Type & Level
    setSelectedFacilityType("");
    setSelectedFacilityLevel("");

    // Clear the location filters as well
    setSelectedStateId("");
    setSelectedLgaId("");
    setSelectedWardId("");
    setShowLgaDropdown(false);
    setShowWardDropdown(false);

    const query = {
      facilityLevel: selectedFacilityLevel || "",
      facilityType: selectedFacilityType || "",
      search: search || "",
    };
    try {
      // 🟢 Update browser URL with query parameters
      router.push(format({ pathname: "/facilityfinder", query }));

      await fetchFacilities(query);

      // 🔥 Filter after fetching and storing facilities

      // Optional delay (only if needed)
      await new Promise((resolve) => setTimeout(resolve, 2000));

      // Reset pagination to first page after search
      setCurrentPage(1);

      // Show filters after data is ready
      setShowFilters(true);
    } catch (error) {
      console.error("Error fetching facilities:", error);
    } finally {
      setLoading(false);
      setHasSearched(true); // ✅ allow map to fit after loading
    }

    localStorage.removeItem("searchResults");
    localStorage.removeItem("homePageSearchQuery");

    // ✅ Fit the map after facilities update
    // fitMapToHospitals();
  };

  // Fetch data from the API
  const fetchFacilityTypes = useCallback(async () => {
    try {
      const response = await axios.get(
        `${process.env.NEXT_PUBLIC_BACKEND_API}/facility-type`
      );

      const data = response?.data?.data;
      // console.log("fetchFacilityTypes data", data);

      if (data && Array.isArray(data)) {
        setFacilityTypes(data);
      }
    } catch (error) {
      setFetchError("Failed to fetch facility types.");
    } finally {
      setLoading(false);
    }
  }, []);

  const fetchFacilityLevels = useCallback(async () => {
    try {
      const response = await axios.get(
        `${process.env.NEXT_PUBLIC_BACKEND_API}/facility-level`
      );

      const data = response?.data?.data; // Axios automatically parses JSON
      // console.log("data", data);

      if (data && Array.isArray(data)) {
        setFacilityLevel(data); // Set the options from the fetched data
      }

      // console.log("fetchFacilityLevels data", data);
    } catch (error) {
      setFetchError("Failed to fetch facility types.");
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchFacilityTypes();
    fetchFacilityLevels();
  }, [fetchFacilityTypes, fetchFacilityLevels]);

  // Set the Map Center to the First Search Result
  useEffect(() => {
    if (hospitals.length > 0) {
      setCenter({
        lat: hospitals[0].latitude,
        lng: hospitals[0].longitude,
      });
    }
  }, [hospitals]);

  ////////////////////////////////////////////////////////////////////////////
  useEffect(() => {
    const rawResults = localStorage.getItem("searchResults");
    const searchQuery = localStorage.getItem("homePageSearchQuery");
    const searchQueryResult = searchQuery ? JSON.parse(searchQuery) : {};

    setSearch(searchQueryResult.search);
    setSelectedFacilityType(searchQueryResult.facilityType || "");
    setSelectedFacilityLevel(searchQueryResult.facilityLevel || "");

    // console.log({ searchQueryResult });

    let storedResults: Facility[] = [];

    try {
      storedResults = rawResults ? JSON.parse(rawResults) : [];
    } catch (error) {
      console.error("Invalid JSON in localStorage. Resetting to empty array.");
      storedResults = [];
    }

    // ✅ Handle all cases: valid data or empty array
    const validFacilities: Facility[] = storedResults
      .map(
        (facility: any): Facility => ({
          ...facility,
          latitude:
            facility.latitude && !isNaN(Number(facility.latitude))
              ? Number(facility.latitude)
              : null,
          longitude:
            facility.longitude && !isNaN(Number(facility.longitude))
              ? Number(facility.longitude)
              : null,
        })
      )
      .filter(
        (facility: Facility) =>
          facility.latitude !== null && facility.longitude !== null
      );

    const mappedFacilities = storedResults.map((f) => ({
      ...f,
      latitude:
        f.latitude && !isNaN(Number(f.latitude))
          ? Number(f.latitude)
          : userLocation?.lat ?? null,
      longitude:
        f.longitude && !isNaN(Number(f.longitude))
          ? Number(f.longitude)
          : userLocation?.lng ?? null,
    }));

    // setHospitals(validFacilities);
    setHospitals(mappedFacilities);

    // 🔥 Extract unique state_id and state_name
    const uniqueStates = Array.from(
      new Map(
        // validFacilities
        mappedFacilities
          .filter((f) => f.state_id !== undefined && f.state_name !== undefined)
          .map((f) => [
            f.state_id,
            { id: String(f.state_id), name: String(f.state_name) },
          ])
      ).values()
    );

    // Save to state to populate dropdown
    setStatesInResults(uniqueStates);

    console.log({ uniqueStates });

    if (uniqueStates && uniqueStates.length > 0) {
      setShowFilters(true);
    }

    // ✅ Only fetch if localStorage has *never* been set
    if (!rawResults) {
      console.log("No localStorage found. Fetching from API...");
      fetchFacilities();
    }
  }, [fetchFacilities]);

  ///////////////////////////////////////////////////////////////////////

  useEffect(() => {
    const handleRouteChange = () => {
      console.log(
        "Navigation detected, clearing searchResults from localStorage."
      );
      localStorage.removeItem("searchResults");
      localStorage.removeItem("homePageSearchQuery");
    };

    const originalPush = router.push;
    router.push = (...args) => {
      handleRouteChange(); // Clear localStorage before navigation
      return originalPush(...args);
    };

    return () => {
      router.push = originalPush; // Restore original push function on cleanup
    };
  }, [router]);

  // Wait for Google Maps API to load
  useEffect(() => {
    if (typeof window !== "undefined" && window.google && window.google.maps) {
      setGoogleMapsLoaded(true);
    }
  }, []);

  useEffect(() => {
    if (userLocation && googleMapsLoaded) {
      const geocoder = new window.google.maps.Geocoder();

      geocoder.geocode({ location: userLocation }, (results, status) => {
        if (status === "OK" && results && results[0]) {
          setUserAddress(results[0].formatted_address);
        } else {
          console.error("Geocoder failed: ", status);
          setUserAddress("Current location");
        }
      });
    }
  }, [userLocation, googleMapsLoaded]);

  useEffect(() => {
    const storedSearch = localStorage.getItem("facilitySearchState");

    if (storedSearch) {
      const parsed = JSON.parse(storedSearch);

      // console.log({ parsed });

      setSearch(parsed.search || "");
      setSelectedFacilityType(parsed.facilityType || "");
      setSelectedFacilityLevel(parsed.facilityLevel || "");

      fetchFacilities({
        search: parsed.search,
        facilityType: parsed.facilityType,
        facilityLevel: parsed.facilityLevel,
      });
    }
  }, [fetchFacilities]);

  const filteredHospitals = hospitals.filter((h) => {
    const matchState = selectedStateId
      ? String(h.state_id) === selectedStateId
      : true;
    const matchLga = selectedLgaId ? String(h.lga_id) === selectedLgaId : true;
    const matchWard = selectedWardId
      ? String(h.ward_id) === selectedWardId
      : true;
    return matchState && matchLga && matchWard;
  });

  useEffect(() => {
    if (!selectedLgaId) {
      setWardsInResults([]);
      return;
    }

    const filteredWards = Array.from(
      new Map(
        hospitals
          .filter((f) => String(f.lga_id) === selectedLgaId)
          .map((f) => [f.ward_id, { id: String(f.ward_id), name: f.ward_name }])
      ).values()
    );

    setWardsInResults(filteredWards);
  }, [selectedLgaId, hospitals]);

  const totalPages = Math.ceil(filteredHospitals.length / itemsPerPage);

  const paginatedHospitals = filteredHospitals.slice(
    (currentPage - 1) * itemsPerPage,
    currentPage * itemsPerPage
  );

  useEffect(() => {
    setCurrentPage(1);
  }, [filteredHospitals.length]);

  useEffect(() => {
    const fetchFilteredFacilities = async () => {
      const query = {
        facilityLevel: selectedFacilityLevel || "",
        facilityType: selectedFacilityType || "",
        search: search || "",
      };

      try {
        // Optional: update URL if needed
        router.push(format({ pathname: "/facilityfinder", query }));

        await fetchFacilities(query); // Your existing fetching logic
        setCurrentPage(1); // reset pagination
      } catch (error) {
        console.error("Error filtering facilities:", error);
      }
    };

    // Trigger fetch only if any value is selected
    if (selectedFacilityType || selectedFacilityLevel) {
      fetchFilteredFacilities();
    }
  }, [selectedFacilityType, selectedFacilityLevel]);

  // On selecting a facility type
  const handleFacilityTypeChange = (
    e: React.ChangeEvent<HTMLSelectElement>
  ) => {
    // ✅ Clear previous map states BEFORE search
    setDirections(null);
    setSelectedHospital(null);
    setFocusedHospital(null);
    setShowHospitalInfoCard(false);

    const selectedType = e.target.value;
    setSelectedFacilityType(selectedType); // Update selected facility type

    // Avoid making a fetch request if the selection didn't change
    if (selectedType !== "" && selectedType === selectedFacilityType) {
      return; // No need to fetch if the same value is selected
    }

    // Reset to all records if the default value is selected
    if (selectedType === "") {
      console.log({ selectedType });

      // Show all records or the filtered records by state
      fetchFacilities({ search });
      // setSelectedFacilityLevel("");
    } else {
      // Fetch facilities based on the selected facility type
      fetchFacilities({ facilityType: selectedType });
    }

    setHasSearched(true); // ✅ Trigger the map fit after state selection

    // ✅ Fit the map after facilities update
    // fitMapToHospitals();
  };

  const handleFacilityLevelChange = (
    e: React.ChangeEvent<HTMLSelectElement>
  ) => {
    // ✅ Clear previous map states BEFORE search
    setDirections(null);
    setSelectedHospital(null);
    setFocusedHospital(null);
    setShowHospitalInfoCard(false);

    const selectedLevel = e.target.value;
    setSelectedFacilityLevel(selectedLevel); // Update selected facility type

    // Avoid making a fetch request if the selection didn't change
    if (selectedLevel != "" && selectedLevel == selectedFacilityLevel) {
      return; // No need to fetch if the same value is selected
    }

    // Reset to all records if the default value is selected
    if (selectedLevel === "") {
      console.log({ selectedLevel });

      // Show all records or the filtered records by state
      fetchFacilities({ search });
    } else {
      // Fetch facilities based on the selected facility type
      fetchFacilities({ facilityType: selectedLevel });
    }

    // ✅ Fit the map after facilities update
    // fitMapToHospitals();
  };

  const handleStateChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
    const selectedState = e.target.value;
    setSelectedStateId(selectedState);
    setSelectedLgaId(""); // Reset LGA when state changes
    // setSelectedWardId(""); // Reset Ward when state changes
    setShowLgaDropdown(!!selectedState); // Show LGA dropdown if a state is selected
    // setShowWardDropdown(false); // Hide ward dropdown initially

    // ✅ Clear previous map states BEFORE search
    setDirections(null);
    setSelectedHospital(null);
    setFocusedHospital(null);
    setShowHospitalInfoCard(false);

    // Update the LGA options based on the selected state
    const filteredLgas = Array.from(
      new Map(
        hospitals
          .filter(
            (f) =>
              String(f.state_id) === selectedState && f.lga_id !== undefined
          )
          .map((f) => [
            f.lga_id,
            {
              id: String(f.lga_id),
              name: String(f.lga_name),
            },
          ])
      ).values()
    );

    setLgasInResults(filteredLgas);

    setHasSearched(true); // ✅ Trigger the map fit after state selection

    // ✅ Fit the map after facilities update
    // fitMapToHospitals();
  };

  const handleLgaChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
    const lgaId = e.target.value;
    // ✅ Clear previous map states BEFORE search
    setDirections(null);
    setSelectedHospital(null);
    setFocusedHospital(null);
    setShowHospitalInfoCard(false);

    setSelectedLgaId(lgaId);
    setSelectedWardId(""); // Reset Ward when LGA changes
    setShowWardDropdown(!!lgaId); // Show ward dropdown if LGA is selected

    setHasSearched(true); // ✅ Trigger the map fit after state selection

    // ✅ Fit the map after facilities update
    // fitMapToHospitals();
  };

  const handleWardChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
    setSelectedWardId(e.target.value);
  };

  const hospitalsWithCoordinates = useMemo(() => {
    return hospitals
      .filter((h) => {
        const matchState = selectedStateId
          ? String(h.state_id) == selectedStateId
          : true;
        const matchLga = selectedLgaId
          ? String(h.lga_id) == selectedLgaId
          : true;
        const matchWard = selectedWardId
          ? String(h.ward_id) == selectedWardId
          : true;

        return matchState && matchLga && matchWard;
      })
      .filter(
        (h) =>
          h.latitude != null &&
          h.longitude != null &&
          !isNaN(h.latitude) &&
          !isNaN(h.longitude)
      );
  }, [hospitals, selectedStateId, selectedLgaId, selectedWardId]);

  const mapRef = useRef<google.maps.Map | null>(null);

  const fitMapToHospitals = () => {
    if (
      !googleMapsLoaded ||
      !userLocation ||
      !mapRef.current ||
      hospitalsWithCoordinates.length === 0
    )
      return;

    const bounds = new window.google.maps.LatLngBounds();
    bounds.extend(userLocation);

    hospitalsWithCoordinates.forEach((hospital) => {
      if (
        typeof hospital.latitude === "number" &&
        typeof hospital.longitude === "number"
      ) {
        bounds.extend({
          lat: hospital.latitude,
          lng: hospital.longitude,
        });
      }
    });

    mapRef.current.fitBounds(bounds, {
      top: 50,
      bottom: 50,
      left: 50,
      right: 50,
    });
  };

  useEffect(() => {
    if (
      hospitalsWithCoordinates.length > 0 &&
      (selectedStateId ||
        selectedLgaId ||
        selectedFacilityLevel ||
        selectedFacilityType ||
        hasSearched)
    ) {
      fitMapToHospitals();
    }
  }, [
    selectedStateId,
    selectedLgaId,
    selectedFacilityType,
    selectedFacilityLevel,
    hospitalsWithCoordinates,
    hasSearched,
  ]);

  return (
    <div className="min-h-screen bg-gray-50">
      <div className="container mx-auto p-4">
        <div className="bg-white rounded-lg shadow-sm p-6">
          <h1 className="text-lg font-medium text-gray-800 mb-6">
            Search based on Location / Facility Name
          </h1>

          <div className="space-y-4">
            <div className="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-8 gap-4">
              {/* 🔍 Search Location */}
              <div className="relative lg:col-span-2">
                <input
                  ref={searchInputRef}
                  type="text"
                  placeholder="Enter Location/Facility Name"
                  value={search}
                  onChange={(e) => setSearch(e.target.value)}
                  className="w-full p-3 pr-10 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                />
                {/* <Search className="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400" /> */}
              </div>

              {showFilters && (
                <>
                  {/* 🏥 State */}
                  <div className="lg:col-span-2">
                    <select
                      value={selectedStateId}
                      onChange={handleStateChange}
                      className="w-full p-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                      <option value="">Select State</option>
                      {statesInResults.map((state) => (
                        <option key={state.id} value={state.id}>
                          {state.name}
                        </option>
                      ))}
                    </select>
                  </div>
                </>
              )}

              {/* 🏥 Lga */}
              {showLgaDropdown && (
                <div className="lg:col-span-2">
                  <select
                    value={selectedLgaId}
                    // onChange={(e) => setSelectedLgaId(e.target.value)}
                    onChange={handleLgaChange}
                    className="w-full p-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                  >
                    <option value="">Select LGA</option>
                    {lgasInResults.map((lga) => (
                      <option key={lga.id} value={lga.id}>
                        {lga.name}
                      </option>
                    ))}
                  </select>
                </div>
              )}

              {/* 🏥 Ward */}

              {/* {showWardDropdown && (
                <div className="lg:col-span-2">
                  <select
                    value={selectedWardId}
                    onChange={handleWardChange}
                    className="w-full p-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                  >
                    <option value="">Select Ward</option>
                    {wardsInResults.map((ward) => (
                      <option key={ward.id} value={ward.id}>
                        {ward.name}
                      </option>
                    ))}
                  </select>
                </div>
              )} */}

              {/* 🏥 Facility Type */}
              <div className="lg:col-span-2">
                <select
                  value={selectedFacilityType}
                  // onChange={(e) => setSelectedFacilityType(e.target.value)}
                  onChange={handleFacilityTypeChange} // Trigger fetch on change
                  className="w-full p-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                >
                  <option value="">Select Facility Type</option>
                  {facilityTypes?.map((type) => (
                    <option key={type.id} value={type.id}>
                      {type.name}
                    </option>
                  ))}
                </select>
              </div>

              {/* 📊 Facility Level */}
              <div className="lg:col-span-2">
                <select
                  value={selectedFacilityLevel}
                  // onChange={(e) => setSelectedFacilityLevel(e.target.value)}
                  onChange={handleFacilityLevelChange}
                  className="w-full p-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                >
                  <option value="">Select Facility Level</option>
                  {facilityLevels?.map((level) => (
                    <option key={level.id} value={level.id}>
                      {level.name}
                    </option>
                  ))}
                </select>
              </div>

              {/* 🔍 Search Button */}
              <div className="lg:col-span-2 flex items-center">
                <GreenButton
                  onClick={handleSearch}
                  className="w-full h-[44px] flex items-center justify-center text-sm"
                >
                  {loading ? "Loading..." : "Search"}
                </GreenButton>
              </div>
            </div>

            <div className="flex flex-col">
              {/* Layout Toggle Buttons */}
              <div className="flex justify-between items-center bg-green-50 p-4 rounded-lg">
                <p className="text-gray-700">
                  {filteredHospitals.length >= 1000
                    ? filteredHospitals.length + "+"
                    : filteredHospitals.length}{" "}
                  healthcare facilities found in your area
                </p>
                <div className="flex gap-2">
                  <button
                    onClick={() => setIsGridView(true)}
                    className={`p-2 rounded-lg transition-colors duration-200 ${
                      isGridView
                        ? "bg-gray-900 text-white"
                        : "bg-white text-gray-900"
                    }`}
                  >
                    <LayoutDashboard size={20} />
                  </button>

                  <button
                    onClick={() => setIsGridView(false)}
                    className={`p-2 rounded-lg transition-colors duration-200 ${
                      !isGridView
                        ? "bg-gray-900 text-white"
                        : "bg-white text-gray-900"
                    }`}
                  >
                    <Menu size={20} />
                  </button>
                </div>
              </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              {/* Hospitals Container */}
              <div
                className={`flex-1 overflow-y-auto max-h-screen px-4 ${
                  !isGridView ? "space-y-4  " : "space-y-4"
                }`}
              >
                <div className="flex-1">
                  <div className="flex flex-col">
                    <div className="flex justify-between items-center bg-gray-100 border border-gray-300 rounded-lg shadow-sm p-4 mb-4">
                      <p className="text-gray-700">
                        Click the{" "}
                        <span className="font-semibold text-green-600">
                          "Direction"
                        </span>{" "}
                        button to view the facility's location on the map, or
                        Click{" "}
                        <span className="font-semibold text-green-600">
                          "Details"
                        </span>{" "}
                        to learn more about the facility.
                      </p>
                    </div>
                  </div>
                  {!isGridView ? (
                    // ✅ TABLE VIEW
                    <div className="overflow-x-auto w-full">
                      <table className="min-w-full bg-white border border-gray-300 rounded-lg shadow-sm text-sm">
                        <thead className="bg-gray-100 text-gray-700">
                          <tr>
                            <th className="px-4 py-2 border-b text-left">
                              Facility Name
                            </th>
                            <th className="px-4 py-2 border-b text-left">
                              Actions
                            </th>
                          </tr>
                        </thead>
                        <tbody className="space-y-2">
                          {paginatedHospitals.map((hospital) => {
                            const imageUrl =
                              Array.isArray(hospital.image_url) &&
                              hospital.image_url.length > 0
                                ? hospital.image_url[0]
                                : "/gh1.svg";

                            return (
                              <tr
                                key={hospital.id}
                                className="border-t hover:bg-gray-50 transition-colors"
                              >
                                <td className="px-4 py-3 relative group cursor-pointer">
                                  <span className="font-semibold text-gray-900">
                                    {hospital.facility_name ?? "N/A"}
                                  </span>
                                  <br />
                                  <span className="text-sm text-gray-500">
                                    {/* {hospital.physical_location ?? "N/A"} */}
                                    {hospital.physical_location
                                      ? `${hospital.physical_location} ${hospital.ward_name}, ${hospital.lga_name}, ${hospital.state_name}`
                                      : `${hospital.ward_name}, ${hospital.lga_name}, ${hospital.state_name}`}
                                  </span>
                                  {/* Tooltip */}
                                  {/* Tooltip */}
                                  <div className="absolute left-4 bottom-full mb-2 hidden group-hover:block bg-gray-800 text-white text-xs rounded-md px-3 py-1 shadow-lg z-10 whitespace-nowrap">
                                    Click buttons to view map or facility
                                    details
                                  </div>
                                </td>
                                <td className="px-4 py-3 flex space-x-2">
                                  <button
                                    onClick={() =>
                                      handleGetDirections(hospital)
                                    }
                                    className="flex items-center text-green-600 font-semibold px-2 py-1 border border-green-600 rounded hover:bg-green-100"
                                  >
                                    {/* Map Icon SVG */}
                                    <svg
                                      xmlns="http://www.w3.org/2000/svg"
                                      className="h-5 w-5 mr-2"
                                      fill="none"
                                      viewBox="0 0 24 24"
                                      stroke="currentColor"
                                    >
                                      <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M12 11c.5304 0 1.0391-.2107 1.4142-.5858C13.7893 10.0391 14 9.5304 14 9s-.2107-1.0391-.5858-1.4142C13.0391 7.2107 12.5304 7 12 7s-1.0391.2107-1.4142.5858C10.2107 7.9609 10 8.4696 10 9s.2107 1.0391.5858 1.4142C10.9609 10.7893 11.4696 11 12 11z"
                                      />
                                      <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"
                                      />
                                    </svg>
                                    Direction
                                  </button>

                                  <Link
                                    href={`/facilityfinder/details/${hospital.id}`}
                                    className="flex items-center text-green-600 font-semibold px-2 py-1 border border-green-600 rounded hover:bg-green-100"
                                    onClick={() => {
                                      const searchState = {
                                        search,
                                        facilityType: selectedFacilityType,
                                        facilityLevel: selectedFacilityLevel,
                                      };
                                      localStorage.setItem(
                                        "facilitySearchState",
                                        JSON.stringify(searchState)
                                      );
                                    }}
                                  >
                                    {/* Info Icon SVG */}
                                    <svg
                                      xmlns="http://www.w3.org/2000/svg"
                                      className="h-5 w-5 mr-2"
                                      fill="none"
                                      viewBox="0 0 24 24"
                                      stroke="currentColor"
                                    >
                                      <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M13 16h-1v-4h-1m1-4h.01M12 4.5C7.857 4.5 4.5 7.857 4.5 12S7.857 19.5 12 19.5 19.5 16.143 19.5 12 16.143 4.5 12 4.5z"
                                      />
                                    </svg>
                                    Details
                                  </Link>
                                </td>
                              </tr>
                            );
                          })}
                        </tbody>
                      </table>
                    </div>
                  ) : (
                    // ✅ LIST CARD VIEW (your original design)
                    <>
                      {/* {currentHospitals.map((hospital) => { */}
                      {paginatedHospitals.map((hospital) => {
                        const parsedImages = JSON.parse(
                          (hospital as any)?.image_url || "[]"
                        );

                        const imageUrl =
                          Array.isArray(parsedImages) && parsedImages.length > 0
                            ? parsedImages[0] // Get the first image
                            : "/gh1.svg"; //

                        return (
                          <Card key={hospital.id} className="mb-4">
                            <div className="p-6 bg-gray-100 border border-gray-300 rounded-lg shadow-sm w-full">
                              <div className="flex flex-col md:flex-row gap-4 items-start">
                                {/* Image */}
                                <div className="w-full md:w-[250px] h-[250px] flex items-center">
                                  <Image
                                    src={imageUrl}
                                    width={250}
                                    height={150}
                                    alt={hospital.facility_name ?? "N/A"}
                                    className="object-cover w-full h-full rounded-lg"
                                  />
                                </div>

                                {/* Details */}
                                <div className="flex flex-col gap-4 md:pl-4">
                                  <h2 className="text-lg font-semibold relative group cursor-pointer">
                                    {hospital.facility_name ?? "N/A"}
                                    {/* Tooltip */}
                                    <div className="absolute left-4 -translate-x-1/2 bottom-full mb-2 hidden group-hover:block bg-gray-800 text-white text-xs rounded-md px-3 py-1 shadow-lg z-10 whitespace-nowrap">
                                      Click buttons to view map or facility
                                      details
                                    </div>
                                  </h2>
                                  {/* <p className="text-sm text-gray-600">
                                    {hospital.physical_location ??
                                      `${
                                        (hospital.ward_name,
                                        hospital.lga_name,
                                        hospital.state_name)
                                      }`}
                                  </p> */}

                                  <p className="text-sm flex items-center gap-2 text-gray-700">
                                    <FaMapMarkerAlt className="text-red-500" />
                                    {hospital.physical_location
                                      ? `${hospital.physical_location} ${hospital.ward_name}, ${hospital.lga_name}, ${hospital.state_name}`
                                      : `${hospital.ward_name}, ${hospital.lga_name}, ${hospital.state_name}`}
                                  </p>

                                  <p className="text-sm flex items-center gap-2 text-gray-700">
                                    <FaPhoneAlt className="text-blue-500" />
                                    {hospital.phone_number &&
                                    hospital.phone_number !== "null" &&
                                    hospital.phone_number.trim() !== ""
                                      ? hospital.phone_number
                                      : "N/A"}
                                  </p>

                                  {/* <p className="text-sm text-gray-600">
                                    Plans accepted: EPO, HMO, Medi-Cal Managed
                                    Care, POS, Senior Advantage
                                    {hospital.description ?? "N/A"}
                                  </p> */}

                                  <p className="text-sm text-gray-600 flex items-center gap-2">
                                    <FaInfoCircle className="text-blue-500" />
                                    {hospital.description ?? "N/A"}
                                  </p>

                                  <div className="w-full flex flex-wrap md:flex-nowrap gap-2 py-4">
                                    {/* <button
                                      className="text-green-600 font-semibold text-center w-full md:w-auto text-sm"
                                      onClick={() =>
                                        handleGetDirections(hospital)
                                      }
                                    >
                                      View Direction
                                    </button>
                                    <Link
                                      href={`/facilityfinder/details/${hospital.id}`}
                                      className="text-green-600 font-semibold text-center w-full md:w-auto text-sm"
                                    >
                                      View Details
                                    </Link> */}

                                    <button
                                      onClick={() =>
                                        handleGetDirections(hospital)
                                      }
                                      className="flex items-center text-green-600 font-semibold px-2 py-1 border border-green-600 rounded hover:bg-green-100"
                                    >
                                      {/* Map Icon SVG */}
                                      <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        className="h-5 w-5 mr-2"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                      >
                                        <path
                                          strokeLinecap="round"
                                          strokeLinejoin="round"
                                          strokeWidth={2}
                                          d="M12 11c.5304 0 1.0391-.2107 1.4142-.5858C13.7893 10.0391 14 9.5304 14 9s-.2107-1.0391-.5858-1.4142C13.0391 7.2107 12.5304 7 12 7s-1.0391.2107-1.4142.5858C10.2107 7.9609 10 8.4696 10 9s.2107 1.0391.5858 1.4142C10.9609 10.7893 11.4696 11 12 11z"
                                        />
                                        <path
                                          strokeLinecap="round"
                                          strokeLinejoin="round"
                                          strokeWidth={2}
                                          d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"
                                        />
                                      </svg>
                                      Direction
                                    </button>

                                    <Link
                                      href={`/facilityfinder/details/${hospital.id}`}
                                      className="flex items-center text-green-600 font-semibold px-2 py-1 border border-green-600 rounded hover:bg-green-100"
                                      onClick={() => {
                                        const searchState = {
                                          search,
                                          facilityType: selectedFacilityType,
                                          facilityLevel: selectedFacilityLevel,
                                        };
                                        localStorage.setItem(
                                          "facilitySearchState",
                                          JSON.stringify(searchState)
                                        );
                                      }}
                                    >
                                      {/* Info Icon SVG */}
                                      <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        className="h-5 w-5 mr-2"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                      >
                                        <path
                                          strokeLinecap="round"
                                          strokeLinejoin="round"
                                          strokeWidth={2}
                                          d="M13 16h-1v-4h-1m1-4h.01M12 4.5C7.857 4.5 4.5 7.857 4.5 12S7.857 19.5 12 19.5 19.5 16.143 19.5 12 16.143 4.5 12 4.5z"
                                        />
                                      </svg>
                                      Details
                                    </Link>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </Card>
                        );
                      })}
                    </>
                  )}

                  {/* Pagination Controls */}
                  <div className="flex justify-center items-center gap-4 mt-6">
                    <button
                      onClick={() =>
                        setCurrentPage((prev) => Math.max(prev - 1, 1))
                      }
                      disabled={currentPage === 1}
                      className={`px-4 py-2 border rounded ${
                        currentPage === 1
                          ? "opacity-50 cursor-not-allowed"
                          : "hover:bg-gray-100"
                      }`}
                    >
                      Previous
                    </button>

                    <span className="text-gray-600">
                      Page {currentPage} of {totalPages}
                    </span>

                    <button
                      onClick={() =>
                        setCurrentPage((prev) => Math.min(prev + 1, totalPages))
                      }
                      disabled={currentPage === totalPages}
                      className={`px-4 py-2 border rounded ${
                        currentPage === totalPages
                          ? "opacity-50 cursor-not-allowed"
                          : "hover:bg-gray-100"
                      }`}
                    >
                      Next
                    </button>
                  </div>
                </div>
              </div>

              <div className="h-[670px]  min-h-[300px] rounded-lg overflow-hidden">
                <LoadScriptNext
                  googleMapsApiKey={
                    process.env.NEXT_PUBLIC_GOOGLE_MAP_API ?? ""
                  }
                  libraries={libraries}
                  onLoad={() => setGoogleMapsLoaded(true)}
                >
                  <GoogleMap
                    mapContainerClassName="w-full h-full"
                    // center={center} // initial
                    center={
                      selectedHospital
                        ? {
                            lat: selectedHospital.latitude,
                            lng: selectedHospital.longitude,
                          }
                        : userLocation || center
                    } // Conditionally update center
                    // zoom={selectedHospital ? 10 : 10} // fallback zoom
                    zoom={selectedHospital ? 12 : 12}
                    // onLoad={(map) => {
                    //   mapRef.current = map;
                    // }}

                    onLoad={(map) => {
                      mapRef.current = map;

                      if (hospitalsWithCoordinates.length > 0) {
                        const bounds = new window.google.maps.LatLngBounds();

                        // Extend bounds for each hospital
                        hospitalsWithCoordinates.forEach((hospital) => {
                          bounds.extend({
                            lat: hospital.latitude,
                            lng: hospital.longitude,
                          });
                        });

                        // Optionally include user location if you want
                        if (
                          userLocation &&
                          !isNaN(userLocation.lat) &&
                          !isNaN(userLocation.lng)
                        ) {
                          bounds.extend(userLocation);
                        }

                        map.fitBounds(bounds); // Adjust map to show all markers
                      }
                    }}
                  >
                    {/* Only render if Google Maps is fully loaded */}
                    {googleMapsLoaded &&
                      userLocation &&
                      !isNaN(userLocation.lat) &&
                      !isNaN(userLocation.lng) && (
                        <>
                          <Marker
                            position={userLocation}
                            icon={{
                              url:
                                "data:image/svg+xml;charset=UTF-8," +
                                encodeURIComponent(`
                                  <svg xmlns="http://www.w3.org/2000/svg" width="80" height="40">
                                    <rect x="0" y="0" width="80" height="30" rx="5" ry="5" fill="#2563EB"/>
                                    <text x="40" y="20" font-size="14" fill="white" text-anchor="middle" font-weight="bold">You</text>
                                  </svg>
                                `),
                              scaledSize: new window.google.maps.Size(80, 40),
                            }}
                            onClick={() => setShowUserInfo(true)} // 👈 Show card again on click
                          />

                          {showUserInfo && userAddress && (
                            <InfoWindow
                              position={userLocation}
                              onCloseClick={() => setShowUserInfo(false)} // 👈 Close card
                            >
                              <div className="p-2 max-w-xs">
                                <h3 className="font-bold mb-2">You</h3>
                                <p className="text-sm text-gray-600 break-words">
                                  {userAddress}
                                </p>
                              </div>
                            </InfoWindow>
                          )}
                        </>
                      )}

                    {/* Show all hospitals if no specific facility is selected */}
                    {!focusedHospital &&
                      hospitalsWithCoordinates.map((hospital) => (
                        <Marker
                          key={hospital.id}
                          position={{
                            lat: hospital.latitude,
                            lng: hospital.longitude,
                          }}
                          onClick={() => {
                            setSelectedHospital(hospital);
                            setFocusedHospital(hospital); // 👈 This is important
                          }}
                        />
                      ))}

                    {/* Show only the selected facility */}
                    {focusedHospital &&
                      focusedHospital.latitude !== null &&
                      focusedHospital.longitude !== null && (
                        <Marker
                          position={{
                            lat: focusedHospital.latitude,
                            lng: focusedHospital.longitude,
                          }}
                          // onClick={() => setSelectedHospital(focusedHospital)}
                          onClick={() => {
                            setSelectedHospital(focusedHospital); // Set which hospital was clicked
                            setShowHospitalInfoCard(true); // Don't show popup until user clicks button
                          }}
                        />
                      )}

                    {/* Route Line */}
                    {directions && (
                      <DirectionsRenderer
                        directions={directions}
                        options={{ suppressMarkers: true }}
                      />
                    )}
                    {/* 🏥 Facility InfoWindow */}
                    {selectedHospital && showHospitalInfoCard && (
                      <InfoWindow
                        position={{
                          lat: selectedHospital.latitude,
                          lng: selectedHospital.longitude,
                        }}
                        onCloseClick={() => setSelectedHospital(null)}
                      >
                        <div className="p-2">
                          <h3 className="font-bold mb-2">
                            {selectedHospital.facility_name}
                          </h3>
                          <p className="text-sm mb-1">
                            {selectedHospital.physical_location}
                          </p>
                          {selectedHospital.phone_number && (
                            <p className="text-sm text-blue-600">
                              {selectedHospital.phone_number}
                            </p>
                          )}
                        </div>
                      </InfoWindow>
                    )}
                  </GoogleMap>
                </LoadScriptNext>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

export default Facility;
