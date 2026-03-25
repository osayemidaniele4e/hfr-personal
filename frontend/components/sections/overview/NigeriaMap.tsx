import {
  GoogleMap,
  InfoWindow,
  LoadScript,
  Polygon,
  Marker,
} from "@react-google-maps/api";
import nigeriaStatePolygon from "../../../public/data/nigeria-states.json";
import { useEffect, useState } from "react";

const polygonOptions = {
  fillColor: "#2D5F5D",
  fillOpacity: 0.6,
  strokeColor: "#fff",
  strokeWeight: 1,
};

const containerStyle = {
  width: "100%",
  height: "500px",
};

const center = {
  lat: 9.082, // Nigeria's center latitude
  lng: 8.6753, // Nigeria's center longitude
};

const bounds = {
  north: 14,
  south: 4,
  west: 2.5,
  east: 15.5,
};

const getPolygonOptions = (stateName: string) => ({
  fillColor: stateName === "Kaduna" ? "#A6CE39" : "#4A7D8C",
  fillOpacity: 0.7,
  strokeColor: "#ffffff",
  strokeWeight: 1.5,
});

const NigeriaMap: React.FC = () => {
  const [mapsLoaded, setMapsLoaded] = useState(false);

  useEffect(() => {
    if (window.google?.maps) setMapsLoaded(true);
  }, []);

  return (
    <LoadScript
      googleMapsApiKey={process.env.NEXT_PUBLIC_GOOGLE_MAP_API ?? ""}
      onLoad={() => setMapsLoaded(true)}
    >
      <GoogleMap
        mapContainerStyle={containerStyle}
        center={center}
        zoom={6}
        options={{
          restriction: { latLngBounds: bounds, strictBounds: true },
          streetViewControl: false,
          mapTypeControl: false,
        }}
      >
        {nigeriaStatePolygon.features.map((state, index) => {
          const stateName = state.properties.name;

          const stateCenter = {
            lat:
              Array.isArray(state.geometry.coordinates[0][0]) &&
              typeof state.geometry.coordinates[0][0][1] === "number"
                ? state.geometry.coordinates[0][0][1]
                : 0,
            lng:
              Array.isArray(state.geometry.coordinates[0][0]) &&
              typeof state.geometry.coordinates[0][0][0] === "number"
                ? state.geometry.coordinates[0][0][0]
                : 0,
          };

          const polygonPaths = state.geometry.coordinates.flatMap((polygon) =>
            polygon.map((ring) =>
              ring
                .filter(
                  (coord): coord is [number, number] =>
                    Array.isArray(coord) &&
                    coord.length === 2 &&
                    typeof coord[0] === "number" &&
                    typeof coord[1] === "number"
                )
                .map(([lng, lat]) => ({ lat, lng }))
            )
          );

          return (
            <div key={index}>
              <Polygon paths={polygonPaths} options={getPolygonOptions(stateName)} />

              {mapsLoaded && (
                <Marker
                  position={stateCenter}
                  label={{
                    text: stateName,
                    fontSize: "12px",
                    color: "black",
                  }}
                  icon={{
                    path: window.google.maps.SymbolPath.CIRCLE,
                    scale: 0,
                  }}
                />
              )}
            </div>
          );
        })}
      </GoogleMap>
    </LoadScript>
  );
};

export default NigeriaMap;
