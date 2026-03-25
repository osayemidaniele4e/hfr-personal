"use client";

import React, { useState } from "react";
import { MapPin, Info } from "lucide-react";
import {
  ComposableMap,
  Geographies,
  Geography,
  ZoomableGroup,
} from "react-simple-maps";
import nigeriaStates from "@/data/nigeriaStates";
import NigeriaMap from "./NigeriaMap";

function OverviewMap() {
  const [hoveredState, setHoveredState] = useState<string | null>(null);

  // Mock data for healthcare facilities per state
  const healthcareFacilities: { [key: string]: number } = {
    Lagos: 235,
    Kano: 185,
    FCT: 156,
    Rivers: 145,
    Oyo: 132,
  };

  const getColor = (stateName: string) => {
    const facilities = healthcareFacilities[stateName] || 0;
    if (facilities > 200) return "#1b4d3e";
    if (facilities > 150) return "#2d7a64";
    if (facilities > 100) return "#3c9d82";
    return "#7fb9aa";
  };

  return (
    <div className="min-h-screen bg-gray-50">
      <main className="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <div className="bg-white rounded-lg shadow-lg p-6">
          <div className="flex items-start justify-between mb-6">
            <div>
              <p className="text-gray-600 flex items-center gap-2">
                <Info className="h-5 w-5 text-teal-600" />
                Hover over states to see detailed information
              </p>
            </div>
            <div className="flex flex-col gap-2">
              <div className="flex items-center gap-2">
                <div className="w-4 h-4 bg-[#1b4d3e]"></div>
                <span className="text-sm text-gray-600">
                  &gt; 200 facilities
                </span>
              </div>
              <div className="flex items-center gap-2">
                <div className="w-4 h-4 bg-[#2d7a64]"></div>
                <span className="text-sm text-gray-600">
                  151-200 facilities
                </span>
              </div>
              <div className="flex items-center gap-2">
                <div className="w-4 h-4 bg-[#3c9d82]"></div>
                <span className="text-sm text-gray-600">
                  101-150 facilities
                </span>
              </div>
              <div className="flex items-center gap-2">
                <div className="w-4 h-4 bg-[#7fb9aa]"></div>
                <span className="text-sm text-gray-600">
                  &lt; 100 facilities
                </span>
              </div>
            </div>
          </div>
          <NigeriaMap />
        </div>
      </main>
    </div>
  );
}

export default OverviewMap;

// import { GoogleMap, LoadScript, Polygon } from "@react-google-maps/api";
// import nigeriaStatePolygon from "../../../public/data/nigeria-states.json";

// const containerStyle = {
//   width: "100%",
//   height: "500px",
// };

// const center = {
//   lat: 9.082, // Approximate center of Nigeria
//   lng: 8.6753,
// };

// // Sample GeoJSON coordinates for a single state (Replace with full Nigeria GeoJSON)
// const nigeriaStatePolygon88 = [
//   { lat: 6.5244, lng: 3.3792 }, // Lagos
//   { lat: 6.6, lng: 3.5 },
//   { lat: 6.4, lng: 3.3 },
// ];

// const polygonOptions = {
//   fillColor: "#2D5F5D",
//   fillOpacity: 0.6,
//   strokeColor: "#fff",
//   strokeWeight: 1,
// };

// const bounds = {
//   north: 13.8856, // Northernmost point
//   south: 4.2771, // Southernmost point
//   west: 2.6769, // Westernmost point
//   east: 14.678, // Easternmost point
// };

// const NigeriaMap: React.FC = () => {
//   return (
//     <LoadScript googleMapsApiKey={process.env.NEXT_PUBLIC_GOOGLE_MAP_API ?? ""}>
//       <GoogleMap
//         mapContainerStyle={containerStyle}
//         center={center}
//         zoom={6}
//         options={{
//           restriction: {
//             latLngBounds: bounds, // Restrict view to Nigeria
//             strictBounds: true, // Prevent moving outside
//           },
//           streetViewControl: false, // Hide Street View
//           mapTypeControl: false, // Hide Map Type Control
//         }}
//       >
//         {nigeriaStatePolygon.features.map((state, index) => (
//           <Polygon
//             key={index}
//             paths={state.geometry.coordinates[0].map((coord) => ({
//               lat: coord[1],
//               lng: coord[0],
//             }))}
//             options={polygonOptions}
//           />
//         ))}
//       </GoogleMap>
//     </LoadScript>
//   );
// };
