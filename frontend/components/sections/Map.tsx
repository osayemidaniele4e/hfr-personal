"use client";

import React, { useState, useCallback } from "react";
import {
  LoadScript,
  GoogleMap,
  Marker,
  InfoWindow,
} from "@react-google-maps/api";
const libraries: ("geometry" | "places")[] = ["places"];
import dynamic from "next/dynamic";
import { List, MapPin, Filter } from "lucide-react";
import Input from "../ui/Input";
import { Button, Card } from "@chakra-ui/react";
import Image from "next/image";
import SelectComponent from "../ui/SelectComponent";
import { GreenButton, Text, WhiteButton } from "../ui/Typography";
import { HiOutlineBars2 } from "react-icons/hi2";
import { LuLayoutDashboard } from "react-icons/lu";
import { CiSliderHorizontal } from "react-icons/ci";
import { MdLocationPin } from "react-icons/md";
import { IoCopy } from "react-icons/io5";
import { useRouter } from "next/navigation";

const facilityData = [
  {
    image: "gh1.svg",
    badge: "24",
  },
  {
    image: "gh2.svg",
    badge: "12",
  },
];

interface Hospital {
  id: number;
  name: string;
  address: string;
  lat: number;
  lng: number;
  opening_hours?: string;
  contact?: string;
}

const Facility = () => {
  const [view, setView] = useState("list");
  const { push } = useRouter();
  const id = 1;

  const [map, setMap] = useState<google.maps.Map | null>(null);
  const [location, setLocation] = useState("");
  const [hospitals, setHospitals] = useState<Hospital[]>([]);
  const [selectedHospital, setSelectedHospital] = useState<Hospital | null>(
    null
  );
  const [center, setCenter] = useState({ lat: 37.7749, lng: -122.4194 }); // Default to SF

  const geocodeLocation = async (address: string) => {
    try {
      const geocoder = new window.google.maps.Geocoder();
      const response = await geocoder.geocode({ address });

      if (response.results[0]) {
        const location = response.results[0].geometry.location;
        setCenter({ lat: location.lat(), lng: location.lng() });
        return location;
      }
    } catch (error) {
      console.error("Geocode error:", error);
    }
    return null;
  };

  const searchHospitals = async () => {
    if (!location) return;

    const geocoded = await geocodeLocation(location);
    if (!geocoded) return;

    const service = new window.google.maps.places.PlacesService(map!);
    service.nearbySearch(
      {
        location: geocoded,
        radius: 5000,
        type: "hospital",
      },
      (results, status) => {
        if (status === "OK" && results) {
          const hospitals = results.map((place, index) => ({
            id: index,
            name: place.name || "Hospital",
            address: place.vicinity || "",
            lat: place.geometry?.location?.lat() || 0,
            lng: place.geometry?.location?.lng() || 0,
          }));
          setHospitals(hospitals);
        }
      }
    );
  };

  const mapContainerStyle = {
    width: "100%",
    height: "500px",
  };

  return (
    <div>
      <div className="min-h-screen m-0 ">
        <header className=" mb-6 p-4">
          <h1 className="text-2xl font-bold mb-4 mt-[-6.5rem] lg:mt-[0]">
            Facility Finder
          </h1>
          <p className="text-sm text-gray-600">
            Porem ipsum dolor sit amet, consectetur adipiscing elit. Nunc
            vulputate libero et velit interdum, ac aliquet odio mattis.
          </p>
        </header>

        <div className="bg-white  rounded-lg">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-2 lg:flex p-4 lg:gap-4 lg:flex-row mb-4 justify-items-start items-start ">
            <Input
              placeholder="Input your location"
              className="w-full md:w-[400px] md:mt-[-.18rem]"
            />
            <SelectComponent className="w-full md:w-[250px]" />
            <SelectComponent className="w-full md:w-[250px]" />
            <GreenButton className=" text-white text-sm p-3">
              Search location
            </GreenButton>
            <WhiteButton className="flex text-sm items-center gap-2 md:h-[50px]">
              <CiSliderHorizontal size={24} /> More filters
            </WhiteButton>
          </div>

          <div className="text-gray-600 bg-[#E8F0E2] text-sm mb-4 w-full p-4 flex md:justify-between items-center">
            <Text className="w-[250px] md:w-full">
              174 healthcare facilities found in your area
            </Text>

            <div className="flex">
              <HiOutlineBars2
                fontSize={34}
                className="bg-[#363636] p-2 rounded-lg"
                color="#fff"
              />
              <LuLayoutDashboard
                fontSize={34}
                className="bg-white rounded-lg p-2 ml-[-.3rem]"
                color="#363636"
              />
            </div>
          </div>

          <div className="flex flex-col lg:flex-row gap-[1rem] md:justify-center md:items-center lg:items-start">
            <div
              className={`flex-1 ${view === "map" ? "hidden md:block" : ""}`}
            >
              {facilityData.map((item, i) => (
                <Card key={i} className="mb-4 p-8 bg-[#F4F4F4]">
                  <div className="flex gap-4 items-start">
                    <div className="">
                      <Image src={item.image} width={182} height={224} alt="" />
                    </div>
                    <div className="flex flex-col gap-[.3rem]">
                      <div className="flex md:justify-between items-center">
                        <h2 className="text-lg font-semibold">
                          General Hospital
                        </h2>
                        <Text
                          className={`${
                            item.badge === "12"
                              ? "bg-[#ECBB7C] rounded-full p-2 text-white text-sm md:text-md"
                              : "bg-[#7CB5EC] rounded-full p-2 text-white text-sm md:text-md"
                          }`}
                        >
                          Open {item.badge}hours
                        </Text>
                      </div>
                      <p className="text-sm text-gray-600">
                        2417 Central Ave, Alameda, CA, 94501{" "}
                        <IoCopy
                          fontSize={16}
                          className="inline cursor-pointer"
                        />
                      </p>
                      <p className="text-sm text-gray-600">
                        Contact info: +23481010101010
                      </p>
                      <p className="text-sm text-gray-600">
                        Plans accepted: Exclusive Provider Organization (EPO),
                        HMO, Medi-Cal Managed Care, Point-of-Service Plan (POS),
                        Senior Advantage
                      </p>
                      <div className="flex gap-[.5rem]">
                        <Text className="underline text-[#5BBA62] cursor-pointer">
                          <MdLocationPin
                            fontSize={24}
                            color="#5BBA62"
                            className="inline"
                          />{" "}
                          View direction
                        </Text>
                        <Text
                          className="underline text-[#5BBA62] cursor-pointer"
                          onClick={() => push(`/facilityfinder/details/${id}`)}
                        >
                          View more details
                        </Text>
                      </div>
                    </div>
                  </div>
                </Card>
              ))}
            </div>

            <div className="w-full md:w-[500px] mr-3 h-[500px] rounded-lg">
              <LoadScript
                googleMapsApiKey={process.env.NEXT_PUBLIC_GOOGLE_MAPS_API_KEY!}
                libraries={libraries}
              >
                <div className="flex flex-col lg:flex-row gap-[1rem] md:justify-center md:items-center lg:items-start">
                  <div className="flex-1">
                    {/* Existing list view */}
                    {hospitals.map((hospital) => (
                      <Card key={hospital.id} className="mb-4 p-8 bg-[#F4F4F4]">
                        {/* Update card content with hospital data */}
                        <h2 className="text-lg font-semibold">
                          {hospital.name}
                        </h2>
                        <p className="text-sm text-gray-600">
                          {hospital.address}
                        </p>
                      </Card>
                    ))}
                  </div>

                  <div className="w-full md:w-[500px] mr-3 h-[500px] rounded-lg">
                    <GoogleMap
                      mapContainerStyle={mapContainerStyle}
                      zoom={14}
                      center={center}
                      onLoad={(map) => setMap(map)}
                    >
                      {hospitals.map((hospital) => (
                        <Marker
                          key={hospital.id}
                          position={{ lat: hospital.lat, lng: hospital.lng }}
                          onClick={() => setSelectedHospital(hospital)}
                        />
                      ))}

                      {selectedHospital && (
                        <InfoWindow
                          position={{
                            lat: selectedHospital.lat,
                            lng: selectedHospital.lng,
                          }}
                          onCloseClick={() => setSelectedHospital(null)}
                        >
                          <div>
                            <h3 className="font-bold">
                              {selectedHospital.name}
                            </h3>
                            <p>{selectedHospital.address}</p>
                          </div>
                        </InfoWindow>
                      )}
                    </GoogleMap>
                  </div>
                </div>
              </LoadScript>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Facility;