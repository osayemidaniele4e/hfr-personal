"use client";
import Image from "next/image";
import React, { useCallback, useEffect, useRef, useState } from "react";
import { Text } from "../ui/Typography";
import { MdLocationPin } from "react-icons/md";
import { FaRegShareSquare, FaDownload } from "react-icons/fa";
import { HiChevronDown, HiDownload, HiShare } from "react-icons/hi";
import { usePathname, useRouter, useSearchParams } from "next/navigation";
import axios from "axios";
import { ImSpinner2 } from "react-icons/im";

import html2canvas from "html2canvas";
import { jsPDF } from "jspdf";

import "swiper/css";
import "swiper/css/navigation";

import Lightbox from "react-image-lightbox";
import "react-image-lightbox/style.css"; // Import the lightbox styles
import Swal from "sweetalert2";

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

function missingFieldLabel(
  column: string,
  labels: Record<string, string> | undefined
): string {
  const fromApi = labels?.[column];
  if (fromApi) return fromApi;
  return column
    .split("_")
    .map((w) => w.charAt(0).toUpperCase() + w.slice(1).toLowerCase())
    .join(" ");
}

const FacilityDetails = () => {
  const router = useRouter();
  const pathname = usePathname();

  const searchParams = useSearchParams();
  const id = searchParams?.get("id"); // Get the hospital ID from URL

  // const [hospital, setHospital] = useState(null);
  const [hospital, setHospital] = useState<Facility | null>(null);

  const [fetchError, setFetchError] = useState<string>(""); // State for error messages

  const [completeness, setCompleteness] = useState<{
    percentage: number;
    missing_fields: string[];
    missing_field_labels?: Record<string, string>;
    total_columns?: number;
    filled_columns?: number;
    missing_count?: number;
  } | null>(null);
  const [completenessLoading, setCompletenessLoading] = useState(false);
  const [completenessError, setCompletenessError] = useState(false);
  const [missingFieldsOpen, setMissingFieldsOpen] = useState(false);

  useEffect(() => {
    if (completenessLoading) {
      setMissingFieldsOpen(false);
    }
  }, [completenessLoading]);

  const fetchCompleteness = useCallback(async (facilityId: string) => {
    if (!facilityId || !/^\d+$/.test(facilityId)) {
      setCompleteness(null);
      setCompletenessError(false);
      setCompletenessLoading(false);
      return;
    }
    setCompletenessLoading(true);
    setCompletenessError(false);
    try {
      const res = await axios.get(
        `${process.env.NEXT_PUBLIC_BACKEND_API}/facility/${facilityId}/completeness`
      );
      const payload = res?.data?.data;
      const rawLabels = payload?.missing_field_labels;
      const missingFieldLabels =
        rawLabels &&
        typeof rawLabels === "object" &&
        !Array.isArray(rawLabels)
          ? (rawLabels as Record<string, string>)
          : undefined;
      if (
        res?.data?.success &&
        payload &&
        typeof payload.percentage === "number" &&
        Array.isArray(payload.missing_fields)
      ) {
        const totalColumns =
          typeof payload.total_columns === "number"
            ? payload.total_columns
            : undefined;
        const filledColumns =
          typeof payload.filled_columns === "number"
            ? payload.filled_columns
            : undefined;
        const missingCount =
          typeof payload.missing_count === "number"
            ? payload.missing_count
            : undefined;
        setCompleteness({
          percentage: payload.percentage,
          missing_fields: payload.missing_fields,
          missing_field_labels: missingFieldLabels,
          total_columns: totalColumns,
          filled_columns: filledColumns,
          missing_count: missingCount,
        });
      } else {
        setCompleteness(null);
        setCompletenessError(true);
      }
    } catch {
      setCompleteness(null);
      setCompletenessError(true);
    } finally {
      setCompletenessLoading(false);
    }
  }, []);

  const [modalIsOpen, setModalIsOpen] = useState(false);
  const [selectedImage, setSelectedImage] = useState(null);

  const [isOpen, setIsOpen] = useState(false);
  const [currentIndex, setCurrentIndex] = useState(0);
  const [mainSrc, setMainSrc] = useState("");

  const getAFacility = useCallback(
    async (facilityId: string) => {
      try {
        const response = await axios.get(
          `${process.env.NEXT_PUBLIC_BACKEND_API}/facilities-hospital/${facilityId}`
        );

        const data = response?.data?.data?.hospital;
        console.log("Fetched facility:", data);
        setHospital(data);
        await fetchCompleteness(facilityId);
      } catch (error) {
        console.error("Error fetching facility:", error);
        setFetchError("Failed to fetch facility details.");
        setHospital(null);
        setCompleteness(null);
        setCompletenessLoading(false);
        setCompletenessError(false);
      }
    },
    [fetchCompleteness]
  );

  useEffect(() => {
    const pathArray = window.location.pathname.split("/"); // Split URL by "/"
    const pathId = pathArray[pathArray.length - 1]; // Get the last part of the URL
    if (pathId) {
      getAFacility(pathId);
    }
  }, [id, getAFacility, pathname]); // Add getAFacility as a dependency

  // const parsedImages = JSON.parse(hospital?.image_url || "[]"); // Default to an empty array
  const parsedImages = JSON.parse((hospital as any)?.image_url || "[]");

  const imageUrl =
    Array.isArray(parsedImages) && parsedImages.length > 0
      ? parsedImages[0] // Get the first image
      : "/detailsImageOne.svg"; // Default placeholder image

  // console.log({ imageUrl });

  const openLightbox = (index: number) => {
    if (!parsedImages[index]) return;

    // If already open, close it first before re-opening
    if (isOpen) {
      setIsOpen(false);
      setTimeout(() => {
        setCurrentIndex(index);
        setMainSrc(parsedImages[index]);
        setIsOpen(true);
      }, 100); // Short delay to allow unmounting
    } else {
      setCurrentIndex(index);
      setMainSrc(parsedImages[index]);
      setIsOpen(true);
    }
  };

  const closeLightbox = () => {
    setIsOpen(false);
  };

  const nextImage = () => {
    setCurrentIndex((currentIndex + 1) % parsedImages.length);
  };

  const prevImage = () => {
    setCurrentIndex(
      currentIndex === 0 ? parsedImages.length - 1 : currentIndex - 1
    );
  };

  const pdfRef = useRef<HTMLDivElement>(null);

  const fixColorsBeforeCapture = () => {
    document.querySelectorAll("*").forEach((el) => {
      const computedStyle = window.getComputedStyle(el);
      if (computedStyle.backgroundColor.includes("oklch")) {
        (el as HTMLElement).style.backgroundColor = "white"; // Change to a safe color
      }
    });
  };

  const downloadPDF = () => {
    if (!pdfRef.current) return; // Prevent error

    // Hide buttons before taking the screenshot
    document.querySelectorAll(".no-print").forEach((el) => {
      (el as HTMLElement).style.visibility = "hidden";
    });

    fixColorsBeforeCapture(); // Fix colors before capturing
    html2canvas(pdfRef.current, { scale: 2 }).then((canvas) => {
      const imgData = canvas.toDataURL("image/jpeg");
      // const pdf = new jsPDF("p", "mm", "a4");
      const pdf = new jsPDF("l", "mm", "a4"); // "l" = Landscape mode
      const pageWidth = pdf.internal.pageSize.getWidth(); // Get landscape width
      const imgWidth = pageWidth - 40; // Reduce width to add padding (20mm left & right)
      const imgHeight = (canvas.height * imgWidth) / canvas.width;

      pdf.addImage(imgData, "JPEG", 20, 10, imgWidth, imgHeight); // 20mm padding from left
      pdf.save(`${hospital?.facility_name || "facility"}_details.pdf`);

      // Show buttons again after PDF is downloaded
      setTimeout(() => {
        document.querySelectorAll(".no-print").forEach((el) => {
          (el as HTMLElement).style.visibility = "visible";
        });
      }, 100); // Small delay to ensure smooth restoration
    });
  };

  const apiKey = process.env.NEXT_PUBLIC_GOOGLE_MAP_API; // Replace with your actual API key

  const getAccurateLocation = async () => {
    try {
      const response = await fetch(
        `https://www.googleapis.com/geolocation/v1/geolocate?key=${apiKey}`,
        { method: "POST" }
      );
      const data = await response.json();

      if (data.location) {
        return { latitude: data.location.lat, longitude: data.location.lng };
      } else {
        throw new Error("Failed to get accurate location");
      }
    } catch (error) {
      console.error("Google Geolocation API Error:", error);
      return null;
    }
  };

  const handleViewDirections = async (hospital: Facility | null) => {
    if (!hospital?.id || !hospital.latitude || !hospital.longitude) {
      Swal.fire({
        icon: "warning",
        title: "Invalid Facility",
        text: "Facility location is missing or invalid.",
        confirmButtonText: "Okay",
      });
      return;
    }

    Swal.fire({
      title: "Getting your location...",
      text: "Please wait while we fetch your location.",
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading(),
    });

    // First, try navigator.geolocation
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (position) => {
          Swal.close();

          const userLat = position.coords.latitude;
          const userLng = position.coords.longitude;
          const facilityLat = hospital.latitude;
          const facilityLng = hospital.longitude;

          const googleMapsUrl = `https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${facilityLat},${facilityLng}&travelmode=driving`;
          window.open(googleMapsUrl, "_blank");
        },
        async (error) => {
          console.warn("Geolocation Error:", error);

          // If browser geolocation fails, use Google Geolocation API
          const location = await getAccurateLocation();
          Swal.close();

          if (location) {
            const googleMapsUrl = `https://www.google.com/maps/dir/?api=1&origin=${location.latitude},${location.longitude}&destination=${hospital.latitude},${hospital.longitude}&travelmode=driving`;
            window.open(googleMapsUrl, "_blank");
          } else {
            Swal.fire({
              icon: "error",
              title: "Location Error",
              text: "Unable to get your location. Please enable GPS or try another browser.",
              confirmButtonText: "Okay",
            });
          }
        },
        {
          enableHighAccuracy: true,
          timeout: 10000,
          maximumAge: 0,
        }
      );
    } else {
      // If geolocation is not supported, use Google API directly
      const location = await getAccurateLocation();
      Swal.close();

      if (location) {
        const googleMapsUrl = `https://www.google.com/maps/dir/?api=1&origin=${location.latitude},${location.longitude}&destination=${hospital.latitude},${hospital.longitude}&travelmode=driving`;
        window.open(googleMapsUrl, "_blank");
      } else {
        Swal.fire({
          icon: "error",
          title: "Geolocation Not Supported",
          text: "Your browser does not support geolocation. Please enable location services or try another device.",
          confirmButtonText: "Okay",
        });
      }
    }
  };

  return (
    <div ref={pdfRef} className="flex flex-col gap-[1rem] lg:pt-32 mx-8 mb-8">
      <HospitalDetails hospital={hospital} downloadPDF={downloadPDF} />

      <div className="flex flex-col lg:flex-row gap-[2rem]">
        <div className="flex flex-col gap-[1rem]">
          <Image src={imageUrl} width={445} height={464} alt="img" />

          <Text
            className="underline text-[#5BBA62] cursor-pointer text-center no-print"
            onClick={() => handleViewDirections(hospital)}
          >
            <MdLocationPin fontSize={24} color="#5BBA62" className="inline" />{" "}
            View direction
          </Text>

          <div className="flex gap-[.5rem] gap-2">
            {parsedImages.slice(0, 2).map((img: any, index: number) => (
              <img
                key={index}
                src={img}
                width={95}
                height={131}
                alt={`Image ${index + 1}`}
                className="cursor-pointer rounded-md object-cover"
                onClick={() => openLightbox(index)}
              />
            ))}

            {parsedImages.length > 3 && (
              <div
                className="w-[120px] h-[120px] flex items-center justify-center bg-black/50 text-white text-lg cursor-pointer rounded-md"
                onClick={() => openLightbox(3)}
              >
                +{parsedImages.length - 3}
              </div>
            )}

            {isOpen && parsedImages.length > 0 && (
              <Lightbox
                key={currentIndex}
                mainSrc={parsedImages[currentIndex]}
                nextSrc={parsedImages[(currentIndex + 1) % parsedImages.length]}
                prevSrc={
                  parsedImages[
                    (currentIndex - 1 + parsedImages.length) %
                      parsedImages.length
                  ]
                }
                onCloseRequest={closeLightbox}
                onMovePrevRequest={prevImage}
                onMoveNextRequest={nextImage}
                reactModalStyle={{ overlay: { zIndex: 1050 } }}
              />
            )}
          </div>
        </div>
        <div className="border rounded-xl shadow-sm p-6 bg-white">
          <section
            className="mb-4 pb-4 border-b border-gray-100"
            aria-label="Profile completeness"
            aria-live="polite"
          >
            <h3 className="text-green-600 font-semibold mb-2">
              Profile completeness
            </h3>
            {completenessLoading ? (
              <div className="flex items-center justify-center py-4" role="status">
                <ImSpinner2 className="h-6 w-6 animate-spin text-green-600" />
                <span className="sr-only">Calculating profile completeness…</span>
              </div>
            ) : completenessError ? (
              <p className="text-sm text-red-700" role="alert">
                Unable to calculate completeness
              </p>
            ) : completeness ? (
              <div className="text-sm text-gray-800 space-y-3">
                <div className="flex items-center justify-between">
                  <span className="font-semibold text-gray-700">Data Completeness Score</span>
                  <span className={`px-2 py-0.5 rounded text-xs font-bold text-white ${
                    completeness.percentage >= 80 ? 'bg-green-600' : 
                    completeness.percentage >= 50 ? 'bg-green-500' : 
                    completeness.percentage >= 20 ? 'bg-amber-500' : 'bg-red-600'
                  }`}>
                    {completeness.percentage >= 80 ? 'EXCELLENT' : 
                     completeness.percentage >= 50 ? 'GOOD' : 
                     completeness.percentage >= 20 ? 'INCOMPLETE' : 'CRITICAL'}
                  </span>
                </div>
                  {completeness.percentage}%
                  {completeness.percentage === 100 ? (
                    <span className="ml-2 text-green-700 font-bold">✓ (Complete)</span>
                  ) : null}
                  {completeness.percentage === 0 &&
                  completeness.missing_fields.length > 0 ? (
                    <span className="ml-2 text-amber-800 font-medium">
                      (No data in scannable columns)
                    </span>
                  ) : null}


                {/* Visual Progress Bar */}
                <div className="w-full bg-gray-100 rounded-full h-4 my-2 overflow-hidden border border-gray-200">
                  <div
                    className={`h-full transition-all duration-1000 ease-out relative ${
                      completeness.percentage >= 80
                        ? "bg-green-600"
                        : completeness.percentage >= 50
                        ? "bg-green-500"
                        : completeness.percentage >= 20
                        ? "bg-amber-500"
                        : "bg-red-600 animate-pulse"
                    }`}
                    style={{ width: `${completeness.percentage}%` }}
                  >
                  </div>
                </div>
                <p className="text-center font-bold text-lg text-gray-800">{completeness.percentage}%</p>
                {typeof completeness.total_columns === "number" &&
                completeness.total_columns > 0 ? (
                  <p className="text-xs text-gray-600">
                    {typeof completeness.filled_columns === "number"
                      ? completeness.filled_columns
                      : completeness.total_columns -
                        completeness.missing_fields.length}{" "}
                    of {completeness.total_columns} scannable columns have a
                    value.
                  </p>
                ) : null}
                {completeness.missing_fields.length > 0 ? (
                  <div>
                    <button
                      type="button"
                      id="missing-fields-toggle"
                      className="flex w-full items-center justify-between gap-2 rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-left text-sm font-semibold text-gray-900 shadow-sm transition hover:bg-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-600"
                      onClick={() =>
                        setMissingFieldsOpen((open) => !open)
                      }
                      aria-expanded={missingFieldsOpen}
                      aria-controls="missing-fields-list"
                      aria-label={
                        missingFieldsOpen
                          ? `Hide list of ${completeness.missing_fields.length} missing fields`
                          : `Show list of ${completeness.missing_fields.length} missing fields`
                      }
                    >
                      <span>
                        Missing fields (
                        {typeof completeness.missing_count === "number"
                          ? completeness.missing_count
                          : completeness.missing_fields.length}
                        )
                      </span>
                      <HiChevronDown
                        className={`h-5 w-5 shrink-0 text-gray-500 transition-transform ${
                          missingFieldsOpen ? "rotate-180" : ""
                        }`}
                        aria-hidden
                      />
                    </button>
                    {missingFieldsOpen ? (
                      <ul
                        id="missing-fields-list"
                        className="mt-2 max-h-48 overflow-y-auto text-gray-800 list-disc pl-5 space-y-0.5 border border-gray-100 rounded-md p-2 bg-gray-50/80"
                        aria-labelledby="missing-fields-toggle"
                      >
                        {completeness.missing_fields.map((col) => (
                          <li key={col} className="text-sm py-1 border-b border-gray-50 last:border-0">
                            {missingFieldLabel(
                              col,
                              completeness.missing_field_labels
                            )}
                          </li>
                        ))}
                      </ul>
                    ) : null}
                  </div>
                ) : null}
              </div>
            ) : (
              <p className="text-sm text-gray-600">—</p>
            )}
          </section>
          {/* General Information */}
          <div className="mb-4">
            <h3 className="text-green-600 font-semibold mb-2">
              General Information
            </h3>
            <div className="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
              <p>
                <span className="font-semibold">Facility type:</span>{" "}
                {(hospital as any)?.facility_type_name || "N/A"}
              </p>
              <p>
                <span className="font-semibold">Ownership:</span>{" "}
                {(hospital as any)?.ownership_name || "N/A"}
              </p>
              <p>
                <span className="font-semibold">Certificate of Standard:</span>{" "}
                Yes
              </p>
              <p>
                <span className="font-semibold">Contact info:</span>{" "}
                {(hospital as any)?.phone_number || "N/A"}
              </p>
              <p>
                <span className="font-semibold">Unique ID:</span>{" "}
                {(hospital as any)?.unique_id || "N/A"}
              </p>
              <p>
                <span className="font-semibold">Facility Level:</span>{" "}
                {(hospital as any)?.facility_level_name || "N/A"}
              </p>
            </div>
          </div>

          {/* Location Details */}
          <div className="mb-4 border-t pt-4">
            <h3 className="text-green-600 font-semibold mb-2">
              Location details
            </h3>
            <div className="grid grid-cols-3 gap-4 text-sm">
              <p>
                <span className="font-semibold">State:</span>{" "}
                {(hospital as any)?.state_name || "N/A"}
              </p>
              <p>
                <span className="font-semibold">LGA:</span>{" "}
                {(hospital as any)?.lga_name || "N/A"}
              </p>
              <p>
                <span className="font-semibold">Ward:</span>{" "}
                {(hospital as any)?.ward_name || "N/A"}
              </p>
            </div>
          </div>

          {/* Facility Capacity Info */}
          <div className="mb-4 border-t pt-4">
            <h3 className="text-green-600 font-semibold mb-2">
              Facility Capacity info
            </h3>
            <div className="grid grid-cols-3 md:grid-cols-6 gap-4 text-sm">
              <p>
                <span className="font-semibold">No of Medical Doctors:</span>{" "}
                <br />
                {(hospital as any)?.doctors || "N/A"}
              </p>
              <p>
                <span className="font-semibold">No of Beds:</span> <br />
                {(hospital as any)?.beds || "N/A"}
              </p>
              <p>
                <span className="font-semibold">No of Midwives:</span> <br />
                {(hospital as any)?.nurse_midwife || "N/A"}
              </p>
              <p>
                <span className="font-semibold">No of Nurses:</span> <br />
                {(hospital as any)?.nurses || "N/A"}
              </p>
              <p>
                <span className="font-semibold">No of Resident Doctors:</span>{" "}
                <br />
                {(hospital as any)?.doctors || "N/A"}
              </p>
              <p>
                <span className="font-semibold">No of Health workers:</span>{" "}
                <br />
                {(hospital as any)?.community_health_officer || "N/A"}
              </p>
            </div>
          </div>

          {/* Plans Accepted */}
          <div className="border-t pt-4">
            <h3 className="text-green-600 font-semibold mb-2">
              Plans accepted
            </h3>
            <p className="text-sm">
              {/* Exclusive Provider Organization (EPO), HMO, Medi-Cal Managed Care,
              Point-of-Service Plan (POS), Senior Advantage. */}
              {(hospital as any)?.description || "N/A"}
            </p>
          </div>
        </div>
      </div>
    </div>
  );
};

export default FacilityDetails;

const HospitalDetails = ({
  hospital,
  downloadPDF,
}: {
  hospital: Facility | null;
  downloadPDF: () => void;
}) => {
  const [copied, setCopied] = useState(false);

  const shareLocation = async () => {
    try {
      const urlToShare = window.location.href; // Or you can specify any URL you want to share

      if (navigator.share) {
        // If Web Share API is available
        await navigator.share({
          title: "Check out this healthcare facility",
          text: "I found this healthcare facility in your area, check it out!",
          url: urlToShare,
        });
        console.log("Location shared successfully!");
      } else {
        // Fallback for browsers that don't support the Web Share API
        // Open a simple modal or link to share on platforms like FB, WhatsApp, etc.
        const fallbackUrl = `https://api.whatsapp.com/send?text=${encodeURIComponent(
          urlToShare
        )}`;
        window.open(fallbackUrl, "_blank");
      }
    } catch (error) {
      console.error("Error sharing location:", error);
    }
  };

  return (
    <div className="flex flex-col md:flex-row items-center justify-between p-4 border-b bg-white">
      <div>
        <p className="text-sm text-gray-500">
          <span className="font-semibold">Hospital Finder</span> / Hospital
          details
        </p>
        <div className="flex items-center gap-2 mt-1">
          <h1 className="text-2xl font-bold">{hospital?.facility_name}</h1>
          <span className="text-xs bg-blue-100 text-blue-600 px-3 py-2 rounded-full">
            {/* Open 24hrs */}
            Open{" "}
            {hospital?.operational_hours !== undefined &&
            hospital?.operational_hours !== null
              ? `${hospital?.operational_hours} hrs`
              : "N/A"}
          </span>
        </div>
        <p className="text-gray-500 text-sm flex items-center gap-1 mt-1">
          {/* 2417 Central Ave, Alameda, CA, 94501 */}
          {hospital?.physical_location
            ? hospital?.physical_location
            : `${hospital?.ward_name}, ${hospital?.lga_name}, ${hospital?.state_name}`}
          <span className="text-gray-400">📍</span>
        </p>
      </div>

      <div className="flex gap-2">
        <button
          className="flex no-print items-center gap-2 px-4 py-2 border rounded-lg text-gray-600 hover:bg-gray-100"
          onClick={shareLocation}
        >
          {/* <HiShare /> Share */}
          <HiShare /> {copied ? "Copied!" : "Share"}
        </button>
        <button
          className="flex no-print items-center gap-2 px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800"
          // onClick={() => downloadFacilityPDF(hospital)}
          onClick={downloadPDF}
        >
          <HiDownload /> Download
        </button>
      </div>
    </div>
  );
};
