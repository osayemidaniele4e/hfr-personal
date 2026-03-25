"use client";

import React, { useState } from "react";
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

const Finder = () => {
  const [view, setView] = useState("list");

  return (
    <div className="min-h-screen pt-16 md:pt-32 m-0 ">
      <header className=" mb-6 p-4">
        <h1 className="text-2xl font-bold mb-4">Facility Finder</h1>
        <p className="text-sm text-gray-600">
          Easily locate health facilities by name, location, type, or services
          using dynamic filters for precise results.
        </p>
      </header>

      <div className="bg-white  rounded-lg">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-2 lg:flex p-4 lg:gap-4 lg:flex-row mb-4 justify-items-start items-start ">
          <Input
            placeholder="Input your location"
            className="w-full md:w-[500px] md:mt-[-.18rem]"
          />
          <SelectComponent className="w-full md:w-[300px]" />
          <SelectComponent className="w-full md:w-[300px]" />
          <GreenButton className=" text-white p-3">Search location</GreenButton>
          <WhiteButton className="flex items-center gap-2 md:h-[50px]">
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

        <div className="flex flex-col md:flex-row items-start">
          <div className={`flex-1 ${view === "map" ? "hidden md:block" : ""}`}>
            {[...Array(2)].map((_, i) => (
              <Card key={i} className="mb-4 p-8">
                <div className="flex gap-4">
                  <div className="">
                    <Image src="/gh1.svg" width={182} height={224} alt="" />
                  </div>
                  <div className="flex flex-col gap-[1rem]">
                    <h2 className="text-lg font-semibold">General Hospital</h2>
                    <p className="text-sm text-gray-600">
                      2417 Central Ave, Alameda, CA, 94501
                    </p>
                    <p className="text-sm text-gray-600">
                      Contact info: +23481010101010
                    </p>
                    <p className="text-sm text-gray-600">
                      Plans accepted: Exclusive Provider Organization (EPO),
                      HMO, Medi-Cal Managed Care, Point-of-Service Plan (POS),
                      Senior Advantage
                    </p>
                    <a href="#" className="text-green-600 font-semibold">
                      View direction
                    </a>
                  </div>
                </div>
              </Card>
            ))}
          </div>

          <div
            className={`w-full md:w-1/2 md:ml-4 ${
              view === "list" ? "hidden md:block" : ""
            }`}
          >
            <Image
              src="/map.svg"
              width={756}
              height={838}
              alt="map"
              className=""
            />
            {/* <MapContainer
              center={[9.0765, 7.3986]}
              zoom={13}
              className="w-full h-96 rounded-lg"
            >
              <TileLayer
                url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                attribution="&copy; <a href='http://osm.org/copyright'>OpenStreetMap</a> contributors"
              />
            </MapContainer> */}
          </div>
        </div>

        {/* <div className="flex justify-end mt-4">
          <Button
            variant="outline"
            className="flex items-center gap-2"
            onClick={() => setView(view === "list" ? "map" : "list")}
          >
            {view === "list" ? <MapPin size={16} /> : <List size={16} />}{" "}
            {view === "list" ? "Map view" : "List view"}
          </Button>
        </div> */}
      </div>
    </div>
  );
};

export default Finder;
