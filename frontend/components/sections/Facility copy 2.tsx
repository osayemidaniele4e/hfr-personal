"use client";

import { Button, Card } from "@chakra-ui/react";
import { CiSliderHorizontal } from "react-icons/ci";
import { MdLocationPin } from "react-icons/md";
import { IoCopy } from "react-icons/io5";
import Input from "../ui/Input";
import SelectComponent from "../ui/SelectComponent";
import { GreenButton, Text, WhiteButton } from "../ui/Typography";

import { LoadScriptNext } from "@react-google-maps/api";

import React, { useState, useRef, useCallback, useEffect } from "react";
import {
  LoadScript,
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

import { useRouter } from "next/navigation";

const libraries: "places"[] = ["places"];
const itemsPerPage = 2;

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


const getLocationFromGoogleAPI = async () => {
  const apiKey = process.env.NEXT_PUBLIC_GOOGLE_MAP_API;

  const requestData = {
    considerIp: true,
    // wifiAccessPoints: [
    //   {
    //     macAddress: "00:11:22:33:44:55",
    //     signalStrength: -65,
    //     signalToNoiseRatio: 40,
    //   },
    // ],
  };

  try {
    const response = await axios.post(
      `https://www.googleapis.com/geolocation/v1/geolocate?key=${apiKey}`,
      requestData,
      {
        headers: {
          "Content-Type": "application/json",
        },
      }
    );

    console.log("Location:", response.data.location);
    return response.data.location;
  } catch (error) {
    // console.error(
    //   "Error fetching location:",
    //   error?.response?.data || error?.message
    // );
  }
};

function Facility() {
  const router = useRouter();

  const [map, setMap] = useState<google.maps.Map | null>(null);

  const [searchBox, setSearchBox] =
    useState<google.maps.places.SearchBox | null>(null);

  const [hospitals, setHospitals] = useState<any[]>([]);

  const [selectedHospital, setSelectedHospital] = useState<any | null>(null);

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

  // const [directions, setDirections] = useState(null);
  // const [directions, setDirections] = useState<null>(null);
  const [directions, setDirections] =
    useState<google.maps.DirectionsResult | null>(null);

  const [directionsRenderer, setDirectionsRenderer] = useState(null);

  const [directionsService, setDirectionsService] =
    useState<google.maps.DirectionsService | null>(null);

  // const [userLocation, setUserLocation] =
  //   useState<google.maps.LatLngLiteral | null>(null);

  const [userLocation, setUserLocation] = useState(defaultLocation);

  // Initialize Google Directions Service
  // const onMapLoad = (map: any) => {
  //   setMap(map);
  //   setDirectionsService(() => new window.google.maps.DirectionsService());
  // };

  /////////////////////////////////////////////////////////////////////////////////////////////////

  // 📍 Detect User’s Initial Location (Runs Once)
  useEffect(() => {
    if (!navigator.geolocation) {
      console.warn("⚠️ Geolocation is not supported");
      getLocationFromGoogleAPI().then(setCenter);
      return;
    }

    navigator.geolocation.getCurrentPosition(
      (position) => {
        console.log("📍 Accurate Location:", position.coords);
        const newLocation = {
          lat: position.coords.latitude,
          lng: position.coords.longitude,
        };
        setUserLocation(newLocation);
        setCenter(newLocation);
      },
      (error) => {
        console.error("🚨 Location Error:", error);
        getLocationFromGoogleAPI().then(setCenter);
      },
      {
        enableHighAccuracy: true,
        timeout: 15000,
        maximumAge: 0,
      }
    );
  }, []);

  // 2️⃣ 🛰️ Continuously Track User’s Location (Watches for Changes)
  // 🛰️ Continuously Track User’s Location
  useEffect(() => {
    const watchId = navigator.geolocation.watchPosition(
      (position) => {
        setUserLocation({
          lat: position.coords.latitude,
          lng: position.coords.longitude,
        });
        console.log("📍 Updated Location:", position.coords);
      },
      (error) => console.error("❌ Error tracking location:", error),
      { enableHighAccuracy: true, maximumAge: 0, timeout: 5000 }
    );

    return () => navigator.geolocation.clearWatch(watchId); // Cleanup
  }, []);

  // 3️⃣ 🗺️ Update Map & Get Directions When a Hospital is Selected
  // 🗺️ Update Map & Get Directions When a Hospital is Selected
  useEffect(() => {
    if (!selectedHospital || !userLocation) return;

    setCenter({
      lat: selectedHospital.latitude,
      lng: selectedHospital.longitude,
    });

    const directionsService = new window.google.maps.DirectionsService();
    directionsService.route(
      {
        origin: userLocation,
        destination: {
          lat: selectedHospital.latitude,
          lng: selectedHospital.longitude,
        },
        travelMode: google.maps.TravelMode.DRIVING,
      },
      (result, status) => {
        if (status === google.maps.DirectionsStatus.OK) {
          setDirections(result);
        } else {
          console.error("❌ Error fetching directions:", status);
        }
      }
    );
  }, [selectedHospital, userLocation]);

  // 🗺️ Initialize Google Directions Service
  // 🗺️ Initialize Google Directions Service
  const onMapLoad = (mapInstance: any) => {
    setMap(mapInstance);
  };

  // 🏥 Set Map Center to the First Search Result
  useEffect(() => {
    if (hospitals.length > 0) {
      setCenter({
        lat: hospitals[0].latitude,
        lng: hospitals[0].longitude,
      });
    }
  }, [hospitals]);

  /////////////////////////////////////////////////////////////////////////////////////////////////

  const handleGetDirections = (hospital: any) => {
    if (!userLocation) {
      console.error("User location not available yet.");
      return;
    }

    if (!hospital || !hospital.latitude || !hospital.longitude) {
      console.error("Invalid hospital data:", hospital);
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
        } else {
          console.error("Directions request failed:", status);
        }
      }
    );
  };

  // Calculate total pages
  const totalPages = Math.ceil(hospitals.length / itemsPerPage);

  // Get hospitals for the current page
  const indexOfLastItem = currentPage * itemsPerPage;
  const indexOfFirstItem = indexOfLastItem - itemsPerPage;
  const currentHospitals = hospitals.slice(indexOfFirstItem, indexOfLastItem);

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
        const requestBody: any = {};
        if (searchValues.facilityLevel)
          requestBody.facility_level_id = searchValues.facilityLevel;
        if (searchValues.facilityType)
          requestBody.facility_type_id = searchValues.facilityType;
        if (searchValues.search)
          requestBody.facility_name = searchValues.search;

        const response = await axios.post(
          `${process.env.NEXT_PUBLIC_BACKEND_API}/facilities-hospitals-search3`,
          requestBody
        );

        const fetchedFacilities = response.data?.data?.facilities || [];

        console.log(response.data?.data);

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

        console.log("Valid Facilities:", validFacilities); // Debugging

        console.log(validFacilities.length);

        setHospitals(validFacilities);
        // setTotalPages(response.data?.data?.facilities?.last_page);
        setSelectedHospital(null);
      } catch (error) {
        console.error("Error fetching data:", error);
      } finally {
        setLoading(false);
      }
    },
    []
  );

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

  useEffect(() => {
    // Retrieve search results from localStorage
    const storedResults: Facility[] = JSON.parse(
      localStorage.getItem("searchResults") || "[]"
    );

    if (storedResults.length > 0) {
      // console.log("Using localStorage data for hospitals.");

      // Ensure latitude and longitude are valid numbers
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

      setHospitals(validFacilities);
      // localStorage.removeItem("searchResults"); // Uncomment if you want to clear storage
    } else {
      console.log("Fetching from API because localStorage is empty.");
      fetchFacilities();
    }
  }, [fetchFacilities]);

  const handleSearch = async () => {
    setLoading(true);

    try {
      await fetchFacilities({
        search,
        facilityType: selectedFacilityType,
        facilityLevel: selectedFacilityLevel,
      });

      // Optional delay (only if needed)
      await new Promise((resolve) => setTimeout(resolve, 2000));
    } catch (error) {
      console.error("Error fetching facilities:", error);
    } finally {
      setLoading(false);
    }

    localStorage.removeItem("searchResults");
  };

  useEffect(() => {
    const handleRouteChange = () => {
      console.log(
        "Navigation detected, clearing searchResults from localStorage."
      );
      localStorage.removeItem("searchResults");
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

  return (
    <div className="min-h-screen bg-gray-50">
      <div className="container mx-auto p-4">
        <div className="bg-white rounded-lg shadow-sm p-6">
          <h1 className="text-lg font-medium text-gray-800 mb-6">
            Search based on location
          </h1>

          <div className="space-y-4">
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-8 gap-4">
              {/* 🔍 Search Location */}
              <div className="relative lg:col-span-2">
                <input
                  ref={searchInputRef}
                  type="text"
                  placeholder="Enter location"
                  value={search}
                  onChange={(e) => setSearch(e.target.value)}
                  className="w-full p-3 pr-10 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                />
                {/* <Search className="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400" /> */}
              </div>

              {/* 🏥 Facility Type */}
              <div className="lg:col-span-2">
                <select
                  value={selectedFacilityType}
                  onChange={(e) => setSelectedFacilityType(e.target.value)}
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
                  onChange={(e) => setSelectedFacilityLevel(e.target.value)}
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
                  {loading ? "Loading..." : "Search Location"}
                </GreenButton>
              </div>
            </div>

            <div className="flex justify-between items-center bg-green-50 p-4 rounded-lg">
              <p className="text-gray-700">
                {hospitals.length} healthcare facilities found in your area
              </p>
              <div className="flex gap-2">
                <button className="p-2 bg-gray-900 text-white rounded-lg">
                  <Menu size={20} />
                </button>
                <button className="p-2 bg-white text-gray-900 rounded-lg">
                  <LayoutDashboard size={20} />
                </button>
              </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <div className="flex-1">
                {currentHospitals.map((hospital) => {
                  const imageUrl =
                    Array.isArray(hospital.image_url) &&
                    hospital.image_url.length > 0
                      ? hospital.image_url[0]
                      : "/gh1.svg";

                  return (
                    <Card key={hospital.id} className="mb-4 p-8">
                      <div className="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                        {/* Image */}
                        <div className="w-full h-[250px] flex items-center">
                          <Image
                            src={imageUrl}
                            width={250}
                            height={150}
                            alt={hospital.facility_name ?? "N/A"}
                            className="object-cover w-full h-full rounded-lg"
                          />
                        </div>

                        {/* Details */}
                        <div className="flex flex-col gap-4">
                          <h2 className="text-lg font-semibold">
                            {hospital.facility_name ?? "N/A"}
                          </h2>
                          <p className="text-sm text-gray-600">
                            {hospital.address ?? "N/A"}
                          </p>
                          <p className="text-sm text-gray-600">
                            Contact info: {hospital.phone_number ?? "N/A"}
                          </p>
                          <p className="text-sm text-gray-600">
                            Plans accepted: EPO, HMO, Medi-Cal Managed Care,
                            POS, Senior Advantage
                          </p>

                          {/* Buttons */}
                          {/* <div className="flex space-x-4">
                            <a
                              href="#"
                              className="text-green-600 font-semibold text-center"
                              onClick={() => handleGetDirections(hospital)}
                            >
                              View Direction
                            </a>

                            <a
                              href="#"
                              className="text-green-600 font-semibold text-center"
                              target="__blank"
                              onClick={(e) => {
                                e.preventDefault();
                                router.push(
                                  `/facilityfinder/details/${hospital.id}`
                                );
                              }}
                            >
                              View Details
                            </a>
                          </div> */}

                          {/* Buttons */}
                          <div className="grid grid-cols-1 md:flex md:space-x-4 gap-2 w-full">
                            <a
                              href="#"
                              className="text-green-600 font-semibold text-center w-full md:w-auto"
                              onClick={() => handleGetDirections(hospital)}
                            >
                              View Direction
                            </a>

                            <a
                              href="#"
                              className="text-green-600 font-semibold text-center w-full md:w-auto"
                              target="__blank"
                              onClick={(e) => {
                                e.preventDefault();
                                router.push(
                                  `/facilityfinder/details/${hospital.id}`
                                );
                              }}
                            >
                              View Details
                            </a>
                          </div>
                        </div>
                      </div>
                    </Card>
                  );
                })}

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

              <div className="h-[600px] rounded-lg overflow-hidden">
                <LoadScriptNext
                  googleMapsApiKey={
                    process.env.NEXT_PUBLIC_GOOGLE_MAP_API ?? ""
                  }
                  libraries={libraries}
                >
                  <GoogleMap
                    mapContainerClassName="w-full h-full"
                    center={center}
                    zoom={selectedHospital ? 14 : 10}
                    onLoad={onMapLoad}
                  >
                    {/* User's Location Marker */}
                    {userLocation &&
                      !isNaN(userLocation.lat) &&
                      !isNaN(userLocation.lng) && (
                        <Marker position={userLocation} label="You" />
                      )}

                    {/* Show all hospitals if no specific facility is selected */}
                    {!selectedHospital &&
                      hospitals.map((hospital) => (
                        <Marker
                          key={hospital.id}
                          position={{
                            lat: hospital.latitude,
                            lng: hospital.longitude,
                          }}
                          onClick={() => setSelectedHospital(hospital)}
                        />
                      ))}

                    {/* Show only the selected facility */}
                    {selectedHospital && (
                      <Marker
                        position={{
                          lat: selectedHospital.latitude,
                          lng: selectedHospital.longitude,
                        }}
                      />
                    )}

                    {/* Route Line */}
                    {directions && (
                      <DirectionsRenderer directions={directions} />
                    )}

                    {/* 🏥 Facility InfoWindow */}
                    {selectedHospital && (
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
                            {selectedHospital.address}
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
